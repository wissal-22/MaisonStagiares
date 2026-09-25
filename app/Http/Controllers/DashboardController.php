<?php

namespace App\Http\Controllers;

use App\Models\Campagne;
use App\Models\Dossier;
use App\Models\IncidentReclamation;
use App\Models\Internat;
use App\Models\Lit;
use App\Models\Mouvement;
use App\Models\Region;
use App\Models\RestaurationRepas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Si l'utilisateur est candidat, rediriger vers son espace dédié
        if ($user->isCandidat()) {
            return view('candidat.dashboard');
        }

        // Récupérer l'internat sélectionné ou par défaut pour le gestionnaire/directeur
        $internatId = $request->get('internat_id', $user->internat_id);
        $internats = Internat::with('etablissement.region')->get();

        $selectedInternat = $internatId ? Internat::find($internatId) : $internats->first();

        // 1. Statistiques globales ou filtrées
        $queryDossiers = Dossier::query();
        $queryLits = Lit::query();
        $queryIncidents = IncidentReclamation::query();
        $queryRepas = RestaurationRepas::query()->whereDate('date_repas', Carbon::today());

        if ($selectedInternat && ($user->isGestionnaire() || $user->isDirecteur() || $request->filled('internat_id'))) {
            $queryDossiers->where('internat_id', $selectedInternat->id);
            $queryLits->whereHas('chambre.batiment', function ($q) use ($selectedInternat) {
                $q->where('internat_id', $selectedInternat->id);
            });
            $queryIncidents->where('internat_id', $selectedInternat->id);
            $queryRepas->where('internat_id', $selectedInternat->id);
        }

        $totalDossiers = (clone $queryDossiers)->count();
        $dossiersAdmis = (clone $queryDossiers)->where('statut', 'admis')->count();
        $dossiersEnCommission = (clone $queryDossiers)->whereIn('statut', ['soumis', 'complet', 'en_commission'])->count();
        $dossiersAttente = (clone $queryDossiers)->where('statut', 'liste_attente')->count();
        $dossiersRefuses = (clone $queryDossiers)->where('statut', 'refuse')->count();

        $totalLits = (clone $queryLits)->count();
        $litsOccupes = (clone $queryLits)->where('statut', 'occupe')->count();
        $litsLibres = (clone $queryLits)->where('statut', 'libre')->count();
        $tauxOccupation = $totalLits > 0 ? round(($litsOccupes / $totalLits) * 100, 1) : 0;

        $incidentsOuverts = (clone $queryIncidents)->whereIn('statut', ['ouvert', 'en_cours'])->count();

        // Effectifs restauration aujourd'hui
        $repasAujourdhui = $queryRepas->get();
        $effectifJourPrevu = $repasAujourdhui->max('effectif_prevu') ?? $litsOccupes;
        $coutJourTotal = $repasAujourdhui->sum('cout_total_dh');
        $regimesSpeciauxCount = $repasAujourdhui->max('regimes_speciaux_count') ?? 0;

        // Mouvements récents
        $mouvementsRecents = Mouvement::with(['dossier.candidat', 'internat'])
            ->latest('date_mouvement')
            ->limit(5)
            ->get();

        // Dossiers récents
        $dossiersRecents = (clone $queryDossiers)
            ->with(['candidat.candidatProfile', 'internat'])
            ->latest('date_depot')
            ->limit(6)
            ->get();

        // Répartition régionale pour la direction régionale et centrale
        $regionsStats = Region::with(['etablissements.internats.dossiers'])->get()->map(function ($reg) {
            $capaciteReg = 0;
            $admisReg = 0;
            $dossiersReg = 0;

            foreach ($reg->etablissements as $etab) {
                foreach ($etab->internats as $int) {
                    $capaciteReg += $int->capacite_totale;
                    $admisReg += $int->dossiers->where('statut', 'admis')->count();
                    $dossiersReg += $int->dossiers->count();
                }
            }

            return [
                'region' => $reg,
                'capacite' => $capaciteReg,
                'admis' => $admisReg,
                'dossiers' => $dossiersReg,
                'taux' => $capaciteReg > 0 ? round(($admisReg / $capaciteReg) * 100, 1) : 0,
            ];
        });

        // Campagnes actives
        $campagneActive = Campagne::with('anneeScolaire')->where('statut', 'ouverte')->first();

        // Alertes
        $alertes = [];
        if ($tauxOccupation >= 90) {
            $alertes[] = ['type' => 'warning', 'titre' => 'Capacité critique', 'message' => "Le taux d'occupation atteint {$tauxOccupation}%. Très peu de places restantes."];
        }
        if ($dossiersEnCommission > 0) {
            $alertes[] = ['type' => 'info', 'titre' => 'Délibération en attente', 'message' => "{$dossiersEnCommission} dossiers sont prêts pour passage en commission d'admission."];
        }
        if ($incidentsOuverts > 0) {
            $alertes[] = ['type' => 'rose', 'titre' => 'Incidents / Réclamations', 'message' => "{$incidentsOuverts} réclamations ou demandes de maintenance requièrent une réponse."];
        }

        return view('dashboard.index', compact(
            'user',
            'internats',
            'selectedInternat',
            'totalDossiers',
            'dossiersAdmis',
            'dossiersEnCommission',
            'dossiersAttente',
            'dossiersRefuses',
            'totalLits',
            'litsOccupes',
            'litsLibres',
            'tauxOccupation',
            'incidentsOuverts',
            'effectifJourPrevu',
            'coutJourTotal',
            'regimesSpeciauxCount',
            'mouvementsRecents',
            'dossiersRecents',
            'regionsStats',
            'campagneActive',
            'alertes'
        ));
    }
}
