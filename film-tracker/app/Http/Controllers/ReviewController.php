<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\WatchItem;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, string $imdbId)
    {
        $watchItem = WatchItem::query()
            ->where('user_id', $request->user()->id)
            ->where('imdb_id', $imdbId)
            ->first();

        if (! $watchItem || $watchItem->status !== 'vu') {
            return redirect()
                ->route('movies.show', $imdbId)
                ->withErrors([
                    'review' => 'Tu dois marquer ce film comme "Vu" avant de publier un avis.',
                ]);
        }

        $validated = $request->validate([
            'movie_title' => ['required', 'string', 'max:255'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        $user = $request->user();
        $author = trim(($user->first_name ?? '').' '.($user->last_name ?? ''));

        Review::query()->create([
            'user_id' => $user->id,
            'imdb_id' => $imdbId,
            'movie_title' => $validated['movie_title'],
            'author' => $author !== '' ? $author : $user->name,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return redirect()
            ->route('movies.show', $imdbId)
            ->with('success', 'Avis publié.');
    }
}
