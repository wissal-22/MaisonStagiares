<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion - Maison des Stagiaires OFPPT</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style> body { font-family: 'Instrument Sans', sans-serif; } </style>
</head>
<body class="h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950 text-slate-100">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white shadow-xl shadow-emerald-500/25 mb-4">
            <i class="fa-solid fa-hotel text-2xl"></i>
        </div>
        <h2 class="text-3xl font-extrabold tracking-tight text-white">
            Maison des Stagiaires
        </h2>
        <p class="mt-2 text-sm text-slate-400">
            Plateforme nationale de gestion des internats de l'OFPPT
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-4xl px-4">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Formulaire de Connexion -->
            <div class="lg:col-span-6 bg-slate-800/80 backdrop-blur-xl border border-slate-700/70 py-8 px-6 shadow-2xl rounded-2xl sm:px-8">
                <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-lock text-emerald-400 text-sm"></i> Connexion sécurisée
                </h3>

                @if($errors->any())
                    <div class="mb-5 p-4 rounded-xl bg-rose-950/60 border border-rose-800 text-rose-300 text-xs font-medium">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if(session('info'))
                    <div class="mb-5 p-4 rounded-xl bg-sky-950/60 border border-sky-800 text-sky-300 text-xs font-medium">
                        {{ session('info') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Adresse E-mail</label>
                        <div class="mt-1.5 relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-sm">
                                <i class="fa-regular fa-envelope"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" placeholder="nom@ofppt.ma"
                                class="block w-full pl-10 pr-3 py-2.5 bg-slate-900/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Mot de passe</label>
                        </div>
                        <div class="mt-1.5 relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-sm">
                                <i class="fa-solid fa-key"></i>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password" required value="password"
                                class="block w-full pl-10 pr-3 py-2.5 bg-slate-900/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-700 bg-slate-900 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs text-slate-400">Se souvenir de moi</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full flex justify-center items-center py-2.5 px-4 border border-transparent rounded-xl shadow-lg shadow-emerald-600/30 text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition">
                        Se connecter <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-slate-700/60 text-center">
                    <p class="text-xs text-slate-400">
                        Nouveau candidat à l'hébergement ?
                        <a href="{{ route('register') }}" class="font-semibold text-emerald-400 hover:text-emerald-300 ml-1">Créer un compte</a>
                    </p>
                </div>
            </div>

            <!-- Accès Rapide Démo & Présentation des Acteurs (RBAC) -->
            <div class="lg:col-span-6 space-y-4">
                <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-5 backdrop-blur-md">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-users-gear text-emerald-400"></i> Accès rapide démo par profil (1 clic)
                        </h4>
                        <span class="text-[10px] bg-emerald-500/20 text-emerald-300 px-2 py-0.5 rounded border border-emerald-500/30 font-semibold">RBAC Intégré</span>
                    </div>
                    <p class="text-xs text-slate-400 mb-4">
                        Cliquez sur n'importe quel acteur ci-dessous pour tester l'interface avec ses droits et son périmètre spécifique :
                    </p>

                    <div class="space-y-2">
                        <!-- Admin -->
                        <a href="{{ route('quick.login', 'admin') }}" class="group flex items-center justify-between p-3 rounded-xl bg-slate-900/60 hover:bg-slate-800 border border-slate-700/60 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center text-xs font-bold">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <div class="text-left">
                                    <div class="text-xs font-semibold text-white group-hover:text-indigo-300">Administrateur Système</div>
                                    <div class="text-[10px] text-slate-400">Gestion des utilisateurs, campagnes, critères & audits</div>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-slate-600 group-hover:text-slate-300 text-xs"></i>
                        </a>

                        <!-- Gestionnaire Internat -->
                        <a href="{{ route('quick.login', 'gestionnaire') }}" class="group flex items-center justify-between p-3 rounded-xl bg-slate-900/60 hover:bg-slate-800 border border-slate-700/60 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-xs font-bold">
                                    <i class="fa-solid fa-house-chimney-user"></i>
                                </div>
                                <div class="text-left">
                                    <div class="text-xs font-semibold text-white group-hover:text-emerald-300">Gestionnaire d'Internat (Casablanca)</div>
                                    <div class="text-[10px] text-slate-400">Instruction des pièces, affectation lits, cantine, présences</div>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-slate-600 group-hover:text-slate-300 text-xs"></i>
                        </a>

                        <!-- Directeur Etablissement -->
                        <a href="{{ route('quick.login', 'directeur') }}" class="group flex items-center justify-between p-3 rounded-xl bg-slate-900/60 hover:bg-slate-800 border border-slate-700/60 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-sky-500/20 text-sky-400 border border-sky-500/30 flex items-center justify-center text-xs font-bold">
                                    <i class="fa-solid fa-stamp"></i>
                                </div>
                                <div class="text-left">
                                    <div class="text-xs font-semibold text-white group-hover:text-sky-300">Directeur d'Établissement (CMC)</div>
                                    <div class="text-[10px] text-slate-400">Validation hiérarchique de la commission d'admission</div>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-slate-600 group-hover:text-slate-300 text-xs"></i>
                        </a>

                        <!-- Direction Régionale -->
                        <a href="{{ route('quick.login', 'direction_regionale') }}" class="group flex items-center justify-between p-3 rounded-xl bg-slate-900/60 hover:bg-slate-800 border border-slate-700/60 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-purple-500/20 text-purple-400 border border-purple-500/30 flex items-center justify-center text-xs font-bold">
                                    <i class="fa-solid fa-map-location-dot"></i>
                                </div>
                                <div class="text-left">
                                    <div class="text-xs font-semibold text-white group-hover:text-purple-300">Direction Régionale (Casa-Settat)</div>
                                    <div class="text-[10px] text-slate-400">Supervision consolidée et arbitrages régionaux</div>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-slate-600 group-hover:text-slate-300 text-xs"></i>
                        </a>

                        <!-- Candidat / Stagiaire -->
                        <a href="{{ route('quick.login', 'candidat') }}" class="group flex items-center justify-between p-3 rounded-xl bg-slate-900/60 hover:bg-slate-800 border border-slate-700/60 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-rose-500/20 text-rose-400 border border-rose-500/30 flex items-center justify-center text-xs font-bold">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                </div>
                                <div class="text-left">
                                    <div class="text-xs font-semibold text-white group-hover:text-rose-300">Candidat Stagiaire (Yassine - Admis)</div>
                                    <div class="text-[10px] text-slate-400">Dépôt préinscription, suivi de statut, lit affecté & recours</div>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-slate-600 group-hover:text-slate-300 text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
