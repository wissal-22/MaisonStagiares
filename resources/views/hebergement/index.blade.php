@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:flex-row lg:items-center">
        <div>
            <div class="mb-1 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-sky-600">
                <i class="fa-solid fa-bed"></i> Gestion de l'hebergement
            </div>
            <h1 class="text-2xl font-bold text-slate-900">Plan des chambres et des lits</h1>
            <p class="mt-1 text-xs text-slate-500">Affectez les candidats admis et suivez l'occupation de l'internat.</p>
        </div>
        <form method="GET" action="{{ route('hebergement.index') }}" class="flex items-center gap-2">
            <label for="internat_id" class="text-xs font-semibold text-slate-600">Internat</label>
            <select id="internat_id" name="internat_id" onchange="this.form.submit()" class="rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-sky-500 focus:ring-sky-500">
                @foreach($internats as $item)
                    <option value="{{ $item->id }}" {{ (string) $selectedInternatId === (string) $item->id ? 'selected' : '' }}>{{ $item->nom }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if($internat)
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><div class="text-2xl font-extrabold text-slate-900">{{ $internat->capacite_totale }}</div><div class="mt-1 text-[10px] font-semibold uppercase tracking-wider text-slate-500">Capacite totale</div></div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4"><div class="text-2xl font-extrabold text-emerald-700">{{ $internat->places_occupees }}</div><div class="mt-1 text-[10px] font-semibold uppercase tracking-wider text-emerald-600">Places occupees</div></div>
            <div class="rounded-xl border border-sky-200 bg-sky-50 p-4"><div class="text-2xl font-extrabold text-sky-700">{{ $internat->places_disponibles }}</div><div class="mt-1 text-[10px] font-semibold uppercase tracking-wider text-sky-600">Places disponibles</div></div>
            <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4"><div class="text-2xl font-extrabold text-indigo-700">{{ $internat->taux_occupation }}%</div><div class="mt-1 text-[10px] font-semibold uppercase tracking-wider text-indigo-600">Taux d'occupation</div></div>
        </div>

        <div class="flex flex-col justify-between gap-4 rounded-2xl border border-sky-200 bg-sky-50 p-5 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-base font-bold text-sky-900">Affectation automatique</h2>
                <p class="mt-1 text-xs text-sky-700">Attribue les lits libres aux candidats admis selon leur sexe et les disponibilites.</p>
            </div>
            <form method="POST" action="{{ route('hebergement.auto_affecter') }}">
                @csrf
                <input type="hidden" name="internat_id" value="{{ $internat->id }}">
                <button type="submit" class="rounded-xl bg-sky-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-sky-700">
                    <i class="fa-solid fa-wand-magic-sparkles mr-1"></i> Affecter automatiquement
                </button>
            </form>
        </div>

        @if($admisSansLit->isNotEmpty())
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-amber-900">Admis en attente d'un lit</h2>
                        <p class="mt-1 text-xs text-amber-700">{{ $admisSansLit->count() }} candidat(s) doivent encore etre affectes.</p>
                    </div>
                    <i class="fa-solid fa-user-clock text-xl text-amber-600"></i>
                </div>
                <div class="space-y-3">
                    @foreach($admisSansLit as $dossier)
                        <form method="POST" action="{{ route('hebergement.affecter_lit') }}" class="flex flex-col justify-between gap-3 rounded-xl border border-amber-200 bg-white p-4 sm:flex-row sm:items-center">
                            @csrf
                            <input type="hidden" name="dossier_id" value="{{ $dossier->id }}">
                            <div>
                                <div class="text-xs font-bold text-slate-900">{{ $dossier->candidat?->name ?? 'Candidat inconnu' }}</div>
                                <div class="mt-1 text-[11px] text-slate-500">{{ $dossier->numero_dossier }} · Sexe : {{ $dossier->candidat?->candidatProfile?->genre === 'fille' ? 'Fille' : 'Garcon' }}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <select name="lit_id" required class="rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-amber-500 focus:ring-amber-500">
                                    <option value="">Choisir un lit libre</option>
                                    @foreach($litsLibres as $lit)
                                        <option value="{{ $lit->id }}">{{ $lit->code }} - Chambre {{ $lit->chambre?->numero }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="rounded-lg bg-amber-600 px-3 py-2 text-xs font-semibold text-white hover:bg-amber-700">Affecter</button>
                            </div>
                        </form>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Plan des chambres</h2>
                    <p class="mt-1 text-xs text-slate-500">{{ $internat->batiments->count() }} batiment(s) · {{ $internat->chambres->count() }} chambre(s)</p>
                </div>
                <a href="{{ route('hebergement.chambres', ['internat_id' => $internat->id]) }}" class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200">Gerer les chambres</a>
            </div>

            <div class="space-y-6">
                @forelse($internat->batiments as $batiment)
                    <div>
                        <div class="mb-3 flex items-center gap-2 border-b border-slate-100 pb-2">
                            <i class="fa-solid fa-building text-slate-400"></i>
                            <h3 class="text-sm font-bold text-slate-800">{{ $batiment->nom }}</h3>
                            <span class="text-[11px] text-slate-400">{{ $batiment->code }}</span>
                        </div>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            @foreach($batiment->chambres as $chambre)
                                <div class="rounded-xl border border-slate-200 p-4 {{ $chambre->statut === 'complet' ? 'bg-rose-50' : 'bg-slate-50' }}">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="font-bold text-slate-900">Chambre {{ $chambre->numero }}</div>
                                        <span class="text-[10px] font-semibold uppercase text-slate-500">{{ $chambre->type }}</span>
                                    </div>
                                    <div class="mt-3 grid grid-cols-2 gap-2 text-center text-[10px]">
                                        <div class="rounded-lg bg-emerald-100 px-2 py-2 text-emerald-800"><strong class="block text-base">{{ $chambre->lits_libres_count }}</strong>libres</div>
                                        <div class="rounded-lg bg-slate-200 px-2 py-2 text-slate-700"><strong class="block text-base">{{ $chambre->lits_occupees_count }}</strong>occupes</div>
                                    </div>
                                    <div class="mt-3 space-y-1.5">
                                        @foreach($chambre->lits as $lit)
                                            <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-white px-2.5 py-2 text-[11px]">
                                                <span class="font-semibold text-slate-700"><i class="fa-solid fa-bed mr-1 text-slate-400"></i>{{ $lit->code }}</span>
                                                @if($lit->dossierActif)
                                                    <form method="POST" action="{{ route('hebergement.liberer_lit', $lit->id) }}">
                                                        @csrf
                                                        <button type="submit" class="text-rose-600 hover:text-rose-800" title="Liberer le lit"><i class="fa-solid fa-arrow-right-from-bracket"></i></button>
                                                    </form>
                                                @else
                                                    <span class="text-emerald-600"><i class="fa-solid fa-circle-check"></i></span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    <a href="{{ route('hebergement.chambres.fiche', $chambre->id) }}" class="mt-3 block text-center text-[11px] font-semibold text-sky-600 hover:underline">Voir la fiche</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="py-8 text-center text-xs text-slate-500">Aucun batiment configure pour cet internat.</p>
                @endforelse
            </div>
        </div>
    @else
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-center text-sm text-amber-800">Aucun internat disponible.</div>
    @endif
</div>
@endsection
