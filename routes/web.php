<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Global helper to get all real anime data from database
if (! function_exists('getAnimeData')) {
    function getAnimeData() {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('animes')) {
                $dbAnimes = \App\Models\Anime::with(['genres', 'episodes'])->published()->get();
                if ($dbAnimes->count() > 0) {
                    $mapped = [];
                    foreach ($dbAnimes as $index => $a) {
                        $epCount = $a->episodes->count() > 0 ? $a->episodes->count() : ($a->total_episodes ?: 0);
                        $mapped[] = [
                            'id' => $a->id,
                            'slug' => $a->slug,
                            'title' => $a->title,
                            'japanese_title' => $a->title_alternative ?? $a->title,
                            'poster' => $a->poster_url ?? 'https://images.unsplash.com/photo-1578632767115-351597cf2477?q=80&w=800&auto=format&fit=crop',
                            'banner' => $a->banner_url ?? ($a->poster_url ?? 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?q=80&w=1600&auto=format&fit=crop'),
                            'trailer_url' => $a->trailer_url ?? null,
                            'rating' => (float)($a->rating ?? 9.0),
                            'year' => (int)($a->year ?? 2024),
                            'type' => strtoupper($a->type ?? 'TV'),
                            'episodes' => (int)$epCount,
                            'status' => ucfirst($a->status ?? 'Ongoing'),
                            'genres' => $a->genres->pluck('name')->all() ?: ['Action', 'Fantasy'],
                            'synopsis' => $a->synopsis,
                            'is_featured' => (bool)$a->is_featured,
                            'trending_rank' => $a->is_featured ? ($index + 1) : null,
                            'latest_ep' => 'EP ' . $epCount,
                            'latest_date' => 'Recently',
                            'schedule_day' => 'SAT',
                            'schedule_time' => '23:30',
                            'continue_progress' => 50,
                            'continue_ep' => 1,
                            'studio' => $a->studio ?? 'Studio',
                            'quality' => 'HD',
                            'sub' => true,
                            'dub' => true,
                        ];
                    }
                    return $mapped;
                }
            }
        } catch (\Throwable $e) {}

        return [];
    }
}

