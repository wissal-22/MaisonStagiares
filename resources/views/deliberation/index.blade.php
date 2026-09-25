@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:flex-row lg:items-center">
        <div>
            <div class="mb-1 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-indigo-600">
                <i class="fa-solid fa-scale-balanced"></i> Commission de selection
            </div>
            <h1 class="text-2xl font-bold text-slate-900">Deliberation et admission</h1>
            <p class="mt-1 text-xs text-slate-500">Classement des candidats par score et decisions d'admission.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <form method="POST" action="{{ route('deliberation.valider_directeur') }}">
                @csrf
                <input type="hidden" name="campagne_id" value="{{ $campagne?->id }}">
                <input type="hidden" name="internat_id" value="{{ $selectedInternatId }}">
                <button type="submit" class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100" {{ $campagne ? '' : 'disabled' }}>
                    <i class="fa-solid fa-stamp mr-1"></i> Valider les admis
                </button>
            </form>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('deliberation.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-3">
            <div>
                <label for="campagne_id" class="mb-1 block text-xs font-semibold text-slate-600">Campagne</label>
                <select id="campagne_id" name="campagne_id" class="block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach($campagnes as $item)
                        <option value="{{ $item->id }}" {{ $campagne?->id === $item->id ? 'selected' : '' }}>{{ $item->titre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="internat_id" class="mb-1 block text-xs font-semibold text-slate-600">Internat</label>
                <select id="internat_id" name="internat_id" class="block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Tous les internats</option>
                    @foreach($internats as $internat)
                        <option value="{{ $internat->id }}" {{ (string) $selectedInternatId === (string) $internat->id ? 'selected' : '' }}>{{ $internat->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-indigo-700">
                    <i class="fa-solid fa-filter mr-1"></i> Afficher le classement
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">
        @foreach([
            'total' => ['label' => 'Total', 'class' => 'bg-slate-50 text-slate-800 border-slate-200'],
            'admis' => ['label' => 'Admis', 'class' => 'bg-emerald-50 text-emerald-800 border-emerald-200'],
            'liste_attente' => ['label' => 'Liste attente', 'class' => 'bg-amber-50 text-amber-800 border-amber-200'],
            'refuses' => ['label' => 'Refuses', 'class' => 'bg-rose-50 text-rose-800 border-rose-200'],
            'en_attente_deliberation' => ['label' => 'A traiter', 'class' => 'bg-indigo-50 text-indigo-800 border-indigo-200'],
        ] as $key => $stat)
            <div class="rounded-xl border p-4 {{ $stat['class'] }}">
                <div class="text-2xl font-extrabold">{{ $stats[$key] }}</div>
                <div class="mt-1 text-[10px] font-semibold uppercase tracking-wider">{{ $stat['label'] }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Classement des candidats</h2>
                    <p class="mt-1 text-xs text-slate-500">{{ $dossiers->count() }} dossier(s), tries par score decroissant</p>
                </div>
                @if($campagne)
                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-[10px] font-semibold text-indigo-700">{{ $campagne->titre }}</span>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-xs">
                    <thead class="border-b border-slate-200 bg-slate-50 text-[10px] uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Rang</th>
                            <th class="px-5 py-3 font-semibold">Candidat</th>
                            <th class="px-5 py-3 font-semibold">Formation</th>
                            <th class="px-5 py-3 font-semibold">Score</th>
                            <th class="px-5 py-3 font-semibold">Decision</th>
                            <th class="px-5 py-3 text-right font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($dossiers as $index => $dossier)
                            <tr class="transition hover:bg-slate-50">
                                <td class="px-5 py-4">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-full {{ $index < 3 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }} font-bold">{{ $index + 1 }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-900">{{ $dossier->candidat?->name ?? 'Candidat inconnu' }}</div>
                                    <div class="mt-1 text-[11px] text-slate-500">{{ $dossier->numero_dossier }}</div>
                                </td>
                                <td class="px-5 py-4 text-slate-600">
                                    {{ $dossier->candidat?->candidatProfile?->niveau?->nom ?? 'Non renseigne' }}<br>
                                    <span class="text-[11px] text-slate-400">{{ $dossier->candidat?->candidatProfile?->filiere?->nom ?? 'Filiere non renseignee' }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="text-lg font-extrabold text-indigo-700">{{ $dossier->score_total ?? 0 }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-bold uppercase {{ $dossier->statut_badge_class }}">{{ $dossier->statut_label }}</span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    @if($dossier->statut === 'liste_attente')
                                        <form method="POST" action="{{ route('deliberation.promouvoir', $dossier->id) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-emerald-50 px-3 py-2 text-[11px] font-semibold text-emerald-700 hover:bg-emerald-100">Promouvoir</button>
                                        </form>
                                    @else
                                        <a href="{{ route('dossiers.show', $dossier->id) }}" class="rounded-lg bg-slate-100 px-3 py-2 text-[11px] font-semibold text-slate-700 hover:bg-slate-200">Details</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-12 text-center text-xs text-slate-500">Aucun dossier pour cette selection.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="mb-4 flex items-center gap-2 text-base font-bold text-slate-900"><i class="fa-solid fa-bolt text-indigo-600"></i> Lancer la deliberation</h2>
                <form method="POST" action="{{ route('deliberation.lancer') }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="campagne_id" value="{{ $campagne?->id }}">
                    <input type="hidden" name="internat_id" value="{{ $selectedInternatId }}">
                    <div>
                        <label for="places_disponibles" class="mb-1 block text-xs font-semibold text-slate-600">Places disponibles</label>
                        <input id="places_disponibles" name="places_disponibles" type="number" min="1" required value="{{ $campagne?->quota_places ?? 1 }}" class="block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-indigo-700">Executer le classement automatique</button>
                </form>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="mb-4 flex items-center gap-2 text-base font-bold text-slate-900"><i class="fa-solid fa-list-check text-emerald-600"></i> Criteres actifs</h2>
                <div class="space-y-3">
                    @forelse($criteres as $critere)
                        <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-2 last:border-0 last:pb-0">
                            <span class="text-xs text-slate-600">{{ $critere->nom }}</span>
                            <span class="rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-bold text-emerald-700">{{ $critere->points_max }} pts</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500">Aucun critere actif.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
