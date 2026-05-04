<x-layouts.app :title="'Connexion'">
    <section class="mx-auto max-w-lg rounded-2xl border border-slate-800 bg-slate-900/70 p-4 sm:p-6">
        <h1 class="text-2xl font-semibold text-white">Connexion</h1>
        <p class="mt-2 text-sm text-slate-400">Connecte-toi pour retrouver ta liste et publier des avis.</p>

        <form method="POST" action="{{ route('login.store') }}" class="mt-5 grid gap-3">
            @csrf

            <label class="text-sm text-slate-300">
                Adresse mail
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    maxlength="255"
                    class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white outline-none placeholder:text-slate-500 focus:border-indigo-500"
                >
            </label>

            <label class="text-sm text-slate-300">
                Mot de passe
                <input
                    type="password"
                    name="password"
                    required
                    class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white outline-none placeholder:text-slate-500 focus:border-indigo-500"
                >
            </label>

            <button type="submit" class="mt-2 w-full rounded-xl bg-indigo-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-400">
                Se connecter
            </button>
        </form>
    </section>
</x-layouts.app>
