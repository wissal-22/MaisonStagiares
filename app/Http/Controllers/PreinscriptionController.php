<?php

namespace App\Http\Controllers;

use App\Models\Campagne;
use App\Models\CandidatProfile;
use App\Models\DocType;
use App\Models\Dossier;
use App\Models\DossierDoc;
use App\Models\IncidentReclamation;
use App\Models\Internat;
use App\Models\Niveau;
use App\Models\User;
use App\Services\SelectionScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PreinscriptionController extends Controller
{
    /**
     * Espace Stagiaire / Tableau de bord personnel du candidat.
     */
    public function dashboard(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $user->load(['candidatProfile.niveau', 'candidatProfile.filiere']);

        $dossier = Dossier::with([
            'campagne.anneeScolaire',
            'internat.etablissement',
            'lit.chambre.batiment',
            'docs.docType',
        ])
            ->where('candidat_id', $user->id)
            ->latest()
            ->first();

        $campagneActive = Campagne::where('statut', 'ouverte')
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->first();

        $mesReclamations = IncidentReclamation::where('user_id', $user->id)
            ->latest()
            ->get();

        return view('candidat.dashboard', compact('user', 'dossier', 'campagneActive', 'mesReclamations'));
    }

    /**
     * Formulaire de préinscription.
     */
    public function create(): View|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // Vérifier si un dossier actif existe déjà
        $existingDossier = Dossier::where('candidat_id', $user->id)->first();
        if ($existingDossier && ! in_array($existingDossier->statut, ['brouillon', 'incomplet'])) {
            return redirect()->route('candidat.dashboard')
                ->with('info', "Vous avez déjà soumis votre dossier de préinscription n° {$existingDossier->numero_dossier}.");
        }

        // Vérifier campagne ouverte
        $campagne = Campagne::where('statut', 'ouverte')
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->first();

        if (! $campagne) {
            return redirect()->route('candidat.dashboard')
                ->with('error', 'Aucune campagne de préinscription n’est actuellement ouverte aux dépôts.');
        }

        $internats = Internat::with('etablissement')->where('statut', 'actif')->get();
        $niveaux = Niveau::with('filieres')->get();
        $docTypes = DocType::all();
        $profile = $user->candidatProfile;

        return view('candidat.preinscription', compact('user', 'campagne', 'internats', 'niveaux', 'docTypes', 'profile', 'existingDossier'));
    }

    /**
     * Enregistrement de la demande de préinscription et téléversement des pièces.
     */
    public function store(Request $request, SelectionScoringService $scoringService): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // 1. Validation de campagne
        $campagne = Campagne::findOrFail($request->input('campagne_id'));
        if (! $campagne->isOuverte()) {
            return back()->with('error', 'La période de dépôt pour cette campagne est clôturée.');
        }

        // 2. Validation formulaire
        $validated = $request->validate([
            'internat_id' => 'required|exists:internats,id',
            'cin' => 'required|string|max:20',
            'is_mineur' => 'nullable|boolean',
            'cin_tuteur' => 'nullable|string|max:20',
            'nom_tuteur' => 'nullable|string|max:100',
            'cef' => 'required|string|max:30',
            'date_naissance' => 'required|date',
            'genre' => 'required|in:garcon,fille',
            'ville_origine' => 'required|string|max:100',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'distance_km' => 'required|integer|min:0',
            'revenu_mensuel_parents' => 'required|numeric|min:0',
            'nombre_freres_soeurs' => 'required|integer|min:0',
            'est_orphelin' => 'nullable|boolean',
            'en_situation_handicap' => 'nullable|boolean',
            'annee_precedente_heberge' => 'nullable|boolean',
            'niveau_id' => 'required|exists:niveaux,id',
            'filiere_id' => 'required|exists:filieres,id',
            'regime_alimentaire' => 'nullable|string|max:100',
            'contact_urgence_nom' => 'nullable|string|max:100',
            'contact_urgence_telephone' => 'nullable|string|max:20',
        ]);

        // 3. Mise à jour ou création du profil candidat
        $profile = CandidatProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'cin' => strtoupper($validated['cin']),
                'is_mineur' => $request->boolean('is_mineur'),
                'cin_tuteur' => $validated['cin_tuteur'] ? strtoupper($validated['cin_tuteur']) : null,
                'nom_tuteur' => $validated['nom_tuteur'],
                'cef' => $validated['cef'],
                'date_naissance' => $validated['date_naissance'],
                'genre' => $validated['genre'],
                'ville_origine' => $validated['ville_origine'],
                'adresse' => $validated['adresse'],
                'telephone' => $validated['telephone'],
                'distance_km' => $validated['distance_km'],
                'revenu_mensuel_parents' => $validated['revenu_mensuel_parents'],
                'nombre_freres_soeurs' => $validated['nombre_freres_soeurs'],
                'est_orphelin' => $request->boolean('est_orphelin'),
                'en_situation_handicap' => $request->boolean('en_situation_handicap'),
                'annee_precedente_heberge' => $request->boolean('annee_precedente_heberge'),
                'niveau_id' => $validated['niveau_id'],
                'filiere_id' => $validated['filiere_id'],
                'regime_alimentaire' => $validated['regime_alimentaire'] ?? 'Normal',
                'contact_urgence_nom' => $validated['contact_urgence_nom'],
                'contact_urgence_telephone' => $validated['contact_urgence_telephone'],
            ]
        );

        // 4. Création ou mise à jour du dossier
        $numDossier = 'DOS-'.date('Y').'-'.str_pad((string) ($user->id + rand(100, 999)), 6, '0', STR_PAD_LEFT);

        $dossier = Dossier::updateOrCreate(
            ['candidat_id' => $user->id, 'campagne_id' => $campagne->id],
            [
                'numero_dossier' => $numDossier,
                'internat_id' => $validated['internat_id'],
                'date_depot' => now(),
                'statut' => 'soumis',
                'is_valide' => false,
                'valide_par_admin' => false,
            ]
        );

        // 5. Enregistrement des documents téléversés
        $docTypes = DocType::all();
        foreach ($docTypes as $docType) {
            $inputName = "doc_{$docType->code}";
            if ($request->hasFile($inputName)) {
                $file = $request->file($inputName);
                $path = $file->store('documents/dossiers', 'public');

                DossierDoc::updateOrCreate(
                    [
                        'dossier_id' => $dossier->id,
                        'doc_type_id' => $docType->id,
                    ],
                    [
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'is_valid' => null, // en attente de vérification
                        'admin_message' => null,
                    ]
                );
            }
        }

        // 6. Calcul automatique du score initial
        $scoreResult = $scoringService->calculateScore($dossier);
        $dossier->score_total = $scoreResult['total'];
        $dossier->details_score = $scoreResult['details'];
        $dossier->save();

        return redirect()->route('candidat.dashboard')
            ->with('success', "Votre dossier n° {$dossier->numero_dossier} a été déposé avec succès ! Accusé de réception généré.");
    }

    /**
     * Soumission d'une réclamation ou signalement d'incident par le candidat.
     */
    public function storeReclamation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:reclamation,incident_discipline,maintenance_chambre',
            'priorite' => 'required|in:faible,moyenne,haute,urgente',
            'titre' => 'required|string|max:200',
            'description' => 'required|string',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $dossier = Dossier::where('candidat_id', $user->id)->first();

        IncidentReclamation::create([
            'internat_id' => $dossier?->internat_id ?? Internat::first()->id,
            'dossier_id' => $dossier?->id,
            'user_id' => $user->id,
            'type' => $validated['type'],
            'priorite' => $validated['priorite'],
            'titre' => $validated['titre'],
            'description' => $validated['description'],
            'chambre_id' => $dossier?->lit?->chambre_id,
            'statut' => 'ouvert',
        ]);

        return redirect()->route('candidat.dashboard')
            ->with('success', 'Votre réclamation / signalement a été transmis avec succès à l’administration de l’internat.');
    }
}
