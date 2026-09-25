<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use App\Models\Campagne;
use App\Models\CritereSelection;
use App\Models\Filiere;
use App\Models\Internat;
use App\Models\Niveau;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReferentielController extends Controller
{
    /**
     * Vue d'ensemble des référentiels et paramétrages.
     */
    public function index(Request $request): View
    {
        $activeTab = $request->get('tab', 'campagnes');

        $campagnes = Campagne::with(['anneeScolaire', 'internat'])->latest()->get();
        $annees = AnneeScolaire::latest()->get();
        $niveaux = Niveau::with('filieres')->get();
        $filieres = Filiere::with('niveau')->get();
        $criteres = CritereSelection::with('internat')->get();
        $internats = Internat::all();

        return view('referentiels.index', compact(
            'activeTab',
            'campagnes',
            'annees',
            'niveaux',
            'filieres',
            'criteres',
            'internats'
        ));
    }

    /**
     * Création d'une campagne de préinscription (Diagramme de cas d'utilisation).
     */
    public function storeCampagne(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'annee_scolaire_id' => 'required|exists:annee_scolaires,id',
            'internat_id' => 'nullable|exists:internats,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'quota_places' => 'required|integer|min:1',
            'statut' => 'required|in:brouillon,ouverte,cloturee,archivee',
            'instructions' => 'nullable|string',
        ]);

        Campagne::create($validated);

        return redirect()->route('referentiels.index', ['tab' => 'campagnes'])
            ->with('success', 'Campagne de préinscription créée avec succès !');
    }

    /**
     * Mise à jour du statut d'une campagne.
     */
    public function updateCampagne(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'statut' => 'required|in:brouillon,ouverte,cloturee,deliberation_terminee,archivee',
            'quota_places' => 'required|integer|min:1',
        ]);

        $campagne = Campagne::findOrFail($id);
        $campagne->update($validated);

        return redirect()->route('referentiels.index', ['tab' => 'campagnes'])
            ->with('success', "La campagne « {$campagne->titre} » a été mise à jour.");
    }

    /**
     * Ajouter une année scolaire (Diagramme de cas d'utilisation).
     */
    public function storeAnneeScolaire(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:annee_scolaires,code',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'est_active' => 'nullable|boolean',
        ]);

        if ($request->boolean('est_active')) {
            AnneeScolaire::where('est_active', true)->update(['est_active' => false]);
        }

        AnneeScolaire::create([
            'code' => $validated['code'],
            'date_debut' => $validated['date_debut'],
            'date_fin' => $validated['date_fin'],
            'est_active' => $request->boolean('est_active'),
        ]);

        return redirect()->route('referentiels.index', ['tab' => 'campagnes'])
            ->with('success', "L'année scolaire {$validated['code']} a été ajoutée avec succès !");
    }

    /**
     * CRUD Niveau (Diagramme de cas d'utilisation).
     */
    public function storeNiveau(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:niveaux,code',
            'nom' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        Niveau::create($validated);

        return redirect()->route('referentiels.index', ['tab' => 'niveaux'])
            ->with('success', "Le niveau de formation {$validated['nom']} a été ajouté.");
    }

    /**
     * CRUD Filières (Diagramme de cas d'utilisation).
     */
    public function storeFiliere(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'niveau_id' => 'required|exists:niveaux,id',
            'code' => 'required|string|max:30|unique:filieres,code',
            'nom' => 'required|string|max:150',
            'description' => 'nullable|string',
        ]);

        Filiere::create($validated);

        return redirect()->route('referentiels.index', ['tab' => 'filieres'])
            ->with('success', "La filière {$validated['nom']} a été créée avec succès !");
    }

    /**
     * Mise à jour de la pondération d'un critère de sélection.
     */
    public function updateCritere(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'points_base' => 'required|integer|min:0|max:100',
            'poids' => 'required|numeric|min:0.1|max:5.0',
            'est_actif' => 'nullable|boolean',
        ]);

        $critere = CritereSelection::findOrFail($id);
        $critere->points_base = $validated['points_base'];
        $critere->poids = $validated['poids'];
        $critere->est_actif = $request->boolean('est_actif');
        $critere->save();

        return redirect()->route('referentiels.index', ['tab' => 'criteres'])
            ->with('success', "Critère « {$critere->nom} » mis à jour avec un barème de {$critere->points_base} pts (Coefficient x{$critere->poids}).");
    }
}
