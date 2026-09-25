<?php

namespace App\Services;

use App\Models\Dossier;
use App\Models\Lit;
use App\Models\Mouvement;

class AllocationChambreService
{
    /**
     * Affecte un lit à un stagiaire admis.
     */
    public function affecterLit(Dossier $dossier, Lit $lit, int $userId): bool
    {
        if ($lit->statut !== 'libre') {
            return false;
        }

        $ancienLitId = $dossier->lit_id;

        // Libérer ancien lit s'il existait
        if ($ancienLitId) {
            Lit::where('id', $ancienLitId)->update(['statut' => 'libre']);
        }

        // Assigner le nouveau lit
        $dossier->lit_id = $lit->id;
        $dossier->date_arrivee_effective = $dossier->date_arrivee_effective ?? now();
        $dossier->save();

        $lit->statut = 'occupe';
        $lit->save();

        // Enregistrer le mouvement
        Mouvement::create([
            'dossier_id' => $dossier->id,
            'internat_id' => $dossier->internat_id,
            'type' => $ancienLitId ? 'changement_chambre' : 'arrivee',
            'date_mouvement' => now(),
            'ancien_lit_id' => $ancienLitId,
            'nouveau_lit_id' => $lit->id,
            'motif' => $ancienLitId ? "Mutation vers le lit {$lit->code}" : "Affectation initiale et check-in au lit {$lit->code}",
            'enregistre_par_user_id' => $userId,
        ]);

        return true;
    }

    /**
     * Affectation automatique des stagiaires admis sans lit.
     *
     * @return array{affectes: int, echecs: int}
     */
    public function autoAffecterAdmis(int $internatId, int $userId): array
    {
        $dossiersSansLit = Dossier::query()
            ->where('internat_id', $internatId)
            ->where('statut', 'admis')
            ->whereNull('lit_id')
            ->with(['candidat.candidatProfile'])
            ->get();

        $affectes = 0;
        $echecs = 0;

        foreach ($dossiersSansLit as $dossier) {
            $genreCandidat = $dossier->candidat->candidatProfile?->genre ?? 'garcon';
            $genreBatiment = $genreCandidat === 'fille' ? 'filles' : 'garcons';
            $isHandicape = (bool) $dossier->candidat->candidatProfile?->en_situation_handicap;

            // Chercher lit disponible
            $queryLit = Lit::query()
                ->where('statut', 'libre')
                ->whereHas('chambre.batiment', function ($q) use ($internatId, $genreBatiment) {
                    $q->where('internat_id', $internatId)
                        ->where(function ($sub) use ($genreBatiment) {
                            $sub->where('genre', $genreBatiment)
                                ->orWhere('genre', 'mixte');
                        });
                });

            if ($isHandicape) {
                // Priorité rez-de-chaussée (étage 0) ou chambre PMR
                $litTrouve = (clone $queryLit)->whereHas('chambre', function ($q) {
                    $q->where('etage', 0)->orWhere('type', 'pmr');
                })->first();
            } else {
                $litTrouve = null;
            }

            if (! $litTrouve) {
                $litTrouve = $queryLit->first();
            }

            if ($litTrouve && $this->affecterLit($dossier, $litTrouve, $userId)) {
                $affectes++;
            } else {
                $echecs++;
            }
        }

        return [
            'affectes' => $affectes,
            'echecs' => $echecs,
        ];
    }
}
