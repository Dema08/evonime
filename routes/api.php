<?php

use App\Http\Controllers\Api\AnimeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EpisodeController;
use App\Http\Controllers\Api\GenreController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\StreamController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WatchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API V1 Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->middleware(['api', 'throttle:api'])->group(function () {

    // 1. Home
    Route::get('/home', [HomeController::class, 'index'])->name('api.v1.home');

    // 2. Anime & Search
    Route::get('/animes/search', [AnimeController::class, 'search'])->name('api.v1.animes.search');
    Route::get('/animes', [AnimeController::class, 'index'])->name('api.v1.animes.index');
    Route::get('/animes/{slug}', [AnimeController::class, 'show'])->name('api.v1.animes.show');
    Route::get('/animes/{slug}/episodes', [AnimeController::class, 'episodes'])->name('api.v1.animes.episodes');
    Route::get('/animes/{slug}/related', [AnimeController::class, 'related'])->name('api.v1.animes.related');

    // 3. Episodes
    Route::get('/episodes/latest', [EpisodeController::class, 'latest'])->name('api.v1.episodes.latest');
    Route::get('/episodes/{id}', [EpisodeController::class, 'show'])->whereNumber('id')->name('api.v1.episodes.show');
    Route::get('/episodes/{id}/sources', [EpisodeController::class, 'sources'])->whereNumber('id')->name('api.v1.episodes.sources');
    Route::get('/episodes/{id}/subtitles', [EpisodeController::class, 'subtitles'])->whereNumber('id')->name('api.v1.episodes.subtitles');

    // 4. Genres
    Route::get('/genres', [GenreController::class, 'index'])->name('api.v1.genres.index');
    Route::get('/genres/{slug}', [GenreController::class, 'show'])->name('api.v1.genres.show');

    // 5. Stream Token / URL Generator
    Route::post('/stream/url', [StreamController::class, 'url'])->name('api.v1.stream.url');
    Route::get('/stream/sources/{episodeId}', [StreamController::class, 'sources'])->whereNumber('episodeId')->name('api.v1.stream.sources');

    // 6. Authentication (Public)
    Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:auth')->name('api.v1.auth.register');
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:auth')->name('api.v1.auth.login');

    // 7. Authenticated Routes (auth:sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::get('/auth/me', [AuthController::class, 'me'])->name('api.v1.auth.me');
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.v1.auth.logout');

        // Watch & History
        Route::get('/watch/continue', [WatchController::class, 'continueWatching'])->name('api.v1.watch.continue');
        Route::get('/watch/history', [WatchController::class, 'history'])->name('api.v1.watch.history');
        Route::delete('/watch/history/{episodeId}', [WatchController::class, 'destroyHistory'])->whereNumber('episodeId')->name('api.v1.watch.history.destroy');
        Route::get('/watch/{episodeId}', [WatchController::class, 'show'])->whereNumber('episodeId')->name('api.v1.watch.show');
        Route::post('/watch/progress', [WatchController::class, 'track'])->middleware('throttle:progress')->name('api.v1.watch.progress');

        // User Profile & Password
        Route::get('/user/profile', [UserController::class, 'profile'])->name('api.v1.user.profile');
        Route::patch('/user/profile', [UserController::class, 'updateProfile'])->name('api.v1.user.profile.update');
        Route::patch('/user/password', [UserController::class, 'updatePassword'])->name('api.v1.user.password.update');
    });
});
