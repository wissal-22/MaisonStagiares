@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-1 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-emerald-600">
            <i class="fa-solid fa-sliders"></i> Paramétrage & référentiels
        </div>
        <h1 class="text-2xl font-bold text-slate-900">Référentiels et paramètres</h1>
        <p class="mt-1 text-xs text-slate-500">Gérez les campagnes, années scolaires, niveaux, filières et critères de sélection.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-base font-bold text-slate-900">Campagnes</h2>
            @if(isset($campagnes) && $campagnes->isNotEmpty())
                <div class="space-y-3">
                    @foreach($campagnes as $campagne)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <div class="text-sm font-semibold text-slate-900">{{ $campagne->titre }}</div>
                                    <div class="mt-1 text-[11px] text-slate-500">{{ $campagne->anneeScolaire?->code ?? 'N/A' }} · {{ $campagne->internat?->nom ?? 'Tous' }}</div>
                                </div>
                                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-bold uppercase text-emerald-800">{{ $campagne->statut }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-500">Aucune campagne n’a encore été créée.</p>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-base font-bold text-slate-900">Années scolaires</h2>
            @if(isset($annees) && $annees->isNotEmpty())
                <div class="flex flex-wrap gap-2">
                    @foreach($annees as $annee)
                        <span class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] font-semibold text-slate-700">
                            {{ $annee->code }}
                            @if($annee->est_active)
                                <span class="text-emerald-600">(active)</span>
                            @endif
                        </span>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-500">Aucune année scolaire.</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-base font-bold text-slate-900">Niveaux</h2>
            @if(isset($niveaux) && $niveaux->isNotEmpty())
                <div class="space-y-2">
                    @foreach($niveaux as $niveau)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm font-medium text-slate-700">
                            {{ $niveau->nom }} <span class="text-slate-400">({{ $niveau->code }})</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-500">Aucun niveau défini.</p>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-base font-bold text-slate-900">Filières</h2>
            @if(isset($filieres) && $filieres->isNotEmpty())
                <div class="space-y-2">
                    @foreach($filieres as $filiere)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm font-medium text-slate-700">
                            {{ $filiere->nom }} <span class="text-slate-400">({{ $filiere->code }})</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-500">Aucune filière enregistrée.</p>
            @endif
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-base font-bold text-slate-900">Critères de sélection</h2>
        @if(isset($criteres) && $criteres->isNotEmpty())
            <div class="space-y-3">
                @foreach($criteres as $critere)
                    <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">{{ $critere->nom }}</div>
                            <div class="text-[11px] text-slate-500">{{ $critere->code }} · {{ $critere->internat?->nom ?? 'Tous' }}</div>
                        </div>
                        <span class="rounded-full bg-slate-200 px-2.5 py-1 text-[10px] font-bold uppercase text-slate-700">{{ $critere->points_base }} pts</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-slate-500">Aucun critère enregistré.</p>
        @endif
    </div>
</div>
@endsection
