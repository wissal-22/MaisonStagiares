<?php

namespace App\Services;

use App\Models\Dossier;
use App\Models\Internat;
use App\Models\Presence;
use App\Models\RestaurationRepas;
use Carbon\Carbon;

class RestaurationService
{
    /**
     * Calcule les effectifs journaliers et régimes alimentaires pour une date donnée.
     *
     * @return array{
     *     effectif_total_heberge: int,
     *     absents_autorises: int,
     *     effectif_a_nourrir: int,
     *     regimes_speciaux: array<string, int>
     * }
     */
    public function getEffectifsJournaliers(Internat $internat, string $date): array
    {
        $dateCarbon = Carbon::parse($date)->toDateString();

        // 1. Tous les stagiaires hébergés actifs (admis et ayant un lit attribué)
        $dossiersHeberges = Dossier::query()
            ->where('internat_id', $internat->id)
            ->where('statut', 'admis')
            ->whereNotNull('lit_id')
            ->with(['candidat.candidatProfile'])
            ->get();

        $totalHeberges = $dossiersHeberges->count();

        // 2. Absences / permissions signalées pour ce jour
        $dossierIds = $dossiersHeberges->pluck('id');
        $absentsIds = Presence::query()
            ->where('internat_id', $internat->id)
            ->whereIn('dossier_id', $dossierIds)
            ->whereDate('date_presence', $dateCarbon)
            ->whereIn('statut', ['absent_justifie', 'permission'])
            ->pluck('dossier_id')
            ->unique();

        $nbAbsents = $absentsIds->count();
        $effectifANourrir = max(0, $totalHeberges - $nbAbsents);

        // 3. Répartition des régimes alimentaires parmi les présents
        $regimesSpeciaux = [
            'sans_gluten' => 0,
            'vegetarien' => 0,
            'sans_lactose' => 0,
            'diabetique' => 0,
            'autre' => 0,
        ];

        foreach ($dossiersHeberges as $dossier) {
            if ($absentsIds->contains($dossier->id)) {
                continue;
            }

            $regime = strtolower($dossier->candidat->candidatProfile?->regime_alimentaire ?? '');
            if (empty($regime) || $regime === 'aucun' || $regime === 'normal') {
                continue;
            }

            if (str_contains($regime, 'gluten')) {
                $regimesSpeciaux['sans_gluten']++;
            } elseif (str_contains($regime, 'vege')) {
                $regimesSpeciaux['vegetarien']++;
            } elseif (str_contains($regime, 'lactose')) {
                $regimesSpeciaux['sans_lactose']++;
            } elseif (str_contains($regime, 'diabete')) {
                $regimesSpeciaux['diabetique']++;
            } else {
                $regimesSpeciaux['autre']++;
            }
        }

        return [
            'effectif_total_heberge' => $totalHeberges,
            'absents_autorises' => $nbAbsents,
            'effectif_a_nourrir' => $effectifANourrir,
            'regimes_speciaux' => $regimesSpeciaux,
        ];
    }

    /**
     * Génère ou synchronise les fiches repas d'un internat pour une journée (3 repas).
     */
    public function synchroniserRepasJournee(Internat $internat, string $date, int $userId): void
    {
        $stats = $this->getEffectifsJournaliers($internat, $date);
        $dateCarbon = Carbon::parse($date)->toDateString();
        $totalRegimes = array_sum($stats['regimes_speciaux']);

        $repasConfig = [
            'petit_dejeuner' => ['cout' => 10.00, 'menu' => 'Pain complet, beurre, confiture, fromage, lait chaud, thé, café, œufs durs'],
            'dejeuner' => ['cout' => 25.00, 'menu' => 'Salade marocaine, tajine poulet aux olives et frites, fruits de saison, pain'],
            'diner' => ['cout' => 20.00, 'menu' => 'Soupe harira traditionnelle, dattes, couscous aux légumes, yaourt'],
        ];

        foreach ($repasConfig as $type => $data) {
            $effectifPrevu = $stats['effectif_a_nourrir'];
            $coutTotal = $effectifPrevu * $data['cout'];

            RestaurationRepas::updateOrCreate(
                [
                    'internat_id' => $internat->id,
                    'date_repas' => $dateCarbon,
                    'type_repas' => $type,
                ],
                [
                    'effectif_prevu' => $effectifPrevu,
                    'effectif_reel' => $effectifPrevu, // par défaut effectif initialisé
                    'regimes_speciaux_count' => $totalRegimes,
                    'cout_unitaire_dh' => $data['cout'],
                    'cout_total_dh' => $coutTotal,
                    'menu_description' => $data['menu'],
                    'bon_commande_ref' => 'BC-'.strtoupper(substr($type, 0, 3)).'-'.Carbon::parse($date)->format('Ymd'),
                    'enregistre_par_user_id' => $userId,
                ]
            );
        }
    }
}
