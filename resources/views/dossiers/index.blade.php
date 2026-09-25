@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:flex-row md:items-center">
        <div>
            <div class="mb-1 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-emerald-600">
                <i class="fa-solid fa-folder-open"></i> Instruction des dossiers
            </div>
            <h1 class="text-2xl font-bold text-slate-900">Preinscriptions</h1>
            <p class="mt-1 text-xs text-slate-500">Consultez et instruisez les demandes d'hebergement.</p>
        </div>
        <div class="rounded-xl bg-emerald-50 px-4 py-3 text-right">
            <div class="text-2xl font-extrabold text-emerald-700">{{ $statutsCount['total'] }}</div>
            <div class="text-[11px] font-semibold uppercase tracking-wider text-emerald-600">Dossiers au total</div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-8">
        @foreach([
            'soumis' => ['label' => 'Soumis', 'class' => 'text-blue-700 bg-blue-50 border-blue-100'],
            'complet' => ['label' => 'Complets', 'class' => 'text-emerald-700 bg-emerald-50 border-emerald-100'],
            'incomplet' => ['label' => 'Incomplets', 'class' => 'text-amber-700 bg-amber-50 border-amber-100'],
            'en_commission' => ['label' => 'Commission', 'class' => 'text-indigo-700 bg-indigo-50 border-indigo-100'],
            'admis' => ['label' => 'Admis', 'class' => 'text-teal-700 bg-teal-50 border-teal-100'],
            'liste_attente' => ['label' => 'Attente', 'class' => 'text-orange-700 bg-orange-50 border-orange-100'],
            'refuse' => ['label' => 'Refuses', 'class' => 'text-rose-700 bg-rose-50 border-rose-100'],
        ] as $key => $status)
            <div class="rounded-xl border p-3 {{ $status['class'] }}">
                <div class="text-xl font-bold">{{ $statutsCount[$key] }}</div>
                <div class="text-[10px] font-semibold uppercase tracking-wide">{{ $status['label'] }}</div>
            </div>
        @endforeach
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('dossiers.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-5">
            <div class="lg:col-span-2">
                <label for="search" class="mb-1 block text-xs font-semibold text-slate-600">Rechercher</label>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                    <input id="search" name="search" type="search" value="{{ request('search') }}" placeholder="Nom, email, CIN ou numero..." class="block w-full rounded-xl border border-slate-300 py-2 pl-9 pr-3 text-xs text-slate-800 focus:border-emerald-500 focus:ring-emerald-500">
                </div>
            </div>
            <div>
                <label for="internat_id" class="mb-1 block text-xs font-semibold text-slate-600">Internat</label>
                <select id="internat_id" name="internat_id" class="block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-800 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Tous les internats</option>
                    @foreach($internats as $internat)
                        <option value="{{ $internat->id }}" {{ request('internat_id') == $internat->id ? 'selected' : '' }}>{{ $internat->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="niveau_id" class="mb-1 block text-xs font-semibold text-slate-600">Niveau</label>
                <select id="niveau_id" name="niveau_id" class="block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-800 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Tous les niveaux</option>
                    @foreach($niveaux as $niveau)
                        <option value="{{ $niveau->id }}" {{ request('niveau_id') == $niveau->id ? 'selected' : '' }}>{{ $niveau->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="statut" class="mb-1 block text-xs font-semibold text-slate-600">Statut</label>
                <select id="statut" name="statut" class="block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-800 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Tous les statuts</option>
                    @foreach(['soumis', 'complet', 'incomplet', 'en_commission', 'admis', 'liste_attente', 'refuse'] as $statut)
                        <option value="{{ $statut }}" {{ request('statut') === $statut ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $statut)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2 lg:col-span-5">
                <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">
                    <i class="fa-solid fa-filter mr-1"></i> Filtrer
                </button>
                <a href="{{ route('dossiers.index') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">Reinitialiser</a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Dossiers recents</h2>
                <p class="mt-1 text-xs text-slate-500">{{ $dossiers->total() }} resultat(s)</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-left text-xs">
                <thead class="border-b border-slate-200 bg-slate-50 text-[10px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Dossier</th>
                        <th class="px-5 py-3 font-semibold">Candidat</th>
                        <th class="px-5 py-3 font-semibold">Formation</th>
                        <th class="px-5 py-3 font-semibold">Internat</th>
                        <th class="px-5 py-3 font-semibold">Statut</th>
                        <th class="px-5 py-3 text-right font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($dossiers as $dossier)
                        @php
                            $profile = $dossier->candidat?->candidatProfile;
                            $statusClass = match ($dossier->statut) {
                                'admis', 'complet' => 'bg-emerald-100 text-emerald-800',
                                'refuse' => 'bg-rose-100 text-rose-800',
                                'incomplet', 'liste_attente' => 'bg-amber-100 text-amber-800',
                                default => 'bg-blue-100 text-blue-800',
                            };
                        @endphp
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <div class="font-bold text-slate-900">{{ $dossier->numero_dossier }}</div>
                                <div class="mt-1 text-[11px] text-slate-400">{{ $dossier->date_depot?->format('d/m/Y H:i') ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-semibold text-slate-800">{{ $dossier->candidat?->name ?? 'Candidat inconnu' }}</div>
                                <div class="mt-1 text-[11px] text-slate-500">{{ $dossier->candidat?->email ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                <div>{{ $profile?->niveau?->nom ?? 'Non renseigne' }}</div>
                                <div class="mt-1 text-[11px] text-slate-400">{{ $profile?->filiere?->nom ?? 'Filiere non renseignee' }}</div>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $dossier->internat?->nom ?? 'Non affecte' }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-bold uppercase {{ $statusClass }}">{{ $dossier->statut_label }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('dossiers.show', $dossier->id) }}" class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-2 font-semibold text-slate-700 transition hover:bg-emerald-100 hover:text-emerald-700">
                                    Ouvrir <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-500">
                                <i class="fa-solid fa-folder-open mb-2 text-2xl text-slate-300"></i>
                                <div>Aucun dossier ne correspond aux filtres.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($dossiers->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">
                {{ $dossiers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
