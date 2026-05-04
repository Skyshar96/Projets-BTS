<x-layouts.app :title="'Recherche de films'">
    <section class="mb-6">
        <h1 class="text-2xl font-semibold text-white sm:text-3xl">Trouve un film ou une serie</h1>
        <p class="mt-2 text-sm text-slate-400 sm:text-base">Recherche avec OMDb puis ajoute directement a ta liste.</p>
    </section>

    <section class="mb-8 rounded-2xl border border-slate-800 bg-slate-900/70 p-4 sm:p-6">
        <form method="GET" action="{{ route('movies.index') }}" class="flex flex-col gap-3 sm:flex-row">
            <input
                id="q"
                name="q"
                type="text"
                value="{{ $query }}"
                placeholder="Ex: Batman"
                class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white outline-none ring-0 placeholder:text-slate-500 focus:border-indigo-500 sm:text-base"
            >
            <button
                type="submit"
                class="rounded-xl bg-indigo-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-400 sm:text-base"
            >
                Rechercher
            </button>
        </form>

        @if ($error)
            <p class="mt-3 rounded-lg border border-red-500/40 bg-red-500/10 px-3 py-2 text-sm text-red-200">{{ $error }}</p>
        @endif
    </section>

    @if (count($movies) > 0)
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($movies as $movie)
                <article class="flex h-full flex-col overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70 shadow-lg">
                    <a href="{{ route('movies.show', $movie['imdbID'] ?? '') }}" class="block aspect-[2/3] bg-slate-800">
                        @if (($movie['Poster'] ?? 'N/A') !== 'N/A')
                            <img
                                src="{{ $movie['Poster'] }}"
                                alt="Affiche {{ $movie['Title'] ?? 'film' }}"
                                class="h-full w-full object-cover"
                                loading="lazy"
                            >
                        @else
                            <div class="flex h-full items-center justify-center text-sm text-slate-400">Pas d'image</div>
                        @endif
                    </a>

                    <div class="flex flex-1 flex-col p-4">
                        <h2 class="line-clamp-2 text-base font-semibold text-white">{{ $movie['Title'] ?? 'Titre inconnu' }}</h2>
                        <p class="mt-1 text-sm text-slate-400">Annee : {{ $movie['Year'] ?? 'N/A' }}</p>

                        <div class="mt-4 flex flex-col gap-2">
                            <a
                                href="{{ route('movies.show', $movie['imdbID'] ?? '') }}"
                                class="rounded-lg border border-slate-700 px-3 py-2 text-center text-sm font-medium text-slate-200 transition hover:border-indigo-400 hover:text-white"
                            >
                                Voir details et avis
                            </a>

                            @auth
                                <form method="POST" action="{{ route('watch-items.store') }}">
                                    @csrf
                                    <input type="hidden" name="imdb_id" value="{{ $movie['imdbID'] ?? '' }}">
                                    <input type="hidden" name="title" value="{{ $movie['Title'] ?? '' }}">
                                    <input type="hidden" name="poster" value="{{ ($movie['Poster'] ?? 'N/A') !== 'N/A' ? $movie['Poster'] : '' }}">
                                    <input type="hidden" name="year" value="{{ $movie['Year'] ?? '' }}">
                                    <input type="hidden" name="status" value="a_voir">
                                    <button
                                        type="submit"
                                        class="w-full rounded-lg bg-emerald-500 px-3 py-2 text-sm font-semibold text-slate-950 transition hover:bg-emerald-400"
                                    >
                                        Ajouter a ma liste
                                    </button>
                                </form>
                            @else
                                <a
                                    href="{{ route('login') }}"
                                    class="w-full rounded-lg bg-emerald-500 px-3 py-2 text-center text-sm font-semibold text-slate-950 transition hover:bg-emerald-400"
                                >
                                    Connecte-toi pour ajouter
                                </a>
                            @endauth
                        </div>
                    </div>
                </article>
            @endforeach
        </section>
    @elseif ($query !== '')
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 text-center text-slate-300">
            Aucun resultat pour <span class="font-semibold text-white">{{ $query }}</span>.
        </div>
    @endif
</x-layouts.app>
