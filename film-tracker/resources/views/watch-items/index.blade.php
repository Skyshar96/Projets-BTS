<x-layouts.app :title="'Ma liste'">
    <section class="mb-6">
        <h1 class="text-2xl font-semibold text-white sm:text-3xl">Ma liste</h1>
        <p class="mt-2 text-sm text-slate-400 sm:text-base">Retrouve tes films, change leur statut et lis les avis.</p>
    </section>

    @if ($watchItems->isEmpty())
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 text-center text-slate-300">
            Aucun film ajoute pour le moment.
        </div>
    @else
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($watchItems as $item)
                <article class="flex h-full flex-col overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70 shadow-lg">
                    <a href="{{ route('movies.show', $item->imdb_id) }}" class="block aspect-[2/3] bg-slate-800">
                        @if ($item->poster)
                            <img
                                src="{{ $item->poster }}"
                                alt="Affiche {{ $item->title }}"
                                class="h-full w-full object-cover"
                                loading="lazy"
                            >
                        @else
                            <div class="flex h-full items-center justify-center text-sm text-slate-400">Pas d'image</div>
                        @endif
                    </a>

                    <div class="flex flex-1 flex-col p-4">
                        <h2 class="line-clamp-2 text-base font-semibold text-white">{{ $item->title }}</h2>
                        <p class="mt-1 text-sm text-slate-400">Annee : {{ $item->year ?? 'N/A' }}</p>
                        <p class="mt-2 text-sm">
                            <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $item->status === 'vu' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300' }}">
                                {{ $item->status === 'vu' ? 'Vu' : 'A voir' }}
                            </span>
                        </p>

                        <div class="mt-4 flex flex-col gap-2">
                            <a
                                href="{{ route('movies.show', $item->imdb_id) }}"
                                class="rounded-lg border border-slate-700 px-3 py-2 text-center text-sm font-medium text-slate-200 transition hover:border-indigo-400 hover:text-white"
                            >
                                Voir details et avis
                            </a>

                            <form method="POST" action="{{ route('watch-items.update', $item) }}">
                                @csrf
                                @method('PATCH')
                                <button
                                    type="submit"
                                    class="w-full rounded-lg bg-indigo-500 px-3 py-2 text-sm font-semibold text-white transition hover:bg-indigo-400"
                                >
                                    Marquer {{ $item->status === 'vu' ? 'A voir' : 'Vu' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>
    @endif
</x-layouts.app>
