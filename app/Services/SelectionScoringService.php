<?php

namespace App\Services;

use App\Models\CritereSelection;
use App\Models\Dossier;

class SelectionScoringService
{
    /**
     * Calcule le score d'un dossier selon les critères configurés.
     *
     * @return array{total: float, details: array<string, array{nom: string, points: float, motif: string}>}
     */
    public function calculateScore(Dossier $dossier): array
    {
        $dossier->loadMissing(['candidat.candidatProfile', 'internat']);
        $profile = $dossier->candidat->candidatProfile;

        if (! $profile) {
            return ['total' => 0.0, 'details' => []];
        }

        // Récupérer critères spécifiques à l'internat ou nationaux
        $criteres = CritereSelection::query()
            ->where('est_actif', true)
            ->where(function ($query) use ($dossier) {
                $query->where('internat_id', $dossier->internat_id)
                    ->orWhereNull('internat_id');
            })
            ->get();

        $totalScore = 0.0;
        $details = [];

        foreach ($criteres as $critere) {
            $points = 0.0;
            $motif = '';

            switch ($critere->type_critere) {
                case 'distance':
                    // Plus le candidat est éloigné, plus le score est élevé
                    if ($profile->distance_km >= 200) {
                        $points = (float) $critere->points_base * 1.5;
                        $motif = "Éloignement très important (>= 200 km : {$profile->distance_km} km)";
                    } elseif ($profile->distance_km >= 100) {
                        $points = (float) $critere->points_base * 1.0;
                        $motif = "Éloignement important (100-199 km : {$profile->distance_km} km)";
                    } elseif ($profile->distance_km >= 50) {
                        $points = (float) $critere->points_base * 0.5;
                        $motif = "Éloignement moyen (50-99 km : {$profile->distance_km} km)";
                    } else {
                        $points = 0.0;
                        $motif = "Proximité géographique (< 50 km : {$profile->distance_km} km)";
                    }
                    break;

                case 'revenu':
                    // Plus le revenu des parents est faible, plus le score est élevé
                    if ($profile->revenu_mensuel_parents <= 2000) {
                        $points = (float) $critere->points_base * 1.5;
                        $motif = 'Revenu familial très modeste (<= 2000 DH)';
                    } elseif ($profile->revenu_mensuel_parents <= 4000) {
                        $points = (float) $critere->points_base * 1.0;
                        $motif = 'Revenu familial modeste (2001-4000 DH)';
                    } elseif ($profile->revenu_mensuel_parents <= 7000) {
                        $points = (float) $critere->points_base * 0.5;
                        $motif = 'Revenu familial moyen (4001-7000 DH)';
                    } else {
                        $points = 0.0;
                        $motif = 'Revenu familial supérieur à 7000 DH';
                    }
                    break;

                case 'orphelin':
                    if ($profile->est_orphelin) {
                        $points = (float) $critere->points_base * 1.2;
                        $motif = 'Candidat orphelin (priorité sociale)';
                    }
                    break;

                case 'handicap':
                    if ($profile->en_situation_handicap) {
                        $points = (float) $critere->points_base * 1.2;
                        $motif = 'Candidat en situation de handicap (chambre PMR prioritaire)';
                    }
                    break;

                case 'reconduction':
                    if ($profile->annee_precedente_heberge) {
                        $points = (float) $critere->points_base * 1.0;
                        $motif = 'Réinscription / reconduction d’un ancien stagiaire assidu';
                    }
                    break;

                default:
                    $points = (float) $critere->points_base;
                    $motif = 'Critère standard appliqué';
                    break;
            }

            // Pondération
            $weightedPoints = round($points * (float) $critere->poids, 2);
            $totalScore += $weightedPoints;

            $details[$critere->code] = [
                'nom' => $critere->nom,
                'points' => $weightedPoints,
                'poids' => (float) $critere->poids,
                'motif' => $motif,
            ];
        }

        return [
            'total' => round($totalScore, 2),
            'details' => $details,
        ];
    }

    /**
     * Exécute la commission de délibération sur une campagne / internat.
     *
     * @return array{total_traites: int, admis: int, liste_attente: int, refuses: int}
     */
    public function deliberer(int $campagneId, ?int $internatId = null, int $placesDisponibles = 50): array
    {
        $query = Dossier::query()
            ->where('campagne_id', $campagneId)
            ->whereIn('statut', ['soumis', 'complet', 'en_commission']);

        if ($internatId) {
            $query->where('internat_id', $internatId);
        }

        $dossiers = $query->get();

        // 1. Calculer le score pour chaque dossier
        foreach ($dossiers as $dossier) {
            $result = $this->calculateScore($dossier);
            $dossier->score_total = $result['total'];
            $dossier->details_score = $result['details'];
            $dossier->save();
        }

        // 2. Classer par score décroissant
        $dossiersClasses = $dossiers->sortByDesc('score_total')->values();

        $admisCount = 0;
        $attenteCount = 0;
        $refuseCount = 0;

        foreach ($dossiersClasses as $index => $dossier) {
            if ($index < $placesDisponibles) {
                $dossier->statut = 'admis';
                $dossier->motif_decision = 'Admis en commission par classement au mérite social (Rang #'.($index + 1)." - Score: {$dossier->score_total})";
                $admisCount++;
            } elseif ($index < ($placesDisponibles + 25)) {
                $dossier->statut = 'liste_attente';
                $dossier->motif_decision = "Placé sur liste d'attente (Rang #".($index + 1)." - Score: {$dossier->score_total})";
                $attenteCount++;
            } else {
                $dossier->statut = 'refuse';
                $dossier->motif_decision = "Capacité d'accueil atteinte pour cette campagne (Score: {$dossier->score_total})";
                $refuseCount++;
            }

            $dossier->valide_par_admin = true;
            $dossier->save();
        }

        return [
            'total_traites' => $dossiersClasses->count(),
            'admis' => $admisCount,
            'liste_attente' => $attenteCount,
            'refuses' => $refuseCount,
        ];
    }
}
