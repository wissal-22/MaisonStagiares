<?php

namespace App\Http\Controllers;

use App\Models\Campagne;
use App\Models\CritereSelection;
use App\Models\Dossier;
use App\Models\Internat;
use App\Services\SelectionScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DeliberationController extends Controller
{
    /**
     * Grille de délibération de la commission de sélection.
     */
    public function index(Request $request, SelectionScoringService $scoringService): View
    {
        $campagnes = Campagne::all();
        $selectedCampagneId = $request->get('campagne_id', $campagnes->first()?->id);
        $campagne = Campagne::with('anneeScolaire')->find($selectedCampagneId);

        $internats = Internat::all();
        $user = Auth::user();
        $selectedInternatId = $request->get('internat_id', $user->internat_id ?? $internats->first()?->id);

        $query = Dossier::with([
            'candidat.candidatProfile.niveau',
            'candidat.candidatProfile.filiere',
            'internat.etablissement',
            'lit',
        ]);

        if ($selectedCampagneId) {
            $query->where('campagne_id', $selectedCampagneId);
        }

        if ($selectedInternatId) {
            $query->where('internat_id', $selectedInternatId);
        }

        // Classement par score décroissant
        $dossiers = $query->orderByDesc('score_total')->get();

        // Récupérer les critères de sélection actifs
        $criteres = CritereSelection::where('est_actif', true)->get();

        // Statistiques de la délibération
        $stats = [
            'total' => $dossiers->count(),
            'admis' => $dossiers->where('statut', 'admis')->count(),
            'liste_attente' => $dossiers->where('statut', 'liste_attente')->count(),
            'refuses' => $dossiers->where('statut', 'refuse')->count(),
            'en_attente_deliberation' => $dossiers->whereIn('statut', ['soumis', 'complet', 'en_commission'])->count(),
            'tous_valides_directeur' => $dossiers->where('statut', 'admis')->every(fn ($d) => $d->valide_par_admin),
        ];

        return view('deliberation.index', compact(
            'campagnes',
            'campagne',
            'internats',
            'selectedInternatId',
            'dossiers',
            'criteres',
            'stats'
        ));
    }

    /**
     * Lancement automatique de l'algorithme de délibération et d'admission.
     */
    public function lancerDeliberation(Request $request, SelectionScoringService $scoringService): RedirectResponse
    {
        $validated = $request->validate([
            'campagne_id' => 'required|exists:campagnes,id',
            'internat_id' => 'nullable|exists:internats,id',
            'places_disponibles' => 'required|integer|min:1',
        ]);

        $resultats = $scoringService->deliberer(
            (int) $validated['campagne_id'],
            $validated['internat_id'] ? (int) $validated['internat_id'] : null,
            (int) $validated['places_disponibles']
        );

        return back()->with('success', "Commission de délibération exécutée avec succès ! Traités : {$resultats['total_traites']}, Admis : {$resultats['admis']}, Liste d'attente : {$resultats['liste_attente']}, Refusés : {$resultats['refuses']}.");
    }

    /**
     * Validation hiérarchique officielle par le Directeur d'Établissement.
     */
    public function validerParDirecteur(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'campagne_id' => 'required|exists:campagnes,id',
            'internat_id' => 'nullable|exists:internats,id',
        ]);

        $query = Dossier::where('campagne_id', $validated['campagne_id'])
            ->where('statut', 'admis');

        if (! empty($validated['internat_id'])) {
            $query->where('internat_id', $validated['internat_id']);
        }

        $count = $query->update([
            'valide_par_admin' => true,
            'valide_par_user_id' => Auth::id(),
            'date_validation' => now(),
        ]);

        return back()->with('success', "Décisions d'admission validées officiellement par la Direction ({$count} dossiers admis certifiés). Les résultats sont désormais publiés aux candidats.");
    }

    /**
     * Promotion d'un candidat de la liste d'attente vers la liste des admis (F2.8).
     */
    public function promouvoirAttente(int $id): RedirectResponse
    {
        $dossier = Dossier::findOrFail($id);

        if ($dossier->statut !== 'liste_attente') {
            return back()->with('error', "Ce dossier n'est pas sur liste d'attente.");
        }

        $dossier->statut = 'admis';
        $dossier->motif_decision = "Promu de la liste d'attente suite à désistement ou libération de place le ".now()->format('d/m/Y');
        $dossier->valide_par_admin = true;
        $dossier->valide_par_user_id = Auth::id();
        $dossier->date_validation = now();
        $dossier->save();

        return back()->with('success', "Le candidat {$dossier->candidat->name} a été promu avec succès sur la liste des admis !");
    }
}
