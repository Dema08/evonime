<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Global helper to get all anime data
function getAnimeData() {
    return config('anime.anime', []);
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
    
    return view('anime.show', compact('anime', 'recommended'));
});

// 4. Watch Page Route
Route::get('/watch/{slug}/{episode?}', function ($slug, $episode = 1) {
    $animeList = getAnimeData();
    $anime = collect($animeList)->firstWhere('slug', $slug) ?? $animeList[0];
    $episodeNum = (int)$episode;
    
    return view('watch', compact('anime', 'episodeNum', 'animeList'));
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
        return redirect('/admin/dashboard');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended('/admin/dashboard');
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

// 8. Admin Routes
Route::get('/admin', function () {
    return redirect('/admin/dashboard');
});

Route::get('/admin/dashboard', function () {
    $animeList = getAnimeData();
    $totalAnime = count($animeList);
    $totalEpisodes = array_sum(array_column($animeList, 'episodes'));
    $genres = config('anime.genres', []);
    $totalGenres = count($genres);

    $heroItems = array_values(array_filter($animeList, fn($a) => !empty($a['trending_rank'])));
    usort($heroItems, fn($a, $b) => ($a['trending_rank'] ?? 99) <=> ($b['trending_rank'] ?? 99));

    $topRatedItems = array_values(array_filter($animeList, fn($a) => isset($a['rating']) && $a['rating'] >= 9.5));
    usort($topRatedItems, fn($a, $b) => $b['rating'] <=> $a['rating']);

    return view('admin.dashboard', compact('animeList', 'totalAnime', 'totalEpisodes', 'totalGenres', 'heroItems', 'topRatedItems'));
})->name('admin.dashboard');

// Dedicated Hero Management Page
Route::get('/admin/hero', function () {
    $animeList = getAnimeData();
    $heroItems = array_values(array_filter($animeList, fn($a) => !empty($a['trending_rank'])));
    usort($heroItems, fn($a, $b) => ($a['trending_rank'] ?? 99) <=> ($b['trending_rank'] ?? 99));

    return view('admin.hero', compact('animeList', 'heroItems'));
})->name('admin.hero');

// Dedicated Top-Rated Anime Management Page
Route::get('/admin/top-rated', function () {
    $animeList = getAnimeData();
    $topRatedItems = array_values(array_filter($animeList, fn($a) => isset($a['rating']) && $a['rating'] >= 9.5));
    usort($topRatedItems, fn($a, $b) => $b['rating'] <=> $a['rating']);

    return view('admin.top-rated', compact('animeList', 'topRatedItems'));
})->name('admin.top-rated');

// Dedicated Anime Catalog Management Page
Route::get('/admin/anime', function () {
    $animeList = getAnimeData();
    return view('admin.anime', compact('animeList'));
})->name('admin.anime');
