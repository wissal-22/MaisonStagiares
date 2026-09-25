<?php

namespace App\Http\Controllers;

use App\Models\Internat;
use App\Models\RestaurationRepas;
use App\Services\RestaurationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RestaurationController extends Controller
{
    /**
     * Tableau de bord du module Restauration.
     */
    public function index(Request $request, RestaurationService $restaurationService): View
    {
        $internats = Internat::all();
        $user = Auth::user();
        $selectedInternatId = $request->get('internat_id', $user->internat_id ?? $internats->first()?->id);
        $internat = Internat::findOrFail($selectedInternatId);

        $selectedDate = $request->get('date', Carbon::today()->toDateString());

        // Calcul automatique en temps réel des effectifs du jour
        $calculsJour = $restaurationService->getEffectifsJournaliers($internat, $selectedDate);

        // Repas enregistrés pour cette date
        $repasDuJour = RestaurationRepas::where('internat_id', $internat->id)
            ->whereDate('date_repas', $selectedDate)
            ->get();

        // Si aucun repas généré pour cette date, les synchroniser automatiquement
        if ($repasDuJour->isEmpty()) {
            $restaurationService->synchroniserRepasJournee($internat, $selectedDate, Auth::id());
            $repasDuJour = RestaurationRepas::where('internat_id', $internat->id)
                ->whereDate('date_repas', $selectedDate)
                ->get();
        }

        // Historique récent des repas pour le suivi des coûts
        $historiqueRepas = RestaurationRepas::where('internat_id', $internat->id)
            ->latest('date_repas')
            ->limit(15)
            ->get();

        $coutTotalJour = $repasDuJour->sum('cout_total_dh');

        return view('restauration.index', compact(
            'internats',
            'internat',
            'selectedInternatId',
            'selectedDate',
            'calculsJour',
            'repasDuJour',
            'historiqueRepas',
            'coutTotalJour'
        ));
    }

    /**
     * Synchroniser les effectifs et recalculer les repas pour une date.
     */
    public function synchroniser(Request $request, RestaurationService $restaurationService): RedirectResponse
    {
        $validated = $request->validate([
            'internat_id' => 'required|exists:internats,id',
            'date' => 'required|date',
        ]);

        $internat = Internat::findOrFail($validated['internat_id']);
        $restaurationService->synchroniserRepasJournee($internat, $validated['date'], Auth::id());

        return back()->with('success', "Effectifs et bons de repas recalculés avec succès pour le {$validated['date']} !");
    }

    /**
     * Mise à jour du pointage réel et du menu d'un repas.
     */
    public function updateRepas(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'effectif_reel' => 'required|integer|min:0',
            'menu_description' => 'nullable|string|max:500',
            'bon_commande_ref' => 'nullable|string|max:100',
            'statut' => 'required|in:planifie,consomme,cloture',
        ]);

        $repas = RestaurationRepas::findOrFail($id);
        $repas->effectif_reel = $validated['effectif_reel'];
        $repas->cout_total_dh = $repas->effectif_reel * $repas->cout_unitaire_dh;
        $repas->menu_description = $validated['menu_description'];
        $repas->bon_commande_ref = $validated['bon_commande_ref'];
        $repas->statut = $validated['statut'];
        $repas->save();

        return back()->with('success', "Le repas {$repas->type_repas_label} a été mis à jour avec un effectif réel de {$repas->effectif_reel} convives (Écart: {$repas->ecart}).");
    }
}
