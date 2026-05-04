<x-layouts.app :title="$movieTitle . ' - Avis'">
    @if ($error)
        <p class="mb-4 rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">{{ $error }}</p>
    @endif

    <section class="mb-8 grid gap-6 rounded-2xl border border-slate-800 bg-slate-900/70 p-4 sm:p-6 md:grid-cols-[220px_1fr]">
        <div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-800">
            @if ($movie && ($movie['Poster'] ?? 'N/A') !== 'N/A')
                <img
                    src="{{ $movie['Poster'] }}"
                    alt="Affiche {{ $movieTitle }}"
                    class="h-full w-full object-cover"
                >
            @else
                <div class="flex aspect-[2/3] items-center justify-center text-sm text-slate-400">Pas d'image</div>
            @endif
        </div>

        <div>
            <h1 class="text-2xl font-semibold text-white sm:text-3xl">{{ $movieTitle }}</h1>
            @if ($movie)
                <div class="mt-3 grid gap-2 text-sm text-slate-300 sm:grid-cols-2">
                    <p><span class="text-slate-400">Annee :</span> {{ $movie['Year'] ?? 'N/A' }}</p>
                    <p><span class="text-slate-400">Genre :</span> {{ $movie['Genre'] ?? 'N/A' }}</p>
                    <p><span class="text-slate-400">IMDb :</span> {{ $movie['imdbRating'] ?? 'N/A' }}</p>
                    <p><span class="text-slate-400">Duree :</span> {{ $movie['Runtime'] ?? 'N/A' }}</p>
                </div>
                <p class="mt-4 text-sm leading-6 text-slate-300 sm:text-base">{{ $movie['Plot'] ?? '' }}</p>
            @endif
        </div>
    </section>

    <section class="mb-8 rounded-2xl border border-slate-800 bg-slate-900/70 p-4 sm:p-6">
        <h2 class="text-xl font-semibold text-white">Publier un avis</h2>
        @guest
            <p class="mt-3 rounded-lg border border-indigo-500/30 bg-indigo-500/10 px-3 py-2 text-sm text-indigo-200">
                Connecte-toi pour ajouter ce film à ta liste et publier un avis.
                <a href="{{ route('login') }}" class="font-semibold underline">Se connecter</a>
            </p>
        @elseif ($canReview)
            <form method="POST" action="{{ route('reviews.store', $imdbId) }}" class="mt-4 grid gap-3">
                @csrf
                <input type="hidden" name="movie_title" value="{{ $movieTitle }}">

                <div class="grid gap-3 sm:grid-cols-2">
                    <label class="text-sm text-slate-300">
                        Note
                        <select
                            name="rating"
                            required
                            class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white outline-none focus:border-indigo-500"
                        >
                            <option value="5">5/5</option>
                            <option value="4">4/5</option>
                            <option value="3">3/5</option>
                            <option value="2">2/5</option>
                            <option value="1">1/5</option>
                        </select>
                    </label>
                </div>

                <label class="text-sm text-slate-300">
                    Ton avis
                    <textarea
                        name="comment"
                        rows="4"
                        required
                        maxlength="2000"
                        class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white outline-none placeholder:text-slate-500 focus:border-indigo-500"
                    ></textarea>
                </label>

                <button type="submit" class="w-full rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-emerald-400 sm:w-auto">
                    Publier l'avis
                </button>
            </form>
        @else
            <p class="mt-3 rounded-lg border border-amber-500/30 bg-amber-500/10 px-3 py-2 text-sm text-amber-200">
                Tu dois marquer ce film comme <strong>Vu</strong> dans "Ma liste" pour publier un avis.
            </p>
        @endif
    </section>

    <section class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 sm:p-6">
        <h2 class="text-xl font-semibold text-white">Avis des utilisateurs ({{ $reviews->count() }})</h2>
        @if ($reviews->isEmpty())
            <p class="mt-3 text-slate-300">Aucun avis pour le moment.</p>
        @else
            <div class="mt-4 grid gap-3">
                @foreach ($reviews as $review)
                    <article class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                        <p class="text-sm text-slate-300">
                            <strong class="text-white">{{ $review->author }}</strong>
                            <span class="mx-2 text-slate-600">•</span>
                            <span class="text-amber-300">{{ $review->rating }}/5</span>
                        </p>
                        <p class="mt-2 text-sm leading-6 text-slate-200">{{ $review->comment }}</p>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.app>
