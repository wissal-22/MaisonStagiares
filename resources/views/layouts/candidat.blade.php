<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Espace Candidat - Maison des Stagiaires OFPPT' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <style> body { font-family: 'Instrument Sans', sans-serif; } </style>
</head>
<body class="min-h-screen flex flex-col antialiased text-slate-800 bg-slate-50 selection:bg-rose-500 selection:text-white">

    <aside class="w-full shrink-0 border-b border-slate-200 bg-white sm:fixed sm:inset-y-0 sm:left-0 sm:flex sm:w-60 sm:flex-col sm:border-b-0 sm:border-r">
        <div class="flex items-center gap-3 border-b border-slate-100 px-6 py-5">
            <a href="{{ route('candidat.dashboard') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-rose-600 to-pink-500 flex items-center justify-center text-white shadow-md shadow-rose-500/20 group-hover:scale-105 transition transform">
                    <i class="fa-solid fa-graduation-cap text-lg"></i>
                </div>
                <div>
                    <div class="text-base font-bold text-slate-900 leading-tight">OFPPT - Maison des Stagiaires</div>
                    <div class="text-xs text-slate-500 font-medium">Portail Candidat</div>
                </div>
            </a>
        </div>

        <div class="border-b border-slate-100 bg-slate-50 px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-rose-100 border border-rose-200 flex items-center justify-center text-rose-700 font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <div class="truncate text-sm font-bold text-slate-900">{{ auth()->user()->name }}</div>
                    <div class="text-xs font-medium text-rose-600">Stagiaire / Candidat</div>
                </div>
            </div>
        </div>

        <nav class="flex flex-wrap gap-1 px-4 py-4 text-sm font-medium sm:flex-1 sm:flex-col">
            <a href="{{ route('candidat.dashboard') }}" class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('candidat.dashboard') ? 'bg-rose-50 text-rose-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                <i class="fa-solid fa-house mr-2 text-xs text-slate-400"></i> Mon dossier
            </a>
            <a href="{{ route('candidat.preinscription') }}" class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('candidat.preinscription*') ? 'bg-rose-50 text-rose-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                <i class="fa-solid fa-file-pen mr-2 text-xs text-slate-400"></i> Préinscription
            </a>
        </nav>

        <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 p-4">
            @csrf
            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg bg-rose-50 px-3 py-2.5 text-sm font-semibold text-rose-700 transition hover:bg-rose-100">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                Se déconnecter
            </button>
        </form>
    </aside>

    <!-- Switcher de Rôles Test -->
    <div class="hidden bg-slate-900 text-slate-300 text-xs py-1.5 px-4 border-b border-slate-800 flex-wrap items-center justify-between gap-2 shadow-inner">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-rose-500/20 text-rose-300 font-semibold text-[10px] tracking-wider uppercase border border-rose-500/30">
                <i class="fa-solid fa-graduation-cap mr-1 text-rose-400"></i> Espace Stagiaire
            </span>
            <span class="text-slate-400 hidden sm:inline">Portail Candidats & Résidents en Internat</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-slate-400 font-medium">Changer de rôle test :</span>
            <a href="{{ route('quick.login', 'admin') }}" class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300">Admin</a>
            <a href="{{ route('quick.login', 'gestionnaire') }}" class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300">Gestionnaire</a>
            <a href="{{ route('quick.login', 'directeur') }}" class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300">Directeur</a>
            <a href="{{ route('quick.login', 'candidat') }}" class="px-2 py-0.5 rounded bg-rose-600 text-white font-bold">Stagiaire</a>
        </div>
    </div>

    <!-- Header Candidat -->
    <header class="hidden bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-rose-600 to-pink-500 flex items-center justify-center text-white shadow-md shadow-rose-500/20">
                        <i class="fa-solid fa-graduation-cap text-lg"></i>
                    </div>
                    <div>
                        <div class="text-base font-bold text-slate-900 leading-tight">OFPPT • Maison des Stagiaires</div>
                        <div class="text-xs text-slate-500 font-medium">Portail Candidat à l'Hébergement</div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <nav class="hidden sm:flex items-center gap-2">
                        <a href="{{ route('candidat.dashboard') }}" class="px-3 py-1.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('candidat.dashboard') ? 'bg-rose-50 text-rose-700' : 'text-slate-600 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-house text-xs mr-1"></i> Mon Dossier
                        </a>
                        <a href="{{ route('candidat.preinscription') }}" class="px-3 py-1.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('candidat.preinscription*') ? 'bg-rose-50 text-rose-700' : 'text-slate-600 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-file-pen text-xs mr-1"></i> Préinscription
                        </a>
                    </nav>

                    <div class="h-6 w-px bg-slate-200"></div>

                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <div class="text-sm font-bold text-slate-900">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-rose-600 font-medium">Stagiaire / Candidat</div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" title="Se déconnecter" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="flex items-center gap-3 p-4 mb-4 text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-xl shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-center gap-3 p-4 mb-4 text-rose-800 bg-rose-50 border border-rose-200 rounded-xl shadow-sm">
                <i class="fa-solid fa-circle-xmark text-rose-500 text-lg"></i>
                <div class="text-sm font-medium">{{ session('error') }}</div>
            </div>
        @endif
        @if(session('info'))
            <div class="flex items-center gap-3 p-4 mb-4 text-sky-800 bg-sky-50 border border-sky-200 rounded-xl shadow-sm">
                <i class="fa-solid fa-circle-info text-sky-500 text-lg"></i>
                <div class="text-sm font-medium">{{ session('info') }}</div>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-1 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full sm:ml-60">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500 mt-auto sm:ml-60">
        © {{ date('Y') }} Office de la Formation Professionnelle et de la Promotion du Travail — Maisons des Stagiaires
    </footer>
</body>
</html>
