@extends('layouts.candidat')

@section('content')
<div class="space-y-6">

    <!-- Bannière de bienvenue -->
    <div class="bg-gradient-to-r from-rose-600 via-pink-600 to-purple-700 rounded-2xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-xs font-semibold mb-3 border border-white/20">
                <i class="fa-solid fa-graduation-cap"></i> Espace Hébergement OFPPT
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                Bonjour, {{ $user->name }} !
            </h1>
            <p class="text-sm text-rose-100 max-w-2xl mt-1">
                Suivez ici l'état d'instruction de votre demande d'hébergement en Maison des Stagiaires, consultez votre affectation de chambre et déposez vos éventuelles réclamations.
            </p>
        </div>
        <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl pointer-events-none">
            <i class="fa-solid fa-hotel"></i>
        </div>
    </div>

    @if(!$dossier)
        <!-- Aucun dossier déposé -->
        <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center shadow-sm">
            <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 mx-auto flex items-center justify-center text-2xl mb-4">
                <i class="fa-solid fa-folder-plus"></i>
            </div>
            <h2 class="text-lg font-bold text-slate-900">Aucun dossier de préinscription soumis</h2>
            <p class="text-sm text-slate-500 max-w-md mx-auto mt-1">
                @if($campagneActive)
                    La campagne d'hébergement <strong>« {{ $campagneActive->titre }} »</strong> est actuellement ouverte jusqu'au {{ $campagneActive->date_fin->format('d/m/Y') }}.
                @else
                    La prochaine campagne d'hébergement sera ouverte prochainement.
                @endif
            </p>

            @if($campagneActive)
            <div class="mt-6">
                <a href="{{ route('candidat.preinscription') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-sm shadow-lg shadow-rose-600/30 transition">
                    <i class="fa-solid fa-file-pen"></i> Déposer ma demande de préinscription en ligne
                </a>
            </div>
            @endif
        </div>
    @else
        <!-- Dossier Déposé : Timeline d'avancement -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Demande d'hébergement</span>
                    <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                        <span>Dossier N° {{ $dossier->numero_dossier }}</span>
                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-bold border {{ $dossier->statut_badge_class }}">
                            {{ $dossier->statut_label }}
                        </span>
                    </h2>
                    <div class="text-xs text-slate-500 mt-0.5">
                        Déposé le {{ $dossier->date_depot->format('d/m/Y à H:i') }} • {{ $dossier->internat->nom }}
                    </div>
                </div>

                @if($dossier->statut === 'admis')
                <a href="{{ route('reports.fiche_hebergement', $dossier->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 font-semibold text-xs transition">
                    <i class="fa-solid fa-print"></i> Imprimer ma Fiche d'Hébergement
                </a>
                @endif
            </div>

            <!-- Timeline Visuelle en 4 étapes -->
            <div class="py-4">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 relative">
                    <!-- Étape 1 : Dépôt -->
                    <div class="flex items-center sm:flex-col text-left sm:text-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-sm shadow-md">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-900">1. Dossier Reçu</div>
                            <div class="text-[11px] text-slate-500">{{ $dossier->date_depot->format('d/m/Y') }}</div>
                        </div>
                    </div>

                    <!-- Étape 2 : Instruction pièces -->
                    <div class="flex items-center sm:flex-col text-left sm:text-center gap-3">
                        <div class="w-10 h-10 rounded-full {{ in_array($dossier->statut, ['complet', 'en_commission', 'admis', 'liste_attente', 'refuse']) ? 'bg-emerald-500 text-white' : ($dossier->statut === 'incomplet' ? 'bg-orange-500 text-white' : 'bg-slate-200 text-slate-600') }} flex items-center justify-center font-bold text-sm shadow-md">
                            <i class="fa-solid {{ $dossier->statut === 'incomplet' ? 'fa-exclamation' : 'fa-check' }}"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-900">2. Instruction des pièces</div>
                            <div class="text-[11px] text-slate-500">{{ $dossier->statut === 'incomplet' ? 'Complément requis' : 'Vérifié' }}</div>
                        </div>
                    </div>

                    <!-- Étape 3 : Commission & Calcul Score -->
                    <div class="flex items-center sm:flex-col text-left sm:text-center gap-3">
                        <div class="w-10 h-10 rounded-full {{ in_array($dossier->statut, ['admis', 'liste_attente', 'refuse']) ? 'bg-emerald-500 text-white' : 'bg-indigo-500 text-white animate-pulse' }} flex items-center justify-center font-bold text-sm shadow-md">
                            <i class="fa-solid fa-scale-balanced"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-900">3. Commission de Sélection</div>
                            <div class="text-[11px] text-slate-500">Score : <strong>{{ $dossier->score_total }} pts</strong></div>
                        </div>
                    </div>

                    <!-- Étape 4 : Décision & Admission -->
                    <div class="flex items-center sm:flex-col text-left sm:text-center gap-3">
                        <div class="w-10 h-10 rounded-full {{ $dossier->statut === 'admis' ? 'bg-emerald-500 text-white' : ($dossier->statut === 'liste_attente' ? 'bg-amber-500 text-white' : ($dossier->statut === 'refuse' ? 'bg-rose-500 text-white' : 'bg-slate-200 text-slate-400')) }} flex items-center justify-center font-bold text-sm shadow-md">
                            <i class="fa-solid {{ $dossier->statut === 'admis' ? 'fa-bed' : ($dossier->statut === 'liste_attente' ? 'fa-clock' : 'fa-hourglass-half') }}"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-900">4. Décision Finale</div>
                            <div class="text-[11px] text-slate-500 font-semibold">{{ $dossier->statut_label }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Motif de décision ou instructions -->
            @if($dossier->motif_decision)
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                <span class="font-bold text-slate-700">Décision de la commission :</span>
                <p class="text-slate-600 mt-0.5">{{ $dossier->motif_decision }}</p>
            </div>
            @endif
        </div>

        <!-- Si admis : Détails de l'Affectation en Chambre (F3.2, 4.1) -->
        @if($dossier->statut === 'admis')
        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl border border-emerald-200 p-6 shadow-sm space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg shadow-md shadow-emerald-600/20">
                    <i class="fa-solid fa-key"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-emerald-950">Votre Affectation en Hébergement</h3>
                    <p class="text-xs text-emerald-700">Détails de votre chambre, lit et consignes d'arrivée</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mt-2">
                <div class="bg-white p-4 rounded-xl border border-emerald-100 shadow-xs">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase">Internat</span>
                    <div class="text-sm font-bold text-slate-900 mt-0.5">{{ $dossier->internat->nom }}</div>
                    <div class="text-xs text-slate-500">{{ $dossier->internat->etablissement->ville }}</div>
                </div>

                <div class="bg-white p-4 rounded-xl border border-emerald-100 shadow-xs">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase">Bâtiment</span>
                    <div class="text-sm font-bold text-slate-900 mt-0.5">
                        {{ $dossier->lit?->chambre?->batiment?->nom ?? 'Attribution en cours' }}
                    </div>
                    <div class="text-xs text-slate-500">
                        {{ $dossier->lit?->chambre?->batiment?->genre === 'garcons' ? 'Résidence Garçons' : 'Résidence Filles' }}
                    </div>
                </div>

                <div class="bg-white p-4 rounded-xl border border-emerald-100 shadow-xs">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase">Chambre & Étage</span>
                    <div class="text-sm font-bold text-slate-900 mt-0.5">
                        {{ $dossier->lit?->chambre?->numero ?? 'CH-En cours' }}
                    </div>
                    <div class="text-xs text-slate-500">
                        Étage {{ $dossier->lit?->chambre?->etage ?? 0 }} • {{ ucfirst($dossier->lit?->chambre?->type ?? 'Standard') }}
                    </div>
                </div>

                <div class="bg-white p-4 rounded-xl border border-emerald-100 shadow-xs">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase">Numéro de Lit</span>
                    <div class="text-sm font-bold text-emerald-700 mt-0.5">
                        {{ $dossier->lit?->code ?? 'En cours d’attribution' }}
                    </div>
                    <div class="text-xs text-slate-500">
                        Position : {{ ucfirst($dossier->lit?->position ?? 'Standard') }}
                    </div>
                </div>
            </div>

            <!-- Dates d'arrivée & Règlement intérieur -->
            <div class="bg-white p-4 rounded-xl border border-emerald-100 text-xs space-y-2">
                <div class="flex items-center gap-2 text-slate-800 font-bold">
                    <i class="fa-solid fa-calendar-check text-emerald-600"></i>
                    Date d'arrivée prévue :
                    <span class="text-emerald-700">{{ $dossier->internat->date_arrivee_prevue ? $dossier->internat->date_arrivee_prevue->format('d/m/Y') : 'Dès ouverture officielle' }}</span>
                </div>
                <div class="text-slate-600 leading-relaxed">
                    <strong>Règlement intérieur :</strong> {{ $dossier->internat->reglement_interieur ?? "Respect absolu du calme, couvre-feu à 22h00, port du badge obligatoire." }}
                </div>
            </div>
        </div>
        @endif

        <!-- Pièces Justificatives Téléversées -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-folder-closed text-slate-400"></i> Pièces Justificatives Déposées
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($dossier->docs as $doc)
                <div class="flex items-center justify-between p-3.5 rounded-xl border {{ $doc->is_valid === true ? 'bg-emerald-50/60 border-emerald-200' : ($doc->is_valid === false ? 'bg-rose-50/60 border-rose-200' : 'bg-slate-50 border-slate-200') }} text-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg {{ $doc->is_valid === true ? 'bg-emerald-500 text-white' : ($doc->is_valid === false ? 'bg-rose-500 text-white' : 'bg-slate-300 text-slate-700') }} flex items-center justify-center font-bold">
                            <i class="fa-regular fa-file-lines"></i>
                        </div>
                        <div>
                            <div class="font-bold text-slate-900">{{ $doc->docType->nom }}</div>
                            <div class="text-[11px] text-slate-500">{{ $doc->original_name }}</div>
                            @if($doc->admin_message)
                                <div class="text-[11px] text-rose-600 font-semibold mt-0.5"><i class="fa-solid fa-comment-dots mr-1"></i>{{ $doc->admin_message }}</div>
                            @endif
                        </div>
                    </div>
                    <div>
                        @if($doc->is_valid === true)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Validé</span>
                        @elseif($doc->is_valid === false)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">Rejeté</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700">En cours d'étude</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Réclamations & Recours (F2.9) -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-circle-question text-rose-600"></i> Réclamations, Recours & Incidents
                    </h3>
                    <p class="text-xs text-slate-500">Signalez un problème ou déposez un recours auprès de l'administration</p>
                </div>
            </div>

            <!-- Formulaire de signalement -->
            <form method="POST" action="{{ route('candidat.reclamation.store') }}" class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700">Type de demande *</label>
                        <select name="type" required class="mt-1 block w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs">
                            <option value="reclamation">Recours / Réclamation sur la sélection</option>
                            <option value="maintenance_chambre">Demande de maintenance de chambre</option>
                            <option value="incident_discipline">Incident de vie en collectivité</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700">Priorité *</label>
                        <select name="priorite" required class="mt-1 block w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs">
                            <option value="moyenne">Moyenne</option>
                            <option value="haute">Haute</option>
                            <option value="urgente">Urgente</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700">Objet de la demande *</label>
                    <input name="titre" type="text" required placeholder="Ex: Précision sur les revenus, Panne éclairage..." class="mt-1 block w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700">Description détaillée *</label>
                    <textarea name="description" rows="2" required placeholder="Expliquez clairement votre situation..." class="mt-1 block w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs"></textarea>
                </div>

                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-semibold text-xs rounded-lg transition shadow-sm">
                    Envoyer ma réclamation / signalement
                </button>
            </form>

            <!-- Historique des réclamations -->
            @if($mesReclamations->count() > 0)
            <div class="space-y-2 pt-2">
                <div class="text-xs font-bold text-slate-700">Historique de vos demandes :</div>
                @foreach($mesReclamations as $rec)
                <div class="p-3 rounded-xl bg-white border border-slate-200 text-xs space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-900">{{ $rec->titre }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $rec->statut === 'traite' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ ucfirst($rec->statut) }}
                        </span>
                    </div>
                    <p class="text-slate-600 text-[11px]">{{ $rec->description }}</p>
                    @if($rec->reponse_gestionnaire)
                        <div class="mt-2 p-2 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900 text-[11px]">
                            <strong>Réponse de l'administration :</strong> {{ $rec->reponse_gestionnaire }}
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    @endif

</div>
@endsection
