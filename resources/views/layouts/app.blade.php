<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Maison des Stagiaires - OFPPT' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Heroicons / FontAwesome via CDN for rich iconography -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
    </style>
</head>
<body class="min-h-full flex flex-col antialiased text-slate-800 bg-slate-50 selection:bg-emerald-500 selection:text-white">
    <div class="flex min-h-screen flex-1 flex-col lg:flex-row">
        <aside class="w-full shrink-0 border-b border-slate-200 bg-white lg:fixed lg:inset-y-0 lg:left-0 lg:flex lg:w-60 lg:flex-col lg:border-b-0 lg:border-r">
            <div class="flex items-center gap-3 border-b border-slate-100 px-6 py-5">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-md shadow-emerald-500/20 group-hover:scale-105 transition transform">
                        <i class="fa-solid fa-hotel text-lg"></i>
                    </div>
                    <div>
                        <div class="text-lg font-bold tracking-tight text-slate-900 flex items-center gap-2">
                            <span>OFPPT</span>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-semibold border border-slate-200">Internats</span>
                        </div>
                        <div class="text-xs text-slate-500 font-medium">Maison des Stagiaires</div>
                    </div>
                </a>
            </div>

            <div class="border-b border-slate-100 bg-slate-50 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-200 border border-slate-300 flex items-center justify-center text-slate-600 font-bold shadow-inner">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="truncate text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</div>
                        <div class="flex items-center gap-1 text-xs font-medium text-emerald-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ auth()->user()->role_label }}
                        </div>
                    </div>
                </div>
            </div>

            <nav class="flex flex-wrap gap-1 px-4 py-4 text-sm font-medium lg:flex-1 lg:flex-col lg:flex-nowrap lg:overflow-y-auto">
                        <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-chart-pie mr-1.5 text-xs text-slate-400"></i> Tableau de bord
                        </a>
                        <a href="{{ route('dossiers.index') }}" class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('dossiers.*') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-folder-open mr-1.5 text-xs text-slate-400"></i> Préinscriptions
                        </a>
                        <a href="{{ route('deliberation.index') }}" class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('deliberation.*') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-scale-balanced mr-1.5 text-xs text-slate-400"></i> Délibération & Admission
                        </a>
                        <a href="{{ route('hebergement.index') }}" class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('hebergement.*') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-bed mr-1.5 text-xs text-slate-400"></i> Hébergement
                        </a>
                        <a href="{{ route('restauration.index') }}" class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('restauration.*') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-utensils mr-1.5 text-xs text-slate-400"></i> Restauration
                        </a>
                        <a href="{{ route('presences.index') }}" class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('presences.*') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-clipboard-user mr-1.5 text-xs text-slate-400"></i> Présences
                        </a>
                        <a href="{{ route('incidents.index') }}" class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('incidents.*') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-triangle-exclamation mr-1.5 text-xs text-slate-400"></i> Incidents & Réclamations
                        </a>
                        @if(auth()->user()?->isAdmin() || auth()->user()?->isDirectionCentrale())
                        <a href="{{ route('referentiels.index') }}" class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('referentiels.*') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-sliders mr-1.5 text-xs text-slate-400"></i> Paramétrage
                        </a>
                        @endif
                    </nav>

            <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 p-4">
                @csrf
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg bg-rose-50 px-3 py-2.5 text-sm font-semibold text-rose-700 transition hover:bg-rose-100">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    Se déconnecter
                </button>
            </form>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col lg:ml-60">

    <!-- Notifications et Alertes Flash -->
    <div class="mx-auto mt-4 w-full max-w-7xl px-4 sm:px-6 lg:px-8">
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

        @if(session('warning'))
            <div class="flex items-center gap-3 p-4 mb-4 text-amber-800 bg-amber-50 border border-amber-200 rounded-xl shadow-sm">
                <i class="fa-solid fa-triangle-exclamation text-amber-500 text-lg"></i>
                <div class="text-sm font-medium">{{ session('warning') }}</div>
            </div>
        @endif
    </div>

    <!-- Contenu Principal -->
    <main class="w-full max-w-7xl mx-auto flex-1 px-4 py-6 sm:px-6 lg:px-8">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Pied de page -->
    <footer class="mt-auto border-t border-slate-200 bg-white py-4 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <div>
                © {{ date('Y') }} Office de la Formation Professionnelle et de la Promotion du Travail (OFPPT) — Direction des Systèmes d'Information.
            </div>
            <div class="flex items-center gap-4 text-slate-400">
                <span>Système National de Gestion des Internats</span>
                <span>•</span>
                <span>Version 2.0</span>
            </div>
        </div>
    </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
