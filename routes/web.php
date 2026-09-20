<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Global helper to get all anime data
if (! function_exists('getAnimeData')) {
    function getAnimeData() {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('animes') && \App\Models\Anime::count() > 0) {
                $dbAnimes = \App\Models\Anime::with('genres')->get();
                $configAnimes = config('anime.anime', []);
                $configMap = collect($configAnimes)->keyBy('slug');

                $mapped = [];
                foreach ($dbAnimes as $index => $a) {
                    $configItem = $configMap->get($a->slug, []);
                    $mapped[] = [
                        'id' => $a->id,
                        'slug' => $a->slug,
                        'title' => $a->title,
                        'japanese_title' => $a->title_alternative ?? $a->title,
                        'poster' => $a->poster_url ?? ($configItem['poster'] ?? 'https://images.unsplash.com/photo-1578632767115-351597cf2477?q=80&w=800&auto=format&fit=crop'),
                        'banner' => $a->banner_url ?? ($configItem['banner'] ?? 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?q=80&w=1600&auto=format&fit=crop'),
                        'trailer_url' => $configItem['trailer_url'] ?? null,
                        'rating' => (float)($a->rating ?? 9.0),
                        'year' => (int)($a->year ?? 2024),
                        'type' => strtoupper($a->type ?? 'TV'),
                        'episodes' => (int)($a->total_episodes ?? 12),
                        'status' => ucfirst($a->status ?? 'ongoing'),
                        'genres' => $a->genres->pluck('name')->all() ?: ($configItem['genres'] ?? ['Action', 'Fantasy']),
                        'synopsis' => $a->synopsis,
                        'trending_rank' => $a->is_featured ? ($index + 1) : ($configItem['trending_rank'] ?? null),
                        'latest_ep' => 'EP ' . ($a->total_episodes ?? 12),
                        'latest_date' => 'Recently',
                        'schedule_day' => $configItem['schedule_day'] ?? 'SAT',
                        'schedule_time' => $configItem['schedule_time'] ?? '23:30',
                        'continue_progress' => $configItem['continue_progress'] ?? 50,
                        'continue_ep' => $configItem['continue_ep'] ?? 1,
                        'studio' => $a->studio ?? 'Studio',
                        'quality' => 'HD',
                        'sub' => true,
                        'dub' => true,
                    ];
                }
                if (!empty($mapped)) return $mapped;
            }
        } catch (\Throwable $e) {}

        return config('anime.anime', []);
    }
}

// 1. Homepage Route
Route::get('/', function () {
    $animeList = getAnimeData();
    
    // Split into sections
    $heroItems = array_values(array_filter($animeList, fn($a) => !empty($a['trending_rank'])));
    $trendingNow = array_values(array_filter($animeList, fn($a) => !empty($a['trending_rank'])));
    usort($trendingNow, fn($a, $b) => $a['trending_rank'] <=> $b['trending_rank']);

    $continueWatching = array_values(array_filter($animeList, fn($a) => isset($a['continue_progress']) && $a['continue_progress'] > 0));
    $latestEpisodes = array_slice($animeList, 0, 6);
    $popularAnime = $animeList;
    $recommended = array_slice($animeList, 4, 6);
    $genres = config('anime.genres', []);
    $scheduleDays = config('anime.schedule_days', []);

    return view('home', compact(
        'heroItems', 
        'trendingNow', 
        'continueWatching', 
        'latestEpisodes', 
        'popularAnime', 
        'recommended', 
        'genres', 
        'scheduleDays',
        'animeList'
    ));
});

// 2. Anime Explore Page Route
Route::get('/anime', function () {
    $animeList = getAnimeData();
    $genres = config('anime.genres', []);
    return view('anime.index', compact('animeList', 'genres'));
});

// 3. Anime Detail Page Route
    Route::get('/anime/{slug}', function ($slug) {
        $animeList = getAnimeData();
        $anime = collect($animeList)->firstWhere('slug', $slug) ?? $animeList[0];
        $recommended = array_values(array_filter($animeList, fn($a) => $a['slug'] !== $anime['slug']));

        // Load episodes from database
        $episodesList = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('episodes')) {
            $dbAnime = \App\Models\Anime::where('slug', $slug)->first();
            if ($dbAnime) {
                $dbEpisodes = $dbAnime->episodes()->orderBy('episode_number')->get();
                $episodesList = $dbEpisodes->map(fn($ep) => [
                    'number' => $ep->episode_number,
                    'title' => $ep->title,
                    'thumbnail' => $ep->thumbnail_url,
                    'duration' => $ep->duration ? floor($ep->duration / 60) . ' min' : '24 min',
                    'watched' => false,
                ])->toArray();
            }
        }

        return view('anime.show', compact('anime', 'recommended', 'episodesList'));
    });

// 4. Watch Page Route
    Route::get('/watch/{slug}/{episode?}', function ($slug, $episode = 1) {
        $animeList = getAnimeData();
        $anime = collect($animeList)->firstWhere('slug', $slug) ?? $animeList[0];
        $episodeNum = (int)$episode;

        // Load episodes from database
        $episodes = collect();
        $episodeId = null;
        if (\Illuminate\Support\Facades\Schema::hasTable('episodes')) {
            $dbAnime = \App\Models\Anime::where('slug', $slug)->first();
            if ($dbAnime) {
                $episodes = $dbAnime->episodes()->orderBy('episode_number')->get();
                $ep = $episodes->firstWhere('episode_number', $episodeNum);
                $episodeId = $ep?->id;
            }
        }

        return view('watch', compact('anime', 'episodeNum', 'animeList', 'episodeId', 'episodes'));
    });

// 5. Watchlist Page Route
Route::get('/watchlist', function () {
    $animeList = getAnimeData();
    return view('watchlist', compact('animeList'));
});

// 6. Watch History Page Route
Route::get('/history', function () {
    $animeList = getAnimeData();
    return view('history', compact('animeList'));
});

// 7. Auth Login Routes
Route::get('/login', function () {
    if (Auth::check()) {
        if (Auth::user()?->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect('/');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // Guard web eksplisit agar tidak tergantung default guard.
    if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        if ($request->user()?->isAdmin()) {
            return redirect()->intended('/admin');
        }
        return redirect()->intended('/');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

