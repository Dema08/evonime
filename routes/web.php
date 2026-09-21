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
                            'trailer_url' => null,
                            'rating' => (float)($a->rating ?? 9.0),
                            'year' => (int)($a->year ?? 2024),
                            'type' => strtoupper($a->type ?? 'TV'),
                            'episodes' => (int)$epCount,
                            'status' => ucfirst($a->status ?? 'Ongoing'),
                            'genres' => $a->genres->pluck('name')->all() ?: ['Action', 'Fantasy'],
                            'synopsis' => $a->synopsis,
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

// 1. Homepage Route
Route::get('/', function () {
    $animeList = getAnimeData();
    
    // Split into sections
    $heroItems = array_values(array_filter($animeList, fn($a) => !empty($a['trending_rank'])));
    if (empty($heroItems) && !empty($animeList)) {
        $heroItems = array_slice($animeList, 0, 3);
    }
    
    $trendingNow = $animeList;
    $continueWatching = array_values(array_filter($animeList, fn($a) => isset($a['continue_progress']) && $a['continue_progress'] > 0));
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

// 6. Watch History Page Route
Route::get('/history', function () {
    $animeList = getAnimeData();
    return view('history', compact('animeList'));
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


