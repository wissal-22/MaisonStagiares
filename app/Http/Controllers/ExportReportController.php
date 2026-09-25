<?php

namespace App\Http\Controllers;

use App\Models\Campagne;
use App\Models\Dossier;
use App\Models\Internat;
use App\Models\RestaurationRepas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExportReportController extends Controller
{
    /**
     * Fiche d'hébergement individuelle officielle (F3.5).
     */
    public function ficheHebergement(int $dossierId): View
    {
        $dossier = Dossier::with([
            'candidat.candidatProfile.niveau',
            'candidat.candidatProfile.filiere',
            'internat.etablissement',
            'lit.chambre.batiment',
            'mouvements',
        ])->findOrFail($dossierId);

        return view('reports.fiche-hebergement', compact('dossier'));
    }

    /**
     * Registre général d'occupation par internat (F3.8).
     */
    public function registreOccupation(Request $request, int $internatId): View
    {
        $internat = Internat::with([
            'etablissement.region',
            'batiments.chambres.lits.dossierActif.candidat.candidatProfile.filiere',
        ])->findOrFail($internatId);

        return view('reports.registre-occupation', compact('internat'));
    }

    /**
     * Procès-verbal de délibération de la commission de sélection (F2.4, F2.5).
     */
    public function pvDeliberation(int $campagneId): View
    {
        $campagne = Campagne::with('anneeScolaire', 'internat.etablissement')->findOrFail($campagneId);

        $dossiersAdmis = Dossier::where('campagne_id', $campagneId)
            ->where('statut', 'admis')
            ->orderByDesc('score_total')
            ->with(['candidat.candidatProfile.filiere', 'lit.chambre'])
            ->get();

        $dossiersAttente = Dossier::where('campagne_id', $campagneId)
            ->where('statut', 'liste_attente')
            ->orderByDesc('score_total')
            ->with(['candidat.candidatProfile.filiere'])
            ->get();

        $dossiersRefuses = Dossier::where('campagne_id', $campagneId)
            ->where('statut', 'refuse')
            ->orderByDesc('score_total')
            ->with(['candidat.candidatProfile.filiere'])
            ->get();

        return view('reports.pv-deliberation', compact('campagne', 'dossiersAdmis', 'dossiersAttente', 'dossiersRefuses'));
    }

    /**
     * Rapport de consommation et d'écarts de restauration (F4.5).
     */
    public function rapportRestauration(Request $request, int $internatId): View
    {
        $internat = Internat::with('etablissement')->findOrFail($internatId);
        $mois = $request->get('mois', Carbon::today()->format('Y-m'));

        $repas = RestaurationRepas::where('internat_id', $internatId)
            ->where('date_repas', 'like', "{$mois}%")
            ->orderBy('date_repas')
            ->get();

        $totalPrevu = $repas->sum('effectif_prevu');
        $totalReel = $repas->sum('effectif_reel');
        $totalCout = $repas->sum('cout_total_dh');
        $ecartGlobal = $totalReel - $totalPrevu;

        return view('reports.rapport-restauration', compact('internat', 'mois', 'repas', 'totalPrevu', 'totalReel', 'totalCout', 'ecartGlobal'));
    }
}
