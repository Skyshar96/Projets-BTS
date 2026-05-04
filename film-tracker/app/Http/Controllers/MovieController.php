<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\WatchItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        // q = texte recherché depuis l'URL (ex: /?q=batman), trim enlève les espaces inutiles.
        $query = trim((string) $request->query('q', ''));
        // Valeurs par défaut envoyées à la vue si aucune recherche ou en cas d'erreur.
        $movies = [];
        $error = null;

        if ($query !== '') {
            // On centralise la lecture de la clé API pour gérer plusieurs formats de configuration.
            $apiKey = $this->resolveApiKey();

            if ($apiKey === null) {
                $error = 'OMDb API key is missing. Please set OMDB_API_KEY in .env.';
            } else {
                // Requête de recherche OMDb (paramètre "s" = search text).
                $response = Http::timeout(10)->get('https://www.omdbapi.com/', [
                    'apikey' => $apiKey,
                    's' => $query,
                ]);

                if ($response->successful()) {
                    $payload = $response->json();

                    // On sécurise l'accès aux données OMDb pour éviter les erreurs si la réponse n'est pas un tableau JSON valide.
                    if (is_array($payload)) {
                        $movies = ($payload['Response'] ?? 'False') === 'True' && is_array($payload['Search'] ?? null)
                            ? $payload['Search']
                            : [];

                        if (($payload['Response'] ?? 'False') !== 'True') {
                            // OMDb peut répondre en HTTP 200 mais avec Response=False + Error.
                            $error = (string) ($payload['Error'] ?? 'No movie found.');
                        }
                    } else {
                        $error = 'Invalid response from OMDb API.';
                    }
                } else {
                    $error = 'Unable to reach OMDb API right now.';
                }
            }
        }

        return view('movies.index', [
            'query' => $query,
            'movies' => $movies,
            'error' => $error,
        ]);
    }

    public function show(string $imdbId)
    {
        // Détails d'un film précis, identifié par son identifiant IMDb (ex: tt0372784).
        $movie = null;
        $error = null;
        $apiKey = $this->resolveApiKey();

        if ($apiKey === null) {
            $error = 'OMDb API key is missing. Please set OMDB_API_KEY in .env.';
        } else {
            // Requête de détail OMDb (paramètre "i" = imdbId du film).
            $response = Http::timeout(10)->get('https://www.omdbapi.com/', [
                'apikey' => $apiKey,
                'i' => $imdbId,
                'plot' => 'short',
            ]);

            if ($response->successful()) {
                $payload = $response->json();

                // Même protection que dans index(): on lit les clés uniquement si la payload est bien un tableau.
                if (is_array($payload)) {
                    if (($payload['Response'] ?? 'False') === 'True') {
                        $movie = $payload;
                    } else {
                        $error = (string) ($payload['Error'] ?? 'Movie not found.');
                    }
                } else {
                    $error = 'Invalid response from OMDb API.';
                }
            } else {
                $error = 'Unable to reach OMDb API right now.';
            }
        }

        // On récupère uniquement l'élément de liste du compte connecté pour ce film.
        $watchItem = Auth::check()
            ? WatchItem::query()
                ->where('user_id', Auth::id())
                ->where('imdb_id', $imdbId)
                ->first()
            : null;
        // Les avis sont publics sur la fiche film, donc filtrés uniquement par imdb_id.
        $reviews = Review::query()
            ->where('imdb_id', $imdbId)
            ->latest()
            ->get();

        return view('movies.show', [
            'imdbId' => $imdbId,
            'movie' => $movie,
            // Le titre vient d'OMDb si disponible, sinon de la watchlist utilisateur, sinon fallback "Film".
            'movieTitle' => (is_array($movie) ? ($movie['Title'] ?? null) : null) ?? $watchItem?->title ?? 'Film',
            'error' => $error,
            'reviews' => $reviews,
            'canReview' => $watchItem?->status === 'vu',
        ]);
    }

    private function resolveApiKey(): ?string
    {
        // La clé vient de config/services.php -> services.omdb.key (alimenté par OMDB_API_KEY du .env).
        $raw = (string) config('services.omdb.key', '');

        if ($raw === '') {
            return null;
        }

        if (str_contains($raw, 'apikey=')) {
            // Supporte aussi le cas où on colle une URL OMDb complète au lieu de la clé seule.
            $parts = parse_url($raw);
            // Parse URL peut retourner false: on protège la lecture de query avant parse_str.
            parse_str(is_array($parts) ? ($parts['query'] ?? '') : '', $queryParams);

            return $queryParams['apikey'] ?? null;
        }

        return $raw;
    }
}
