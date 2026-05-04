<x-layouts.app :title="'Inscription'">
    <section class="mx-auto max-w-lg rounded-2xl border border-slate-800 bg-slate-900/70 p-4 sm:p-6">
        <h1 class="text-2xl font-semibold text-white">Créer un compte</h1>
        <p class="mt-2 text-sm text-slate-400">Inscris-toi pour gérer ta liste et tes avis.</p>

        <form method="POST" action="{{ route('register.store') }}" class="mt-5 grid gap-3">
            @csrf

            <div class="grid gap-3 sm:grid-cols-2">
                <label class="text-sm text-slate-300">
                    Prénom
                    <input
                        type="text"
                        name="first_name"
                        value="{{ old('first_name') }}"
                        required
                        maxlength="80"
                        class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white outline-none placeholder:text-slate-500 focus:border-indigo-500"
                    >
                </label>

                <label class="text-sm text-slate-300">
                    Nom
                    <input
                        type="text"
                        name="last_name"
                        value="{{ old('last_name') }}"
                        required
                        maxlength="80"
                        class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white outline-none placeholder:text-slate-500 focus:border-indigo-500"
                    >
                </label>
            </div>

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
                    minlength="8"
                    class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white outline-none placeholder:text-slate-500 focus:border-indigo-500"
                >
            </label>

            <label class="text-sm text-slate-300">
                Confirmer le mot de passe
                <input
                    type="password"
                    name="password_confirmation"
                    required
                    minlength="8"
                    class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white outline-none placeholder:text-slate-500 focus:border-indigo-500"
                >
            </label>

            <button type="submit" class="mt-2 w-full rounded-xl bg-indigo-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-400">
                S'inscrire
            </button>
        </form>
    </section>
</x-layouts.app>
