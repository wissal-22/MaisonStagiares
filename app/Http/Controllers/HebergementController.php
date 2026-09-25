<?php

namespace App\Http\Controllers;

use App\Models\Batiment;
use App\Models\Chambre;
use App\Models\Dossier;
use App\Models\Internat;
use App\Models\Lit;
use App\Models\Mouvement;
use App\Services\AllocationChambreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HebergementController extends Controller
{
    /**
     * Plan physique de l'hébergement : Bâtiments, Chambres et Lits.
     */
    public function index(Request $request): View
    {
        $internats = Internat::all();
        $user = Auth::user();
        $selectedInternatId = $request->get('internat_id', $user->internat_id ?? $internats->first()?->id);

        $internat = Internat::with([
            'batiments.chambres.lits.dossierActif.candidat.candidatProfile',
        ])->find($selectedInternatId);

        // Candidats admis en attente d'affectation de lit
        $admisSansLit = Dossier::with('candidat.candidatProfile')
            ->where('internat_id', $selectedInternatId)
            ->where('statut', 'admis')
            ->whereNull('lit_id')
            ->get();

        // Tous les lits libres pour affectation manuelle
        $litsLibres = Lit::where('statut', 'libre')
            ->whereHas('chambre.batiment', function ($q) use ($selectedInternatId) {
                $q->where('internat_id', $selectedInternatId);
            })
            ->with('chambre.batiment')
            ->get();

        return view('hebergement.index', compact('internats', 'internat', 'selectedInternatId', 'admisSansLit', 'litsLibres'));
    }

    /**
     * Affectation automatique intelligente des places.
     */
    public function autoAffecter(Request $request, AllocationChambreService $allocationService): RedirectResponse
    {
        $validated = $request->validate([
            'internat_id' => 'required|exists:internats,id',
        ]);

        $res = $allocationService->autoAffecterAdmis((int) $validated['internat_id'], Auth::id());

        return back()->with('success', "Affectation automatique terminée ! {$res['affectes']} stagiaire(s) affecté(s) avec succès. Non-affectés (faute de places du bon genre/type) : {$res['echecs']}.");
    }

    /**
     * Affectation manuelle d'un lit à un dossier admis.
     */
    public function affecterLit(Request $request, AllocationChambreService $allocationService): RedirectResponse
    {
        $validated = $request->validate([
            'dossier_id' => 'required|exists:dossiers,id',
            'lit_id' => 'required|exists:lits,id',
        ]);

        $dossier = Dossier::findOrFail($validated['dossier_id']);
        $lit = Lit::findOrFail($validated['lit_id']);

        if ($allocationService->affecterLit($dossier, $lit, Auth::id())) {
            return back()->with('success', "Le lit {$lit->code} a été attribué avec succès au stagiaire {$dossier->candidat->name}.");
        }

        return back()->with('error', "Ce lit n'est plus disponible.");
    }

    /**
     * Libération d'un lit (départ / check-out).
     */
    public function libererLit(int $id): RedirectResponse
    {
        $lit = Lit::with('dossierActif')->findOrFail($id);
        $dossier = $lit->dossierActif;

        if ($dossier) {
            $dossier->lit_id = null;
            $dossier->date_depart_effective = now();
            $dossier->save();

            Mouvement::create([
                'dossier_id' => $dossier->id,
                'internat_id' => $dossier->internat_id,
                'type' => 'depart',
                'date_mouvement' => now(),
                'ancien_lit_id' => $lit->id,
                'motif' => 'Départ officiel / Clôture du séjour en internat',
                'enregistre_par_user_id' => Auth::id(),
            ]);
        }

        $lit->statut = 'libre';
        $lit->save();

        return back()->with('success', "Le lit {$lit->code} a été libéré et remis en disponibilité.");
    }

    /**
     * Journal des mouvements des stagiaires.
     */
    public function mouvements(Request $request): View
    {
        $query = Mouvement::with([
            'dossier.candidat.candidatProfile',
            'internat',
            'ancienLit.chambre',
            'nouveauLit.chambre',
            'enregistrePar',
        ]);

        if ($request->filled('internat_id')) {
            $query->where('internat_id', $request->internat_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $mouvements = $query->latest('date_mouvement')->paginate(20)->withQueryString();
        $internats = Internat::all();

        return view('hebergement.mouvements', compact('mouvements', 'internats'));
    }

    /**
     * CRUD Chambres (diagramme de cas d'utilisation).
     */
    public function chambres(Request $request): View
    {
        $internats = Internat::with('batiments')->get();
        $user = Auth::user();
        $selectedInternatId = $request->get('internat_id', $user->internat_id ?? $internats->first()?->id);

        $query = Chambre::with(['batiment.internat', 'lits']);

        if ($selectedInternatId) {
            $query->whereHas('batiment', fn ($q) => $q->where('internat_id', $selectedInternatId));
        }

        $chambres = $query->orderBy('numero')->paginate(15)->withQueryString();
        $batiments = Batiment::where('internat_id', $selectedInternatId)->get();

        return view('hebergement.chambres', compact('chambres', 'internats', 'batiments', 'selectedInternatId'));
    }

    /**
     * Création d'une nouvelle chambre avec génération de ses lits.
     */
    public function storeChambre(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'batiment_id' => 'required|exists:batiments,id',
            'numero' => 'required|string|max:50',
            'etage' => 'required|integer|min:0',
            'capacite' => 'required|integer|min:1|max:6',
            'type' => 'required|in:standard,pmr,individuelle',
            'equipements' => 'nullable|string|max:500',
        ]);

        $chambre = Chambre::create([
            'batiment_id' => $validated['batiment_id'],
            'numero' => $validated['numero'],
            'etage' => $validated['etage'],
            'capacite' => $validated['capacite'],
            'type' => $validated['type'],
            'statut' => 'disponible',
            'equipements' => $validated['equipements'] ?? 'Lits simples, armoires, bureaux, sanitaires',
        ]);

        $bat = Batiment::find($validated['batiment_id']);
        for ($i = 1; $i <= $validated['capacite']; $i++) {
            Lit::create([
                'chambre_id' => $chambre->id,
                'code' => "{$bat->code}-{$chambre->numero}-L{$i}",
                'position' => ($i % 2 === 1) ? 'bas' : 'haut',
                'statut' => 'libre',
            ]);
        }

        return back()->with('success', "La chambre {$chambre->numero} et ses {$validated['capacite']} lits ont été créés avec succès !");
    }

    /**
     * Fiche d'hébergement et registre de présence imprimable (F3.5).
     */
    public function ficheChambre(int $id): View
    {
        $chambre = Chambre::with([
            'batiment.internat.etablissement',
            'lits.dossierActif.candidat.candidatProfile.filiere',
        ])->findOrFail($id);

        return view('hebergement.fiche-chambre', compact('chambre'));
    }
}
