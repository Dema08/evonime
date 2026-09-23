<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VideoStreamProxyController;
use App\Http\Controllers\Web\HistoryController;
use App\Models\Anime;
use App\Models\User;
use App\Services\WatchHistoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

// Global helpers (getAnimeData, getContinueWatchingCards)
require_once app_path('Helpers/anime_helpers.php');

// 1. Homepage Route
Route::get('/', function () {
    $animeList = getAnimeData();

    // Filter hero items: anime with rating >= 9.0 OR is_featured == true
    $heroItems = array_values(array_filter($animeList, function ($a) {
        return ! empty($a['is_featured']) || (isset($a['rating']) && (float) $a['rating'] >= 9.0);
    }));

    // Fallback if no anime has rating >= 9.0 or is_featured yet: take top rated anime in database
    if (empty($heroItems) && ! empty($animeList)) {
        $sorted = $animeList;
        usort($sorted, fn ($a, $b) => ($b['rating'] <=> $a['rating']));
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

    if (! $anime) {
        $dbAnime = Anime::with(['genres', 'episodes'])->where('slug', $slug)->firstOrFail();
        $epCount = $dbAnime->episodes->count();
        $anime = [
            'id' => $dbAnime->id,
            'slug' => $dbAnime->slug,
            'title' => $dbAnime->title,
            'japanese_title' => $dbAnime->title_alternative ?? $dbAnime->title,
            'poster' => $dbAnime->poster_url,
            'banner' => $dbAnime->banner_url ?? $dbAnime->poster_url,
            'rating' => (float) ($dbAnime->rating ?? 9.0),
            'year' => (int) ($dbAnime->year ?? 2024),
            'type' => strtoupper($dbAnime->type ?? 'TV'),
            'episodes' => $epCount,
            'status' => ucfirst($dbAnime->status ?? 'Ongoing'),
            'genres' => $dbAnime->genres->pluck('name')->all(),
            'synopsis' => $dbAnime->synopsis,
            'studio' => $dbAnime->studio ?? 'Studio',
            'quality' => 'HD',
        ];
    }

    $recommended = array_values(array_filter($animeList, fn ($a) => $a['slug'] !== $anime['slug']));

    // Load episodes from database
    $episodesList = [];
    $dbAnime = Anime::where('slug', $slug)->first();
    if ($dbAnime) {
        $dbEpisodes = $dbAnime->episodes()->orderBy('episode_number')->get();
        $episodesList = $dbEpisodes->map(fn ($ep) => [
            'number' => (int) $ep->episode_number,
            'title' => $ep->title,
            'thumbnail' => $ep->thumbnail_url ?: ($dbAnime->banner_url ?: $dbAnime->poster_url),
            'duration' => $ep->duration ? floor($ep->duration / 60).' min' : '24 min',
            'watched' => false,
        ])->toArray();
    }

    return view('anime.show', compact('anime', 'recommended', 'episodesList'));
});

// 4. Watch Page Route
Route::get('/watch/{slug}/{episode?}', function ($slug, $episode = 1) {
    $animeList = getAnimeData();
    $anime = collect($animeList)->firstWhere('slug', $slug);
    $episodeNum = (int) $episode;

    $episodes = collect();
    $episodeId = null;

    $dbAnime = Anime::where('slug', $slug)->firstOrFail();
    if (! $anime) {
        $anime = [
            'id' => $dbAnime->id,
            'slug' => $dbAnime->slug,
            'title' => $dbAnime->title,
            'japanese_title' => $dbAnime->title_alternative ?? $dbAnime->title,
            'poster' => $dbAnime->poster_url,
            'banner' => $dbAnime->banner_url ?? $dbAnime->poster_url,
            'rating' => (float) ($dbAnime->rating ?? 9.0),
            'year' => (int) ($dbAnime->year ?? 2024),
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

// 4.1 Video Streaming Reverse Proxy (Google Drive 1080p HTTP Range Streamer)
Route::get('/stream/{anime}/{episode}/{quality?}', [VideoStreamProxyController::class, 'stream'])
    ->name('stream.proxy');

// 5. Watchlist Page Route
Route::get('/watchlist', function () {
    $animeList = getAnimeData();

    return view('watchlist', compact('animeList'));
});

// 6. Watch History Page Route (Lanjut Tonton / riwayat tontonan per user)
Route::middleware('auth')->group(function () {
    // Tracking episode dari halaman watch (browser: session + CSRF).
    // Endpoint API /api/v1/watch/record tetap ada untuk klien Bearer token.
    Route::post('/watch/record', [HistoryController::class, 'record'])->name('watch.record');
    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::delete('/history', [HistoryController::class, 'clear'])->name('history.clear');
    Route::patch('/history/{id}/complete', [HistoryController::class, 'complete'])
        ->whereNumber('id')->name('history.complete');
    Route::delete('/history/{id}', [HistoryController::class, 'destroy'])
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

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
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
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])
    ->name('auth.google');

Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])
    ->name('auth.google.callback');
