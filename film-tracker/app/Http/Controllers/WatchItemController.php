<?php

namespace App\Http\Controllers;

use App\Models\WatchItem;
use Illuminate\Http\Request;

class WatchItemController extends Controller
{
    public function index(Request $request)
    {
        // On récupère l'utilisateur connecté explicitement pour éviter tout accès null.
        $user = $request->user();

        if (! $user) {
            // Cette action est privée: sans session active on renvoie 401.
            abort(401);
        }

        $watchItems = WatchItem::query()
            // Chaque utilisateur ne voit que sa propre watchlist.
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->get();

        return view('watch-items.index', [
            'watchItems' => $watchItems,
        ]);
    }

    public function store(Request $request)
    {
        // On force l'association des films au compte connecté.
        $user = $request->user();

        if (! $user) {
            // Protection supplémentaire si la route est appelée sans authentification.
            abort(401);
        }

        $validated = $request->validate([
            'imdb_id' => ['required', 'string', 'max:30'],
            'title' => ['required', 'string', 'max:255'],
            'poster' => ['nullable', 'string', 'max:2048'],
            'year' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'in:a_voir,vu'],
        ]);

        WatchItem::query()->updateOrCreate(
            [
                // Clé unique fonctionnelle: un film par utilisateur.
                'user_id' => $user->id,
                'imdb_id' => $validated['imdb_id'],
            ],
            [
                'user_id' => $user->id,
                'title' => $validated['title'],
                'poster' => $validated['poster'] ?? null,
                'year' => $validated['year'] ?? null,
                'status' => $validated['status'] ?? 'a_voir',
            ],
        );

        return redirect()->back()->with('success', 'Film ajouté à votre liste.');
    }

    public function update(Request $request, WatchItem $watchItem)
    {
        // On vérifie d'abord l'utilisateur courant.
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        // Un utilisateur ne peut modifier que ses propres éléments de watchlist.
        if ($watchItem->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['nullable', 'in:a_voir,vu'],
        ]);

        $watchItem->update([
            'status' => $validated['status'] ?? ($watchItem->status === 'vu' ? 'a_voir' : 'vu'),
        ]);

        return redirect()->back()->with('success', 'Statut mis à jour.');
    }
}
