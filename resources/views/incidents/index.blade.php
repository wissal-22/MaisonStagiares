@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:flex-row lg:items-center">
        <div>
            <div class="mb-1 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-rose-600">
                <i class="fa-solid fa-triangle-exclamation"></i> Incidents et reclamations
            </div>
            <h1 class="text-2xl font-bold text-slate-900">Tickets de suivi</h1>
            <p class="mt-1 text-xs text-slate-500">Centralisez les reclamations, incidents disciplinaires et demandes de maintenance.</p>
        </div>
        <form method="GET" action="{{ route('incidents.index') }}" class="flex items-center gap-2">
            <label for="internat_id" class="text-xs font-semibold text-slate-600">Internat</label>
            <select id="internat_id" name="internat_id" onchange="this.form.submit()" class="rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-rose-500 focus:ring-rose-500">
                @foreach($internats as $item)
                    <option value="{{ $item->id }}" {{ (string) $selectedInternatId === (string) $item->id ? 'selected' : '' }}>{{ $item->nom }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        @foreach([
            'total' => ['label' => 'Total', 'class' => 'bg-slate-50 text-slate-800 border-slate-200'],
            'ouverts' => ['label' => 'Ouverts', 'class' => 'bg-rose-50 text-rose-800 border-rose-200'],
            'en_cours' => ['label' => 'En cours', 'class' => 'bg-amber-50 text-amber-800 border-amber-200'],
            'resolus' => ['label' => 'Traites', 'class' => 'bg-emerald-50 text-emerald-800 border-emerald-200'],
        ] as $key => $stat)
            <div class="rounded-xl border p-4 {{ $stat['class'] }}"><div class="text-2xl font-extrabold">{{ $stats[$key] }}</div><div class="mt-1 text-[10px] font-semibold uppercase tracking-wider">{{ $stat['label'] }}</div></div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-1 flex items-center gap-2 text-base font-bold text-slate-900"><i class="fa-solid fa-plus-circle text-rose-600"></i> Nouveau ticket</h2>
            <p class="mb-4 text-xs text-slate-500">Enregistrez un incident ou une demande de maintenance.</p>
            <form method="POST" action="{{ route('incidents.store') }}" class="space-y-3">
                @csrf
                <input type="hidden" name="internat_id" value="{{ $selectedInternatId }}">
                <div>
                    <label for="type" class="mb-1 block text-xs font-semibold text-slate-600">Type</label>
                    <select id="type" name="type" required class="block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-rose-500 focus:ring-rose-500">
                        <option value="reclamation">Reclamation / recours</option>
                        <option value="incident_discipline">Incident disciplinaire</option>
                        <option value="maintenance_chambre">Maintenance chambre</option>
                    </select>
                </div>
                <div>
                    <label for="priorite" class="mb-1 block text-xs font-semibold text-slate-600">Priorite</label>
                    <select id="priorite" name="priorite" required class="block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-rose-500 focus:ring-rose-500">
                        @foreach(['faible', 'moyenne', 'haute', 'urgente'] as $priorite)
                            <option value="{{ $priorite }}">{{ ucfirst($priorite) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="titre" class="mb-1 block text-xs font-semibold text-slate-600">Titre</label>
                    <input id="titre" name="titre" type="text" required maxlength="200" placeholder="Objet du ticket" class="block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-rose-500 focus:ring-rose-500">
                </div>
                <div>
                    <label for="chambre_id" class="mb-1 block text-xs font-semibold text-slate-600">Chambre concernee</label>
                    <select id="chambre_id" name="chambre_id" class="block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-rose-500 focus:ring-rose-500">
                        <option value="">Aucune chambre specifique</option>
                        @foreach($chambres as $chambre)
                            <option value="{{ $chambre->id }}">Chambre {{ $chambre->numero }} - {{ $chambre->batiment?->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="description" class="mb-1 block text-xs font-semibold text-slate-600">Description</label>
                    <textarea id="description" name="description" rows="4" required placeholder="Decrivez la situation..." class="block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-rose-500 focus:ring-rose-500"></textarea>
                </div>
                <button type="submit" class="w-full rounded-xl bg-rose-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-rose-700">Enregistrer le ticket</button>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
            <div class="flex flex-col justify-between gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Liste des tickets</h2>
                    <p class="mt-1 text-xs text-slate-500">Suivez les demandes et leur resolution.</p>
                </div>
                <form method="GET" action="{{ route('incidents.index') }}" class="flex gap-2">
                    <input type="hidden" name="internat_id" value="{{ $selectedInternatId }}">
                    <select name="type" class="rounded-lg border border-slate-300 px-2 py-2 text-[11px]">
                        <option value="">Tous les types</option>
                        <option value="reclamation" {{ request('type') === 'reclamation' ? 'selected' : '' }}>Reclamations</option>
                        <option value="incident_discipline" {{ request('type') === 'incident_discipline' ? 'selected' : '' }}>Discipline</option>
                        <option value="maintenance_chambre" {{ request('type') === 'maintenance_chambre' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                    <select name="statut" class="rounded-lg border border-slate-300 px-2 py-2 text-[11px]">
                        <option value="">Tous les statuts</option>
                        @foreach(['ouvert', 'en_cours', 'traite', 'rejete'] as $statut)
                            <option value="{{ $statut }}" {{ request('statut') === $statut ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $statut)) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-lg bg-slate-900 px-3 py-2 text-[11px] font-semibold text-white">Filtrer</button>
                </form>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($incidents as $incident)
                    <div class="p-5">
                        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-[10px] font-bold text-slate-400">#{{ $incident->id }}</span>
                                    <h3 class="text-sm font-bold text-slate-900">{{ $incident->titre }}</h3>
                                    <span class="rounded-full px-2 py-1 text-[10px] font-semibold {{ $incident->priorite === 'urgente' ? 'bg-rose-100 text-rose-800' : ($incident->priorite === 'haute' ? 'bg-orange-100 text-orange-800' : 'bg-slate-100 text-slate-700') }}">{{ ucfirst($incident->priorite) }}</span>
                                </div>
                                <p class="mt-2 text-xs leading-5 text-slate-600">{{ $incident->description }}</p>
                                <div class="mt-2 text-[11px] text-slate-400">{{ $incident->type_label }} · {{ $incident->user?->name ?? 'Utilisateur inconnu' }} · {{ $incident->created_at?->format('d/m/Y H:i') }}</div>
                            </div>
                            <span class="inline-flex self-start rounded-full px-2.5 py-1 text-[10px] font-bold uppercase {{ $incident->statut === 'ouvert' ? 'bg-rose-100 text-rose-800' : ($incident->statut === 'en_cours' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') }}">{{ ucfirst(str_replace('_', ' ', $incident->statut)) }}</span>
                        </div>
                        @if($incident->chambre)
                            <div class="mt-2 text-[11px] font-semibold text-slate-500"><i class="fa-solid fa-door-open mr-1"></i> Chambre {{ $incident->chambre->numero }} - {{ $incident->chambre->batiment?->nom }}</div>
                        @endif
                        <form method="POST" action="{{ route('incidents.traiter', $incident->id) }}" class="mt-4 grid grid-cols-1 gap-2 rounded-xl bg-slate-50 p-3 sm:grid-cols-[auto_1fr_auto]">
                            @csrf
                            <select name="statut" class="rounded-lg border border-slate-300 px-2 py-2 text-[11px]">
                                @foreach(['ouvert', 'en_cours', 'traite', 'rejete'] as $statut)
                                    <option value="{{ $statut }}" {{ $incident->statut === $statut ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $statut)) }}</option>
                                @endforeach
                            </select>
                            <input name="reponse_gestionnaire" type="text" required maxlength="1000" value="{{ $incident->reponse_gestionnaire }}" placeholder="Reponse du gestionnaire" class="rounded-lg border border-slate-300 px-3 py-2 text-[11px]">
                            <button type="submit" class="rounded-lg bg-slate-900 px-3 py-2 text-[11px] font-semibold text-white hover:bg-slate-700">Actualiser</button>
                        </form>
                    </div>
                @empty
                    <p class="px-5 py-12 text-center text-xs text-slate-500">Aucun ticket pour ces filtres.</p>
                @endforelse
            </div>
            @if($incidents->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">{{ $incidents->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