// Continue Watching (Lanjut Tonton) cards untuk user yang login.
// Tracking level-EPISODE: player iframe pihak ketiga cross-origin sehingga
// video.currentTime tidak bisa dibaca, jadi "lanjut" = episode terakhir dibuka.
if (! function_exists('getContinueWatchingCards')) {
    function getContinueWatchingCards(int $limit = 6): array
    {
        $userId = Auth::id();

        if (! $userId || ! \Illuminate\Support\Facades\Schema::hasTable('watch_histories')) {
            return [];
        }

        try {
            $histories = app(\App\Services\WatchHistoryService::class)->continueWatching($userId, $limit);
        } catch (\Throwable $e) {
            return [];
        }

        $cards = [];
        $seenAnime = [];

        foreach ($histories as $history) {
            $anime = $history->anime;   // accessor: episode->anime (sudah eager loaded)
            $episode = $history->episode;

            if (! $anime || ! $episode) {
                continue;
            }

            // 1 kartu per anime: ambil episode terakhir saja (histories sudah
            // diurutkan last_watched_at DESC, jadi kemunculan pertama = terbaru).
            if (isset($seenAnime[$anime->slug])) {
                continue;
            }
            $seenAnime[$anime->slug] = true;

            $cards[] = [
                'slug' => $anime->slug,
                'title' => $anime->title,
                'banner' => $anime->banner_url ?: ($anime->poster_url ?: 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?q=80&w=1600&auto=format&fit=crop'),
                'continue_ep' => (int) $episode->episode_number,
                'continue_progress' => $history->progressPercent(),
                'last_watched_at' => $history->last_watched_at,
            ];
        }

        return $cards;
    }
}

// 1. Homepage Route
Route::get('/', function () {
    $animeList = getAnimeData();
    
    // Filter hero items: anime with rating >= 9.0 OR is_featured == true
    $heroItems = array_values(array_filter($animeList, function($a) {
        return !empty($a['is_featured']) || (isset($a['rating']) && (float)$a['rating'] >= 9.0);
    }));

    // Fallback if no anime has rating >= 9.0 or is_featured yet: take top rated anime in database
    if (empty($heroItems) && !empty($animeList)) {
        $sorted = $animeList;
        usort($sorted, fn($a, $b) => ($b['rating'] <=> $a['rating']));
        $heroItems = array_slice($sorted, 0, 5);
    }
    
    $trendingNow = $animeList;
    $continueWatching = getContinueWatchingCards(6);
    $latestEpisodes = array_slice($animeList, 0, 6);
    $popularAnime = $animeList;
    $recommended = array_slice($animeList, 0, 6);
    $genres = config('anime.genres', ['Action', 'Adventure', 'Fantasy', 'Shounen', 'Supernatural']);
    $scheduleDays = config('anime.schedule_days', ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN']);

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
    $genres = config('anime.genres', ['Action', 'Adventure', 'Fantasy', 'Shounen', 'Supernatural']);
    return view('anime.index', compact('animeList', 'genres'));
});

// 3. Anime Detail Page Route
Route::get('/anime/{slug}', function ($slug) {
    $animeList = getAnimeData();
    $anime = collect($animeList)->firstWhere('slug', $slug);

    if (!$anime) {
        $dbAnime = \App\Models\Anime::with(['genres', 'episodes'])->where('slug', $slug)->firstOrFail();
        $epCount = $dbAnime->episodes->count();
        $anime = [
            'id' => $dbAnime->id,
            'slug' => $dbAnime->slug,
            'title' => $dbAnime->title,
            'japanese_title' => $dbAnime->title_alternative ?? $dbAnime->title,
            'poster' => $dbAnime->poster_url,
            'banner' => $dbAnime->banner_url ?? $dbAnime->poster_url,
            'rating' => (float)($dbAnime->rating ?? 9.0),
            'year' => (int)($dbAnime->year ?? 2024),
            'type' => strtoupper($dbAnime->type ?? 'TV'),
            'episodes' => $epCount,
            'status' => ucfirst($dbAnime->status ?? 'Ongoing'),
            'genres' => $dbAnime->genres->pluck('name')->all(),
            'synopsis' => $dbAnime->synopsis,
            'studio' => $dbAnime->studio ?? 'Studio',
            'quality' => 'HD',
        ];
    }

    $recommended = array_values(array_filter($animeList, fn($a) => $a['slug'] !== $anime['slug']));

    // Load episodes from database
    $episodesList = [];
    $dbAnime = \App\Models\Anime::where('slug', $slug)->first();
    if ($dbAnime) {
        $dbEpisodes = $dbAnime->episodes()->orderBy('episode_number')->get();
        $episodesList = $dbEpisodes->map(fn($ep) => [
            'number' => (int)$ep->episode_number,
            'title' => $ep->title,
            'thumbnail' => $ep->thumbnail_url ?: ($dbAnime->banner_url ?: $dbAnime->poster_url),
            'duration' => $ep->duration ? floor($ep->duration / 60) . ' min' : '24 min',
            'watched' => false,
        ])->toArray();
    }

    return view('anime.show', compact('anime', 'recommended', 'episodesList'));
});

// 4. Watch Page Route
Route::get('/watch/{slug}/{episode?}', function ($slug, $episode = 1) {
    $animeList = getAnimeData();
    $anime = collect($animeList)->firstWhere('slug', $slug);
    $episodeNum = (int)$episode;

    $episodes = collect();
    $episodeId = null;

    $dbAnime = \App\Models\Anime::where('slug', $slug)->firstOrFail();
    if (!$anime) {
        $anime = [
            'id' => $dbAnime->id,
            'slug' => $dbAnime->slug,
            'title' => $dbAnime->title,
            'japanese_title' => $dbAnime->title_alternative ?? $dbAnime->title,
            'poster' => $dbAnime->poster_url,
            'banner' => $dbAnime->banner_url ?? $dbAnime->poster_url,
            'rating' => (float)($dbAnime->rating ?? 9.0),
            'year' => (int)($dbAnime->year ?? 2024),
            'type' => strtoupper($dbAnime->type ?? 'TV'),
            'episodes' => $dbAnime->episodes->count(),
            'status' => ucfirst($dbAnime->status ?? 'Ongoing'),
            'genres' => $dbAnime->genres->pluck('name')->all(),
            'synopsis' => $dbAnime->synopsis,
            'studio' => $dbAnime->studio ?? 'Studio',
            'quality' => 'HD',
        ];
    }

    $episodes = $dbAnime->episodes()->orderBy('episode_number')->get();
    $ep = $episodes->firstWhere('episode_number', $episodeNum);
    $episodeId = $ep?->id;

    return view('watch', compact('anime', 'episodeNum', 'animeList', 'episodeId', 'episodes'));
});

// 5. Watchlist Page Route
Route::get('/watchlist', function () {
    $animeList = getAnimeData();
    return view('watchlist', compact('animeList'));
});

// 6. Watch History Page Route (Lanjut Tonton / riwayat tontonan per user)
Route::middleware('auth')->group(function () {
    // Tracking episode dari halaman watch (browser: session + CSRF).
    // Endpoint API /api/v1/watch/record tetap ada untuk klien Bearer token.
    Route::post('/watch/record', [\App\Http\Controllers\Web\HistoryController::class, 'record'])->name('watch.record');
    Route::get('/history', [\App\Http\Controllers\Web\HistoryController::class, 'index'])->name('history');
    Route::delete('/history', [\App\Http\Controllers\Web\HistoryController::class, 'clear'])->name('history.clear');
    Route::patch('/history/{id}/complete', [\App\Http\Controllers\Web\HistoryController::class, 'complete'])
        ->whereNumber('id')->name('history.complete');
    Route::delete('/history/{id}', [\App\Http\Controllers\Web\HistoryController::class, 'destroy'])
        ->whereNumber('id')->name('history.destroy');
});

// 6b. User Profile Page Route
Route::get('/profile', function () {
    $user = Auth::user();
    $animeList = getAnimeData();
    return view('profile', compact('user', 'animeList'));
})->name('profile')->middleware('auth');

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

// 8. Auth Register Routes
Route::get('/register', function () {
    if (Auth::check()) {
        return redirect('/');
    }
    return view('auth.register');
})->name('register');

Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $user = \App\Models\User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        'role' => 'user',
        'is_active' => true,
    ]);

    Auth::login($user);
    $request->session()->regenerate();

    return redirect()->intended('/');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// 9. Google OAuth Routes
Route::get('/auth/google', [\App\Http\Controllers\AuthController::class, 'redirectToGoogle'])
    ->name('auth.google');

Route::get('/auth/google/callback', [\App\Http\Controllers\AuthController::class, 'handleGoogleCallback'])
    ->name('auth.google.callback');


