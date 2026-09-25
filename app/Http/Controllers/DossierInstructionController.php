<?php

namespace App\Http\Controllers;

use App\Models\Campagne;
use App\Models\DocType;
use App\Models\Dossier;
use App\Models\DossierDoc;
use App\Models\Internat;
use App\Models\Niveau;
use App\Services\SelectionScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DossierInstructionController extends Controller
{
    /**
     * Liste des dossiers de préinscription avec filtres.
     */
    public function index(Request $request): View
    {
        $query = Dossier::with([
            'candidat.candidatProfile.niveau',
            'candidat.candidatProfile.filiere',
            'internat.etablissement',
            'campagne',
            'docs',
        ]);

        // Filtrage par internat (si gestionnaire, restreint à son internat)
        $user = Auth::user();
        if ($user->isGestionnaire() && $user->internat_id) {
            $query->where('internat_id', $user->internat_id);
        } elseif ($request->filled('internat_id')) {
            $query->where('internat_id', $request->internat_id);
        }

        // Filtrage par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtrage par niveau
        if ($request->filled('niveau_id')) {
            $query->whereHas('candidat.candidatProfile', function ($q) use ($request) {
                $q->where('niveau_id', $request->niveau_id);
            });
        }

        // Recherche par nom, CIN, CEF ou numéro de dossier
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('numero_dossier', 'like', "%{$s}%")
                    ->orWhereHas('candidat', function ($sub) use ($s) {
                        $sub->where('name', 'like', "%{$s}%")
                            ->orWhere('email', 'like', "%{$s}%");
                    })
                    ->orWhereHas('candidat.candidatProfile', function ($sub) use ($s) {
                        $sub->where('cin', 'like', "%{$s}%")
                            ->orWhere('cef', 'like', "%{$s}%");
                    });
            });
        }

        $dossiers = $query->latest('date_depot')->paginate(15)->withQueryString();

        $internats = Internat::all();
        $niveaux = Niveau::all();
        $campagnes = Campagne::all();

        $statutsCount = [
            'total' => Dossier::count(),
            'soumis' => Dossier::where('statut', 'soumis')->count(),
            'complet' => Dossier::where('statut', 'complet')->count(),
            'incomplet' => Dossier::where('statut', 'incomplet')->count(),
            'en_commission' => Dossier::where('statut', 'en_commission')->count(),
            'admis' => Dossier::where('statut', 'admis')->count(),
            'liste_attente' => Dossier::where('statut', 'liste_attente')->count(),
            'refuse' => Dossier::where('statut', 'refuse')->count(),
        ];

        return view('dossiers.index', compact('dossiers', 'internats', 'niveaux', 'campagnes', 'statutsCount'));
    }

    /**
     * Vue détaillée et instruction d'un dossier.
     */
    public function show(int $id): View
    {
        $dossier = Dossier::with([
            'candidat.candidatProfile.niveau',
            'candidat.candidatProfile.filiere',
            'internat.etablissement.region',
            'campagne.anneeScolaire',
            'lit.chambre.batiment',
            'docs.docType',
            'mouvements',
            'validePar',
        ])->findOrFail($id);

        return view('dossiers.show', compact('dossier'));
    }

    /**
     * Validation ou rejet d'une pièce justificative unitaire.
     */
    public function validerPiece(Request $request, int $dossierId, int $docId): RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:valider,rejeter',
            'admin_message' => 'nullable|string|max:500',
        ]);

        $dossier = Dossier::findOrFail($dossierId);
        $doc = DossierDoc::where('dossier_id', $dossier->id)->findOrFail($docId);

        $doc->is_valid = ($validated['action'] === 'valider');
        $doc->admin_message = $validated['admin_message'];
        $doc->verified_by_user_id = Auth::id();
        $doc->verified_at = now();
        $doc->save();

        // Vérifier la complétude globale du dossier
        $this->actualiserCompletudeDossier($dossier);

        $message = $doc->is_valid
            ? "La pièce « {$doc->docType->nom} » a été validée."
            : "La pièce « {$doc->docType->nom} » a été rejetée avec motif transmis au candidat.";

        return back()->with('success', $message);
    }

    /**
     * Changement manuel de statut ou de décision d'instruction.
     */
    public function changerStatut(Request $request, int $id, SelectionScoringService $scoringService): RedirectResponse
    {
        $validated = $request->validate([
            'statut' => 'required|in:soumis,complet,incomplet,en_commission,admis,liste_attente,refuse,exclu',
            'motif_decision' => 'nullable|string|max:1000',
        ]);

        $dossier = Dossier::findOrFail($id);
        $dossier->statut = $validated['statut'];
        if ($validated['motif_decision']) {
            $dossier->motif_decision = $validated['motif_decision'];
        }

        if (in_array($validated['statut'], ['complet', 'en_commission', 'admis'])) {
            $dossier->is_valide = true;
        }

        // Recalculer le score
        $scoreResult = $scoringService->calculateScore($dossier);
        $dossier->score_total = $scoreResult['total'];
        $dossier->details_score = $scoreResult['details'];
        $dossier->save();

        return back()->with('success', "Le statut du dossier {$dossier->numero_dossier} a été mis à jour vers [{$dossier->statut_label}].");
    }

    /**
     * Recalcule automatiquement le statut complet/incomplet du dossier.
     */
    private function actualiserCompletudeDossier(Dossier $dossier): void
    {
        $docs = $dossier->docs()->with('docType')->get();
        $hasRejet = $docs->contains('is_valid', false);

        if ($hasRejet) {
            $dossier->statut = 'incomplet';
            $dossier->is_valide = false;
        } else {
            $obligatoiresValides = $docs->filter(function ($d) {
                return $d->docType->est_obligatoire && $d->is_valid === true;
            })->count();

            $totalObligatoires = DocType::where('est_obligatoire', true)->count();

            if ($obligatoiresValides >= $totalObligatoires) {
                $dossier->statut = 'complet';
                $dossier->is_valide = true;
            }
        }

        $dossier->save();
    }
}
