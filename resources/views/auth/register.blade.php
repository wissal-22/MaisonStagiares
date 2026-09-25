<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Créer un compte - Maison des Stagiaires OFPPT</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style> body { font-family: 'Instrument Sans', sans-serif; } </style>
</head>
<body class="h-full flex flex-col justify-center py-10 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-950 via-slate-900 to-rose-950 text-slate-100">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-rose-500 to-pink-500 text-white shadow-xl shadow-rose-500/25 mb-3">
            <i class="fa-solid fa-graduation-cap text-2xl"></i>
        </div>
        <h2 class="text-2xl font-bold tracking-tight text-white">
            Création de Compte Candidat / Stagiaire
        </h2>
        <p class="mt-1 text-xs text-slate-400">
            Créez votre compte candidat pour commencer votre préinscription
        </p>
    </div>

    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md px-4">
        <div class="bg-slate-800/85 backdrop-blur-xl border border-slate-700/80 py-8 px-6 shadow-2xl rounded-2xl sm:px-10">

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-950/60 border border-rose-800 text-rose-300 text-xs font-medium">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase">Nom complet *</label>
                        <input name="name" type="text" required value="{{ old('name') }}" placeholder="Ex: Youssef El Amrani"
                            class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white text-sm focus:ring-rose-500 focus:border-rose-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase">Adresse E-mail *</label>
                        <input name="email" type="email" required value="{{ old('email') }}" placeholder="youssef@example.com"
                            class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white text-sm focus:ring-rose-500 focus:border-rose-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase">Sexe *</label>
                        <select name="genre" required class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white text-sm focus:ring-rose-500 focus:border-rose-500">
                            <option value="garcon" {{ old('genre') === 'garcon' ? 'selected' : '' }}>Garçon (Résidence Masculine)</option>
                            <option value="fille" {{ old('genre') === 'fille' ? 'selected' : '' }}>Fille (Résidence Féminine)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase">Mot de passe *</label>
                        <input name="password" type="password" required
                            class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white text-sm focus:ring-rose-500 focus:border-rose-500">
                    </div>

                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full flex justify-center items-center py-2.5 px-4 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-rose-600 to-pink-500 hover:from-rose-500 hover:to-pink-400 shadow-lg shadow-rose-600/30 transition">
                        Créer mon compte et accéder à la préinscription <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                    </button>
                </div>
            </form>

            <div class="mt-4 pt-4 border-t border-slate-700 text-center">
                <span class="text-xs text-slate-400">Déjà inscrit ?</span>
                <a href="{{ route('login') }}" class="text-xs font-semibold text-rose-400 hover:text-rose-300 ml-1">Connectez-vous</a>
            </div>
        </div>
    </div>

</body>
</html>
