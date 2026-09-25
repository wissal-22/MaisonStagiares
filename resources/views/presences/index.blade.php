@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:flex-row lg:items-center">
        <div>
            <div class="mb-1 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-violet-600">
                <i class="fa-solid fa-clipboard-user"></i> Suivi des presences
            </div>
            <h1 class="text-2xl font-bold text-slate-900">Feuille d'appel</h1>
            <p class="mt-1 text-xs text-slate-500">Enregistrez la presence des stagiaires heberges pour le controle selectionne.</p>
        </div>
        <form method="GET" action="{{ route('presences.index') }}" class="flex flex-wrap items-center gap-2">
            <label for="internat_id" class="text-xs font-semibold text-slate-600">Internat</label>
            <select id="internat_id" name="internat_id" class="rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-violet-500 focus:ring-violet-500">
                @foreach($internats as $item)
                    <option value="{{ $item->id }}" {{ (string) $selectedInternatId === (string) $item->id ? 'selected' : '' }}>{{ $item->nom }}</option>
                @endforeach
            </select>
            <label for="date" class="text-xs font-semibold text-slate-600">Date</label>
            <input id="date" name="date" type="date" value="{{ $date }}" class="rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-violet-500 focus:ring-violet-500">
            <select name="type_controle" class="rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-violet-500 focus:ring-violet-500">
                <option value="matin" {{ $typeControle === 'matin' ? 'selected' : '' }}>Controle du matin</option>
                <option value="soir" {{ $typeControle === 'soir' ? 'selected' : '' }}>Controle du soir</option>
            </select>
            <button type="submit" class="rounded-xl bg-violet-600 px-4 py-2 text-xs font-semibold text-white hover:bg-violet-700">Afficher</button>
        </form>
    </div>

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">
        @foreach([
            'total' => ['label' => 'Total', 'class' => 'bg-slate-50 text-slate-800 border-slate-200'],
            'presents' => ['label' => 'Presents', 'class' => 'bg-emerald-50 text-emerald-800 border-emerald-200'],
            'absents_justifies' => ['label' => 'Absents justifies', 'class' => 'bg-amber-50 text-amber-800 border-amber-200'],
            'absents_injustifies' => ['label' => 'Absents non justifies', 'class' => 'bg-rose-50 text-rose-800 border-rose-200'],
            'permissions' => ['label' => 'Permissions', 'class' => 'bg-sky-50 text-sky-800 border-sky-200'],
        ] as $key => $stat)
            <div class="rounded-xl border p-4 {{ $stat['class'] }}"><div class="text-2xl font-extrabold">{{ $stats[$key] }}</div><div class="mt-1 text-[10px] font-semibold uppercase tracking-wider">{{ $stat['label'] }}</div></div>
        @endforeach
    </div>

    <form method="POST" action="{{ route('presences.enregistrer') }}" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @csrf
        <input type="hidden" name="internat_id" value="{{ $internat->id }}">
        <input type="hidden" name="date_presence" value="{{ $date }}">
        <input type="hidden" name="type_controle" value="{{ $typeControle }}">
        <div class="flex flex-col justify-between gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-base font-bold text-slate-900">Appel du {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h2>
                <p class="mt-1 text-xs text-slate-500">Controle : {{ $typeControle === 'matin' ? 'Matin' : 'Soir' }} · Les personnes sans pointage sont presentes par defaut.</p>
            </div>
            <button type="submit" class="rounded-xl bg-violet-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-violet-700"><i class="fa-solid fa-floppy-disk mr-1"></i> Enregistrer l'appel</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-left text-xs">
                <thead class="border-b border-slate-200 bg-slate-50 text-[10px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Stagiaire</th>
                        <th class="px-5 py-3 font-semibold">Emplacement</th>
                        <th class="px-5 py-3 font-semibold">Statut</th>
                        <th class="px-5 py-3 font-semibold">Remarque</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($dossiersHeberges as $index => $dossier)
                        @php
                            $presence = $dossier->presences->first();
                            $statut = $presence?->statut ?? 'present';
                        @endphp
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <input type="hidden" name="presences[{{ $index }}][dossier_id]" value="{{ $dossier->id }}">
                                <div class="font-semibold text-slate-900">{{ $dossier->candidat?->name ?? 'Candidat inconnu' }}</div>
                                <div class="mt-1 text-[11px] text-slate-500">{{ $dossier->candidat?->candidatProfile?->filiere?->nom ?? 'Filiere non renseignee' }}</div>
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                <div>Chambre {{ $dossier->lit?->chambre?->numero ?? '-' }}</div>
                                <div class="mt-1 text-[11px] text-slate-400">Lit {{ $dossier->lit?->code ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <select name="presences[{{ $index }}][statut]" class="rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-violet-500 focus:ring-violet-500">
                                    @foreach(['present' => 'Present', 'absent_justifie' => 'Absent justifie', 'absent_injustifie' => 'Absent non justifie', 'permission' => 'Permission'] as $value => $label)
                                        <option value="{{ $value }}" {{ $statut === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-5 py-4">
                                <input name="presences[{{ $index }}][remarque]" type="text" value="{{ $presence?->remarque }}" placeholder="Optionnel" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-violet-500 focus:ring-violet-500">
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-12 text-center text-xs text-slate-500">Aucun stagiaire heberge dans cet internat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>
</div>
@endsection
