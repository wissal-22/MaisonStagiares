<?php

namespace App\Http\Controllers;

use App\Models\Chambre;
use App\Models\IncidentReclamation;
use App\Models\Internat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class IncidentReclamationController extends Controller
{
    /**
     * Liste et gestion des incidents et réclamations.
     */
    public function index(Request $request): View
    {
        $internats = Internat::all();
        $user = Auth::user();
        $selectedInternatId = $request->get('internat_id', $user->internat_id ?? $internats->first()?->id);

        $query = IncidentReclamation::with([
            'internat',
            'user',
            'dossier.candidat',
            'chambre.batiment',
            'traitePar',
        ]);

        if ($selectedInternatId && ! $user->isAdmin()) {
            $query->where('internat_id', $selectedInternatId);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $incidents = $query->latest()->paginate(15)->withQueryString();
        $chambres = Chambre::with('batiment')->whereHas('batiment', fn ($q) => $q->where('internat_id', $selectedInternatId))->get();

        $stats = [
            'total' => IncidentReclamation::count(),
            'ouverts' => IncidentReclamation::where('statut', 'ouvert')->count(),
            'en_cours' => IncidentReclamation::where('statut', 'en_cours')->count(),
            'resolus' => IncidentReclamation::where('statut', 'traite')->count(),
        ];

        return view('incidents.index', compact('incidents', 'internats', 'selectedInternatId', 'chambres', 'stats'));
    }

    /**
     * Enregistrement d'un nouvel incident ou demande de maintenance.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'internat_id' => 'required|exists:internats,id',
            'type' => 'required|in:reclamation,incident_discipline,maintenance_chambre',
            'priorite' => 'required|in:faible,moyenne,haute,urgente',
            'titre' => 'required|string|max:200',
            'description' => 'required|string',
            'chambre_id' => 'nullable|exists:chambres,id',
        ]);

        IncidentReclamation::create([
            'internat_id' => $validated['internat_id'],
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'priorite' => $validated['priorite'],
            'titre' => $validated['titre'],
            'description' => $validated['description'],
            'chambre_id' => $validated['chambre_id'] ?? null,
            'statut' => 'ouvert',
        ]);

        return back()->with('success', 'Incident / réclamation enregistré(e) avec succès.');
    }

    /**
     * Prise en charge et réponse officielle du gestionnaire.
     */
    public function traiter(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'statut' => 'required|in:ouvert,en_cours,traite,rejete',
            'reponse_gestionnaire' => 'required|string|max:1000',
        ]);

        $incident = IncidentReclamation::findOrFail($id);
        $incident->statut = $validated['statut'];
        $incident->reponse_gestionnaire = $validated['reponse_gestionnaire'];
        $incident->traite_par_user_id = Auth::id();

        if (in_array($validated['statut'], ['traite', 'rejete'])) {
            $incident->date_resolution = now();
        }

        $incident->save();

        return back()->with('success', "Le ticket #{$incident->id} a été actualisé avec succès.");
    }
}
