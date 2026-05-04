<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Film Tracker' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="mx-auto w-full max-w-6xl px-4 py-6 sm:px-6 lg:px-8">
        <header class="mb-8 flex flex-col gap-4 rounded-2xl border border-slate-800 bg-slate-900/70 p-4 shadow-lg sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('movies.index') }}" class="text-xl font-bold tracking-tight text-white">Film Tracker</a>
            <nav class="flex items-center gap-2 text-sm sm:gap-3">
                <a href="{{ route('movies.index') }}" class="rounded-lg px-3 py-2 text-slate-300 transition hover:bg-slate-800 hover:text-white">Recherche</a>
                @auth
                    <a href="{{ route('watch-items.index') }}" class="rounded-lg px-3 py-2 text-slate-300 transition hover:bg-slate-800 hover:text-white">Ma liste</a>
                    <span class="hidden text-slate-500 sm:inline">|</span>
                    <span class="hidden text-slate-300 sm:inline">Bonjour {{ auth()->user()->first_name ?? auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg px-3 py-2 text-slate-300 transition hover:bg-slate-800 hover:text-white">Déconnexion</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-slate-300 transition hover:bg-slate-800 hover:text-white">Connexion</a>
                    <a href="{{ route('register') }}" class="rounded-lg bg-indigo-500 px-3 py-2 font-semibold text-white transition hover:bg-indigo-400">Inscription</a>
                @endauth
            </nav>
        </header>

        @if (session('success'))
            <div class="mb-4 rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                {{ $errors->first() }}
            </div>
        @endif

        {{ $slot }}
    </div>
</body>
</html>
