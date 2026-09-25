@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <div class="flex flex-col justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:flex-row sm:items-center">
        <div>
            <a href="{{ route('dossiers.index') }}" class="mb-3 inline-flex items-center gap-2 text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                <i class="fa-solid fa-arrow-left"></i> Retour aux dossiers
            </a>
            <h1 class="text-2xl font-bold text-slate-900">Dossier {{ $dossier->numero_dossier }}</h1>
            <p class="mt-1 text-xs text-slate-500">Depose le {{ $dossier->date_depot?->format('d/m/Y a H:i') ?? '-' }}</p>
        </div>
        <span class="inline-flex self-start rounded-full px-3 py-1 text-xs font-bold {{ $dossier->statut_badge_class }}">{{ $dossier->statut_label }}</span>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 flex items-center gap-2 text-base font-bold text-slate-900"><i class="fa-solid fa-user text-emerald-600"></i> Candidat</h2>
            <dl class="space-y-3 text-xs">
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Nom</dt><dd class="font-semibold text-slate-900">{{ $dossier->candidat?->name ?? '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">E-mail</dt><dd class="font-semibold text-slate-900">{{ $dossier->candidat?->email ?? '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">CIN</dt><dd class="font-semibold text-slate-900">{{ $dossier->candidat?->candidatProfile?->cin ?? 'Non renseigne' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">CEF</dt><dd class="font-semibold text-slate-900">{{ $dossier->candidat?->candidatProfile?->cef ?? 'Non renseigne' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Formation</dt><dd class="text-right font-semibold text-slate-900">{{ $dossier->candidat?->candidatProfile?->niveau?->nom ?? '-' }}<br>{{ $dossier->candidat?->candidatProfile?->filiere?->nom ?? '-' }}</dd></div>
            </dl>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 flex items-center gap-2 text-base font-bold text-slate-900"><i class="fa-solid fa-building text-emerald-600"></i> Affectation</h2>
            <dl class="space-y-3 text-xs">
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Internat</dt><dd class="font-semibold text-slate-900">{{ $dossier->internat?->nom ?? 'Non affecte' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Etablissement</dt><dd class="font-semibold text-slate-900">{{ $dossier->internat?->etablissement?->nom ?? '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Campagne</dt><dd class="font-semibold text-slate-900">{{ $dossier->campagne?->titre ?? '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Score</dt><dd class="font-semibold text-slate-900">{{ $dossier->score_total ?? '-' }}</dd></div>
            </dl>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Pieces justificatives</h2>
                <p class="mt-1 text-xs text-slate-500">Validez ou rejetez chaque document transmis.</p>
            </div>
            <form method="POST" action="{{ route('dossiers.statut', $dossier->id) }}" class="flex items-center gap-2">
                @csrf
                <select name="statut" class="rounded-lg border border-slate-300 px-2 py-2 text-xs">
                    @foreach(['soumis', 'complet', 'incomplet', 'en_commission', 'admis', 'liste_attente', 'refuse'] as $statut)
                        <option value="{{ $statut }}" {{ $dossier->statut === $statut ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $statut)) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">Mettre a jour</button>
            </form>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($dossier->docs as $doc)
                <div class="flex flex-col justify-between gap-3 py-3 sm:flex-row sm:items-center">
                    <div>
                        <div class="text-xs font-semibold text-slate-900">{{ $doc->docType?->nom ?? 'Document' }}</div>
                        <div class="mt-1 text-[11px] text-slate-500">{{ $doc->original_name }}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="rounded-full px-2 py-1 text-[10px] font-semibold {{ $doc->is_valid === true ? 'bg-emerald-100 text-emerald-800' : ($doc->is_valid === false ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                            {{ $doc->is_valid === true ? 'Valide' : ($doc->is_valid === false ? 'Rejete' : 'En attente') }}
                        </span>
                        <form method="POST" action="{{ route('dossiers.docs.valider', [$dossier->id, $doc->id]) }}" class="flex gap-1">
                            @csrf
                            <input type="hidden" name="action" value="valider">
                            <button type="submit" title="Valider" class="rounded-lg bg-emerald-50 px-2 py-1.5 text-emerald-700 hover:bg-emerald-100"><i class="fa-solid fa-check"></i></button>
                        </form>
                        <form method="POST" action="{{ route('dossiers.docs.valider', [$dossier->id, $doc->id]) }}" class="flex gap-1">
                            @csrf
                            <input type="hidden" name="action" value="rejeter">
                            <button type="submit" title="Rejeter" class="rounded-lg bg-rose-50 px-2 py-1.5 text-rose-700 hover:bg-rose-100"><i class="fa-solid fa-xmark"></i></button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="py-6 text-center text-xs text-slate-500">Aucune piece justificative deposee.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
