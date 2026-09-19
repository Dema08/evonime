<?php

use Illuminate\Support\Facades\Route;

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

// 7. Auth Login Page Route
Route::get('/login', function () {
    return view('auth.login');
});

// 8. Auth Register Page Route
Route::get('/register', function () {
    return view('auth.register');
});
