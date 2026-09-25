<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\Internat;
use App\Models\Presence;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PresenceController extends Controller
{
    /**
     * Feuille de présence quotidienne / Appel nocturne.
     */
    public function index(Request $request): View
    {
        $internats = Internat::all();
        $user = Auth::user();
        $selectedInternatId = $request->get('internat_id', $user->internat_id ?? $internats->first()?->id);
        $internat = Internat::findOrFail($selectedInternatId);

        $date = $request->get('date', Carbon::today()->toDateString());
        $typeControle = $request->get('type_controle', 'soir');

        // Tous les stagiaires avec un lit actif dans cet internat
        $dossiersHeberges = Dossier::with([
            'candidat.candidatProfile.filiere',
            'lit.chambre.batiment',
            'presences' => fn ($q) => $q->whereDate('date_presence', $date)->where('type_controle', $typeControle),
        ])
            ->where('internat_id', $internat->id)
            ->where('statut', 'admis')
            ->whereNotNull('lit_id')
            ->get();

        // Statistiques du jour
        $stats = [
            'total' => $dossiersHeberges->count(),
            'presents' => 0,
            'absents_justifies' => 0,
            'absents_injustifies' => 0,
            'permissions' => 0,
        ];

        foreach ($dossiersHeberges as $d) {
            $p = $d->presences->first();
            if ($p) {
                if ($p->statut === 'present') {
                    $stats['presents']++;
                } elseif ($p->statut === 'absent_justifie') {
                    $stats['absents_justifies']++;
                } elseif ($p->statut === 'absent_injustifie') {
                    $stats['absents_injustifies']++;
                } elseif ($p->statut === 'permission') {
                    $stats['permissions']++;
                }
            } else {
                $stats['presents']++; // par défaut considéré présent
            }
        }

        return view('presences.index', compact(
            'internats',
            'internat',
            'selectedInternatId',
            'date',
            'typeControle',
            'dossiersHeberges',
            'stats'
        ));
    }

    /**
     * Enregistrement en masse du pointage de présence.
     */
    public function enregistrerAppel(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'internat_id' => 'required|exists:internats,id',
            'date_presence' => 'required|date',
            'type_controle' => 'required|in:matin,soir',
            'presences' => 'required|array',
            'presences.*.dossier_id' => 'required|exists:dossiers,id',
            'presences.*.statut' => 'required|in:present,absent_justifie,absent_injustifie,permission',
            'presences.*.remarque' => 'nullable|string|max:255',
        ]);

        $internatId = (int) $validated['internat_id'];
        $date = $validated['date_presence'];
        $type = $validated['type_controle'];
        $userId = Auth::id();

        foreach ($validated['presences'] as $item) {
            Presence::updateOrCreate(
                [
                    'dossier_id' => $item['dossier_id'],
                    'date_presence' => $date,
                    'type_controle' => $type,
                ],
                [
                    'internat_id' => $internatId,
                    'statut' => $item['statut'],
                    'remarque' => $item['remarque'] ?? null,
                    'pointe_par_user_id' => $userId,
                ]
            );
        }

        return back()->with('success', 'Pointage des présences enregistré avec succès.');
    }
}
