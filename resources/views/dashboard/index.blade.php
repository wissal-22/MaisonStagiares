@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- En-tête du tableau de bord & Filtre Internat -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-emerald-600 uppercase tracking-wider mb-1">
                <i class="fa-solid fa-gauge-high"></i> Pilotage Opérationnel & Décisionnel
            </div>
            <h1 class="text-2xl font-bold text-slate-900">
                Tableau de Bord
                @if($selectedInternat)
                    <span class="text-slate-400 font-normal text-lg">• {{ $selectedInternat->nom }}</span>
                @else
                    <span class="text-slate-400 font-normal text-lg">• Synthèse Nationale</span>
                @endif
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Statistiques en temps réel sur les admissions, l'hébergement, la restauration et les présences.
            </p>
        </div>

        <!-- Filtre Internat -->
        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
            <label for="internat_id" class="text-xs font-semibold text-slate-600 whitespace-nowrap">Filtrer par internat :</label>
            <select id="internat_id" name="internat_id" onchange="this.form.submit()" class="bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-xl px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                <option value="">Tous les internats (Vue consolidée)</option>
                @foreach($internats as $int)
                    <option value="{{ $int->id }}" {{ $selectedInternat?->id == $int->id ? 'selected' : '' }}>
                        {{ $int->nom }} ({{ $int->etablissement->ville }})
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Alertes contextuelles -->
    @if(count($alertes) > 0)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($alertes as $alt)
        <div class="flex items-start gap-3 p-4 rounded-xl border {{ $alt['type'] === 'warning' ? 'bg-amber-50 border-amber-200 text-amber-900' : ($alt['type'] === 'rose' ? 'bg-rose-50 border-rose-200 text-rose-900' : 'bg-blue-50 border-blue-200 text-blue-900') }} shadow-sm">
            <div class="mt-0.5">
                <i class="fa-solid {{ $alt['type'] === 'warning' ? 'fa-triangle-exclamation text-amber-500' : ($alt['type'] === 'rose' ? 'fa-bell text-rose-500' : 'fa-circle-info text-blue-500') }} text-lg"></i>
            </div>
            <div>
                <div class="text-xs font-bold uppercase tracking-wider">{{ $alt['titre'] }}</div>
                <div class="text-xs mt-0.5">{{ $alt['message'] }}</div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Cartes KPI Majeures (4 Métriques Clés) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

        <!-- Taux d'occupation -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Taux d'Occupation</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-bed"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $tauxOccupation }}%</span>
                <span class="text-xs font-medium text-slate-500">({{ $litsOccupes }} / {{ $totalLits }} lits)</span>
            </div>
            <div class="w-full bg-slate-100 h-2 rounded-full mt-3 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-full rounded-full transition-all duration-500" style="width: {{ min(100, $tauxOccupation) }}%"></div>
            </div>
            <div class="mt-2.5 flex items-center justify-between text-[11px] text-slate-500">
                <span>Disponibles : <strong class="text-emerald-600 font-bold">{{ $litsLibres }} places</strong></span>
                <a href="{{ route('hebergement.index') }}" class="text-emerald-600 hover:underline font-semibold">Gérer &rarr;</a>
            </div>
        </div>

        <!-- Admissions & Candidatures -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Dossiers & Admissions</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $dossiersAdmis }}</span>
                <span class="text-xs font-medium text-slate-500">admis sur {{ $totalDossiers }} dossiers</span>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-2 text-[11px]">
                <div class="p-1.5 rounded-lg bg-slate-50 border border-slate-100">
                    <span class="text-slate-500">En délibération :</span>
                    <strong class="block text-indigo-600 font-bold">{{ $dossiersEnCommission }}</strong>
                </div>
                <div class="p-1.5 rounded-lg bg-slate-50 border border-slate-100">
                    <span class="text-slate-500">Liste d'attente :</span>
                    <strong class="block text-amber-600 font-bold">{{ $dossiersAttente }}</strong>
                </div>
            </div>
            <div class="mt-2 text-right">
                <a href="{{ route('deliberation.index') }}" class="text-blue-600 hover:underline text-[11px] font-semibold">Commission &rarr;</a>
            </div>
        </div>

        <!-- Restauration Quotidienne -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Restauration Aujourd'hui</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-utensils"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $effectifJourPrevu }}</span>
                <span class="text-xs font-medium text-slate-500">convives à nourrir</span>
            </div>
            <div class="mt-3 space-y-1 text-[11px] text-slate-600">
                <div class="flex justify-between">
                    <span>Régimes particuliers :</span>
                    <strong class="text-amber-700 font-bold">{{ $regimesSpeciauxCount }} stagiaires</strong>
                </div>
                <div class="flex justify-between">
                    <span>Budget journalier :</span>
                    <strong class="text-slate-900 font-bold">{{ number_format($coutJourTotal, 2) }} DH</strong>
                </div>
            </div>
            <div class="mt-2 text-right">
                <a href="{{ route('restauration.index') }}" class="text-amber-600 hover:underline text-[11px] font-semibold">Commandes &rarr;</a>
            </div>
        </div>

        <!-- Incidents & Réclamations -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Tickets & Maintenance</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-wrench"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $incidentsOuverts }}</span>
                <span class="text-xs font-medium text-slate-500">en attente de traitement</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-3">
                Réclamations d'hébergement, signalements de pannes de literie/sanitaires ou discipline.
            </p>
            <div class="mt-2 text-right">
                <a href="{{ route('incidents.index') }}" class="text-rose-600 hover:underline text-[11px] font-semibold">Consulter &rarr;</a>
            </div>
        </div>

    </div>

    <!-- Grille Principale (Tableau des Régions & Mouvements récents) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Répartition Régionale / Consolidation -->
        <div class="lg:col-span-7 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-map-location-dot text-emerald-600"></i> Consolidation Régionale OFPPT
                    </h2>
                    <p class="text-xs text-slate-500">Capacités d'accueil et taux d'occupation par région administrative</p>
                </div>
                <span class="text-xs font-semibold px-2 py-1 rounded bg-slate-100 text-slate-600 border border-slate-200">
                    {{ $regionsStats->count() }} Régions
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider font-semibold border-y border-slate-200">
                        <tr>
                            <th class="py-3 px-3">Région</th>
                            <th class="py-3 px-3">Établissements</th>
                            <th class="py-3 px-3 text-right">Capacité</th>
                            <th class="py-3 px-3 text-right">Admis</th>
                            <th class="py-3 px-3 text-right">Taux</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($regionsStats as $rStat)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-3 font-semibold text-slate-900 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $rStat['taux'] > 80 ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                                {{ $rStat['region']->nom }}
                            </td>
                            <td class="py-3 px-3 text-slate-500">
                                {{ $rStat['region']->etablissements->pluck('nom')->join(', ') }}
                            </td>
                            <td class="py-3 px-3 text-right font-medium text-slate-700">{{ $rStat['capacite'] }} lits</td>
                            <td class="py-3 px-3 text-right font-bold text-slate-900">{{ $rStat['admis'] }}</td>
                            <td class="py-3 px-3 text-right">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold {{ $rStat['taux'] > 80 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ $rStat['taux'] }}%
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mouvements récents & Registre d'arrivée -->
        <div class="lg:col-span-5 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-blue-600"></i> Derniers Mouvements
                    </h2>
                    <p class="text-xs text-slate-500">Check-in, sorties et permutations de chambres</p>
                </div>
                <a href="{{ route('hebergement.mouvements') }}" class="text-xs font-semibold text-emerald-600 hover:underline">Tout voir</a>
            </div>

            <div class="space-y-3">
                @forelse($mouvementsRecents as $mvt)
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg {{ $mvt->type === 'arrivee' ? 'bg-emerald-100 text-emerald-700' : ($mvt->type === 'depart' ? 'bg-slate-200 text-slate-700' : 'bg-blue-100 text-blue-700') }} flex items-center justify-center font-bold">
                            <i class="fa-solid {{ $mvt->type === 'arrivee' ? 'fa-arrow-right-to-bracket' : ($mvt->type === 'depart' ? 'fa-arrow-right-from-bracket' : 'fa-arrows-rotate') }}"></i>
                        </div>
                        <div>
                            <div class="font-bold text-slate-900">{{ $mvt->dossier->candidat->name }}</div>
                            <div class="text-slate-500 text-[11px]">{{ $mvt->type_label }} • {{ $mvt->motif }}</div>
                        </div>
                    </div>
                    <div class="text-[10px] text-slate-400 font-medium">
                        {{ $mvt->date_mouvement->diffForHumans() }}
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-slate-400 text-xs">
                    Aucun mouvement enregistré récemment.
                </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Liste des Derniers Dossiers Déposés -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-file-lines text-indigo-600"></i> Dossiers de Préinscription Récents
                </h2>
                <p class="text-xs text-slate-500">Demandes déposées récemment en attente d'instruction ou de validation</p>
            </div>
            <a href="{{ route('dossiers.index') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">
                Gérer l'ensemble des dossiers &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider font-semibold border-y border-slate-200">
                    <tr>
                        <th class="py-3 px-3">N° Dossier</th>
                        <th class="py-3 px-3">Candidat</th>
                        <th class="py-3 px-3">CEF / CIN</th>
                        <th class="py-3 px-3">Niveau & Filière</th>
                        <th class="py-3 px-3 text-center">Score Social</th>
                        <th class="py-3 px-3">Statut</th>
                        <th class="py-3 px-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($dossiersRecents as $dos)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-3 font-mono font-bold text-slate-800">
                            {{ $dos->numero_dossier }}
                        </td>
                        <td class="py-3 px-3">
                            <div class="font-bold text-slate-900">{{ $dos->candidat->name }}</div>
                            <div class="text-[11px] text-slate-500">{{ $dos->candidat->candidatProfile?->ville_origine }} ({{ $dos->candidat->candidatProfile?->distance_km }} km)</div>
                        </td>
                        <td class="py-3 px-3 font-mono text-slate-600">
                            <div>{{ $dos->candidat->candidatProfile?->cef }}</div>
                            <div class="text-[10px] text-slate-400">CIN: {{ $dos->candidat->candidatProfile?->cin }}</div>
                        </td>
                        <td class="py-3 px-3">
                            <span class="inline-block px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold text-[10px]">
                                {{ $dos->candidat->candidatProfile?->niveau?->code }}
                            </span>
                            <div class="text-[11px] text-slate-600 truncate max-w-xs">{{ $dos->candidat->candidatProfile?->filiere?->nom }}</div>
                        </td>
                        <td class="py-3 px-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                {{ $dos->score_total }} pts
                            </span>
                        </td>
                        <td class="py-3 px-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $dos->statut_badge_class }}">
                                {{ $dos->statut_label }}
                            </span>
                        </td>
                        <td class="py-3 px-3 text-right">
                            <a href="{{ route('dossiers.show', $dos->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-semibold text-xs transition">
                                <i class="fa-regular fa-eye"></i> Instruire
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
