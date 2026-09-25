@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:flex-row lg:items-center">
        <div>
            <div class="mb-1 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-amber-600">
                <i class="fa-solid fa-utensils"></i> Gestion de la restauration
            </div>
            <h1 class="text-2xl font-bold text-slate-900">Repas et effectifs</h1>
            <p class="mt-1 text-xs text-slate-500">Planifiez les repas, suivez les effectifs et mettez a jour les menus.</p>
        </div>
        <form method="GET" action="{{ route('restauration.index') }}" class="flex flex-wrap items-center gap-2">
            <label for="internat_id" class="text-xs font-semibold text-slate-600">Internat</label>
            <select id="internat_id" name="internat_id" class="rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-amber-500 focus:ring-amber-500">
                @foreach($internats as $item)
                    <option value="{{ $item->id }}" {{ (string) $selectedInternatId === (string) $item->id ? 'selected' : '' }}>{{ $item->nom }}</option>
                @endforeach
            </select>
            <label for="date" class="text-xs font-semibold text-slate-600">Date</label>
            <input id="date" name="date" type="date" value="{{ $selectedDate }}" class="rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-amber-500 focus:ring-amber-500">
            <button type="submit" class="rounded-xl bg-amber-600 px-4 py-2 text-xs font-semibold text-white hover:bg-amber-700">Afficher</button>
        </form>
    </div>

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><div class="text-2xl font-extrabold text-slate-900">{{ $calculsJour['effectif_total_heberge'] }}</div><div class="mt-1 text-[10px] font-semibold uppercase tracking-wider text-slate-500">Heberges</div></div>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4"><div class="text-2xl font-extrabold text-emerald-700">{{ $calculsJour['effectif_a_nourrir'] }}</div><div class="mt-1 text-[10px] font-semibold uppercase tracking-wider text-emerald-600">A nourrir</div></div>
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4"><div class="text-2xl font-extrabold text-amber-700">{{ $calculsJour['absents_autorises'] }}</div><div class="mt-1 text-[10px] font-semibold uppercase tracking-wider text-amber-600">Absents autorises</div></div>
        <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4"><div class="text-2xl font-extrabold text-indigo-700">{{ number_format($coutTotalJour, 2) }} DH</div><div class="mt-1 text-[10px] font-semibold uppercase tracking-wider text-indigo-600">Cout du jour</div></div>
    </div>

    <div class="flex flex-col justify-between gap-4 rounded-2xl border border-amber-200 bg-amber-50 p-5 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-base font-bold text-amber-900">Synchroniser la journee</h2>
            <p class="mt-1 text-xs text-amber-700">Recalcule les effectifs et genere les bons de repas pour la date selectionnee.</p>
        </div>
        <form method="POST" action="{{ route('restauration.synchroniser') }}">
            @csrf
            <input type="hidden" name="internat_id" value="{{ $internat->id }}">
            <input type="hidden" name="date" value="{{ $selectedDate }}">
            <button type="submit" class="rounded-xl bg-amber-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-amber-700"><i class="fa-solid fa-rotate mr-1"></i> Synchroniser</button>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-base font-bold text-slate-900">Repas du {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}</h2>
                <p class="mt-1 text-xs text-slate-500">Mettez a jour l'effectif reel, le menu et la reference du bon de commande.</p>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($repasDuJour as $repas)
                    <form method="POST" action="{{ route('restauration.repas.update', $repas->id) }}" class="space-y-4 p-5">
                        @csrf
                        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900"><i class="fa-solid fa-clock mr-2 text-amber-500"></i>{{ $repas->type_repas_label }}</h3>
                                <p class="mt-1 text-[11px] text-slate-500">Prevu : {{ $repas->effectif_prevu }} · Regimes speciaux : {{ $repas->regimes_speciaux_count }} · Cout unitaire : {{ number_format($repas->cout_unitaire_dh, 2) }} DH</p>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase {{ $repas->statut === 'cloture' ? 'bg-slate-100 text-slate-700' : ($repas->statut === 'consomme' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800') }}">{{ $repas->statut ?? 'Planifie' }}</span>
                        </div>
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                            <div>
                                <label for="effectif_reel_{{ $repas->id }}" class="mb-1 block text-[11px] font-semibold text-slate-600">Effectif reel</label>
                                <input id="effectif_reel_{{ $repas->id }}" name="effectif_reel" type="number" min="0" required value="{{ $repas->effectif_reel }}" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-amber-500 focus:ring-amber-500">
                            </div>
                            <div class="md:col-span-2">
                                <label for="menu_{{ $repas->id }}" class="mb-1 block text-[11px] font-semibold text-slate-600">Menu</label>
                                <input id="menu_{{ $repas->id }}" name="menu_description" type="text" value="{{ $repas->menu_description }}" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-amber-500 focus:ring-amber-500">
                            </div>
                            <div>
                                <label for="statut_{{ $repas->id }}" class="mb-1 block text-[11px] font-semibold text-slate-600">Statut</label>
                                <select id="statut_{{ $repas->id }}" name="statut" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-amber-500 focus:ring-amber-500">
                                    @foreach(['planifie', 'consomme', 'cloture'] as $statut)
                                        <option value="{{ $statut }}" {{ ($repas->statut ?? 'planifie') === $statut ? 'selected' : '' }}>{{ ucfirst($statut) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                            <div class="flex-1">
                                <label for="bon_{{ $repas->id }}" class="mb-1 block text-[11px] font-semibold text-slate-600">Reference bon de commande</label>
                                <input id="bon_{{ $repas->id }}" name="bon_commande_ref" type="text" value="{{ $repas->bon_commande_ref }}" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-amber-500 focus:ring-amber-500">
                            </div>
                            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-slate-700">Enregistrer</button>
                        </div>
                    </form>
                @empty
                    <p class="p-8 text-center text-xs text-slate-500">Aucun repas enregistre pour cette date.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 flex items-center gap-2 text-base font-bold text-slate-900"><i class="fa-solid fa-chart-pie text-amber-600"></i> Regimes speciaux</h2>
            <div class="space-y-3">
                @foreach($calculsJour['regimes_speciaux'] as $regime => $nombre)
                    <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2 text-xs">
                        <span class="text-slate-600">{{ ucfirst(str_replace('_', ' ', $regime)) }}</span>
                        <strong class="text-slate-900">{{ $nombre }}</strong>
                    </div>
                @endforeach
            </div>
            <h2 class="mb-4 mt-8 flex items-center gap-2 text-base font-bold text-slate-900"><i class="fa-solid fa-clock-rotate-left text-slate-500"></i> Historique recent</h2>
            <div class="space-y-2">
                @forelse($historiqueRepas as $repas)
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2 text-[11px] last:border-0">
                        <div><div class="font-semibold text-slate-700">{{ $repas->type_repas_label }}</div><div class="text-slate-400">{{ $repas->date_repas?->format('d/m/Y') }}</div></div>
                        <span class="font-bold text-slate-900">{{ number_format($repas->cout_total_dh, 2) }} DH</span>
                    </div>
                @empty
                    <p class="text-xs text-slate-500">Aucun historique.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
