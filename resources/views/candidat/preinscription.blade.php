@extends('layouts.candidat')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- En-tête -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-rose-600 uppercase tracking-wider mb-1">
                <i class="fa-solid fa-file-signature"></i> Campagne Active {{ $campagne->anneeScolaire->code }}
            </div>
            <h1 class="text-2xl font-bold text-slate-900">Dossier de Préinscription en Internat</h1>
            <p class="text-xs text-slate-500 mt-1">
                {{ $campagne->titre }} • Période de dépôt jusqu'au {{ $campagne->date_fin->format('d/m/Y') }}
            </p>
        </div>
        <div class="text-right">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                <i class="fa-solid fa-lock-open mr-1.5 text-emerald-600"></i> Dépôts Ouverts
            </span>
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
            <div class="font-bold mb-1">Veuillez corriger les erreurs suivantes :</div>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('candidat.preinscription.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <input type="hidden" name="campagne_id" value="{{ $campagne->id }}">

        <!-- Étape 1 : Choix de l'Internat Souhaité -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-rose-100 text-rose-700 text-xs flex items-center justify-center font-bold">1</span>
                Choix de la Maison des Stagiaires (Internat)
            </h2>
            <p class="text-xs text-slate-500">Sélectionnez la structure d'hébergement rattachée à votre établissement de formation :</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($internats as $int)
                <label class="relative flex p-4 rounded-xl border border-slate-200 hover:border-rose-400 bg-slate-50/50 hover:bg-rose-50/30 cursor-pointer transition">
                    <input type="radio" name="internat_id" value="{{ $int->id }}" {{ (old('internat_id', $profile?->user?->internat_id) == $int->id || $loop->first) ? 'checked' : '' }} class="text-rose-600 focus:ring-rose-500 mt-1">
                    <div class="ml-3">
                        <div class="text-xs font-bold text-slate-900">{{ $int->nom }}</div>
                        <div class="text-[11px] text-slate-500">{{ $int->etablissement->nom }} ({{ $int->etablissement->ville }})</div>
                        <div class="text-[10px] text-emerald-600 font-semibold mt-1">Capacité : {{ $int->capacite_totale }} places</div>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        <!-- Étape 2 : Identité & Informations Personnelles -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-rose-100 text-rose-700 text-xs flex items-center justify-center font-bold">2</span>
                Informations Personnelles & État Civil
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700">Nom et Prénom *</label>
                    <input type="text" disabled value="{{ $user->name }}" class="mt-1 block w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-xl text-slate-700 text-xs">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700">Identifiant CEF (Code Stagiaire OFPPT) *</label>
                    <input name="cef" type="text" required value="{{ old('cef', $profile?->cef) }}" placeholder="CEF-XXXX-XXXX" class="mt-1 block w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-rose-500 focus:border-rose-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700">Identifiant CIN (ou du père si mineur) *</label>
                    <input name="cin" type="text" required value="{{ old('cin', $profile?->cin) }}" placeholder="Ex: BK123456" class="mt-1 block w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-rose-500 focus:border-rose-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700">Le candidat est-il mineur ?</label>
                    <div class="mt-2 flex items-center gap-4 text-xs">
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="radio" name="is_mineur" value="0" {{ old('is_mineur', $profile?->is_mineur) ? '' : 'checked' }}> Non (Majeur)
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="radio" name="is_mineur" value="1" {{ old('is_mineur', $profile?->is_mineur) ? 'checked' : '' }}> Oui (Mineur)
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700">Date de Naissance *</label>
                    <input name="date_naissance" type="date" required value="{{ old('date_naissance', $profile?->date_naissance?->format('Y-m-d')) }}" class="mt-1 block w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-rose-500 focus:border-rose-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700">Genre *</label>
                    <select name="genre" required class="mt-1 block w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-rose-500 focus:border-rose-500">
                        <option value="garcon" {{ old('genre', $profile?->genre) === 'garcon' ? 'selected' : '' }}>Garçon (Résidence Masculine)</option>
                        <option value="fille" {{ old('genre', $profile?->genre) === 'fille' ? 'selected' : '' }}>Fille (Résidence Féminine)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700">Numéro de Téléphone *</label>
                    <input name="telephone" type="tel" required value="{{ old('telephone', $profile?->telephone ?? $user->telephone) }}" class="mt-1 block w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-rose-500 focus:border-rose-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700">Ville d'origine (Domicile familial) *</label>
                    <input name="ville_origine" type="text" required value="{{ old('ville_origine', $profile?->ville_origine) }}" placeholder="Ex: Tinghir, Zagora, Al Hoceima..." class="mt-1 block w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-rose-500 focus:border-rose-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700">Adresse complète du domicile familial *</label>
                    <input name="adresse" type="text" required value="{{ old('adresse', $profile?->adresse) }}" placeholder="Quartier, Rue, N°..." class="mt-1 block w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-rose-500 focus:border-rose-500">
                </div>
            </div>
        </div>

        <!-- Étape 3 : Niveau Pédagogique & Filière -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-rose-100 text-rose-700 text-xs flex items-center justify-center font-bold">3</span>
                Niveau & Filière de Formation OFPPT
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700">Niveau de Formation (à choisir parmi les 4 niveaux) *</label>
                    <select name="niveau_id" required class="mt-1 block w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-rose-500 focus:border-rose-500">
                        @foreach($niveaux as $niv)
                            <option value="{{ $niv->id }}" {{ old('niveau_id', $profile?->niveau_id) == $niv->id ? 'selected' : '' }}>
                                {{ $niv->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700">Filière suivie *</label>
                    <select name="filiere_id" required class="mt-1 block w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-rose-500 focus:border-rose-500">
                        @foreach($niveaux as $niv)
                            <optgroup label="{{ $niv->nom }}">
                                @foreach($niv->filieres as $fil)
                                    <option value="{{ $fil->id }}" {{ old('filiere_id', $profile?->filiere_id) == $fil->id ? 'selected' : '' }}>
                                        {{ $fil->nom }} ({{ $fil->code }})
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Étape 4 : Critères Socio-Économiques & Régime Alimentaire -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-rose-100 text-rose-700 text-xs flex items-center justify-center font-bold">4</span>
                Informations Socio-Économiques & Santé (Calcul du Score)
            </h2>
            <p class="text-xs text-slate-500">Ces informations déterminent le calcul automatique de votre score selon le barème national.</p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700">Éloignement kilométrique (en km) *</label>
                    <input name="distance_km" type="number" min="0" required value="{{ old('distance_km', $profile?->distance_km ?? 150) }}" class="mt-1 block w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-rose-500 focus:border-rose-500">
                    <span class="text-[10px] text-slate-400">Distance entre votre domicile et l'internat</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700">Revenu mensuel global des parents (DH) *</label>
                    <input name="revenu_mensuel_parents" type="number" step="100" min="0" required value="{{ old('revenu_mensuel_parents', $profile?->revenu_mensuel_parents ?? 2500) }}" class="mt-1 block w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-rose-500 focus:border-rose-500">
                    <span class="text-[10px] text-slate-400">Total cumulé des revenus déclarés</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700">Nombre de frères et sœurs à charge *</label>
                    <input name="nombre_freres_soeurs" type="number" min="0" required value="{{ old('nombre_freres_soeurs', $profile?->nombre_freres_soeurs ?? 2) }}" class="mt-1 block w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-rose-500 focus:border-rose-500">
                </div>
            </div>

            <!-- Situations particulières -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 bg-slate-50 text-xs cursor-pointer">
                    <input type="checkbox" name="est_orphelin" value="1" {{ old('est_orphelin', $profile?->est_orphelin) ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-rose-500">
                    <span>Candidat orphelin (père ou mère)</span>
                </label>

                <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 bg-slate-50 text-xs cursor-pointer">
                    <input type="checkbox" name="en_situation_handicap" value="1" {{ old('en_situation_handicap', $profile?->en_situation_handicap) ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-rose-500">
                    <span>En situation de handicap (PMR)</span>
                </label>

                <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 bg-slate-50 text-xs cursor-pointer">
                    <input type="checkbox" name="annee_precedente_heberge" value="1" {{ old('annee_precedente_heberge', $profile?->annee_precedente_heberge) ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-rose-500">
                    <span>Déjà hébergé l'année précédente (Reconduction)</span>
                </label>
            </div>

            <!-- Régime Alimentaire & Allergies -->
            <div class="pt-2">
                <label class="block text-xs font-semibold text-slate-700">Régime alimentaire particulier / Allergies déclarées</label>
                <input name="regime_alimentaire" type="text" value="{{ old('regime_alimentaire', $profile?->regime_alimentaire ?? 'Normal') }}" placeholder="Ex: Sans gluten, Végétarien, Allergie arachides..." class="mt-1 block w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-rose-500 focus:border-rose-500">
                <span class="text-[10px] text-slate-400">Ces informations seront directement transmises au service de restauration de l'internat.</span>
            </div>
        </div>

        <!-- Étape 5 : Téléversement des Pièces Justificatives (F1.3, F1.4) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-rose-100 text-rose-700 text-xs flex items-center justify-center font-bold">5</span>
                Téléversement des Pièces Justificatives
            </h2>
            <p class="text-xs text-slate-500">Formats acceptés : PDF, JPG, PNG (taille max: 4 Mo par document). Assurez-vous de la lisibilité des scans.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($docTypes as $doc)
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/70 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-900">{{ $doc->nom }}</span>
                        @if($doc->est_obligatoire)
                            <span class="text-[10px] font-bold text-rose-600 uppercase">Obligatoire</span>
                        @else
                            <span class="text-[10px] text-slate-400 uppercase">Facultatif</span>
                        @endif
                    </div>
                    <p class="text-[11px] text-slate-500">{{ $doc->description }}</p>
                    <input type="file" name="doc_{{ $doc->code }}" accept=".pdf,.jpg,.jpeg,.png" {{ $doc->est_obligatoire ? 'required' : '' }} class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 cursor-pointer">
                </div>
                @endforeach
            </div>
        </div>

        <!-- Validation & Soumission -->
        <div class="bg-slate-900 p-6 rounded-2xl text-white flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xl">
            <div>
                <div class="text-sm font-bold">Prêt à soumettre votre dossier ?</div>
                <div class="text-xs text-slate-400">Un numéro de dossier unique et un accusé de réception horodaté vous seront délivrés.</div>
            </div>
            <button type="submit" class="px-6 py-3 rounded-xl bg-gradient-to-r from-rose-600 to-pink-500 hover:from-rose-500 hover:to-pink-400 text-white font-bold text-sm shadow-lg shadow-rose-600/30 transition">
                <i class="fa-solid fa-paper-plane mr-2"></i> Valider et Déposer mon Dossier
            </button>
        </div>

    </form>

</div>
@endsection
