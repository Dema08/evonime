<?php

use App\Http\Controllers\Admin\AnimeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EpisodeController;
use App\Http\Controllers\Admin\GenreController;
use App\Http\Controllers\Admin\StreamSourceController;
use App\Http\Controllers\Admin\SubtitleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    // Alias agar /admin/dashboard tetap jalan (dulu URL ini yang dipakai).
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Anime + toggle featured
    Route::patch('animes/{anime}/toggle-featured', [AnimeController::class, 'toggleFeatured'])->name('animes.toggle-featured');
    Route::resource('animes', AnimeController::class);

    // Episode nested di bawah anime (shallow agar edit/update/destroy pakai /episodes/{episode})
    Route::patch('episodes/{episode}/status', [EpisodeController::class, 'updateStatus'])->name('episodes.status');
    Route::resource('animes.episodes', EpisodeController::class)->shallow()->except(['show']);

    // Sources & subtitles nested di bawah episode (shallow)
    Route::resource('episodes.sources', StreamSourceController::class)->shallow()->parameters(['sources' => 'source'])->except(['show']);
    Route::resource('episodes.subtitles', SubtitleController::class)->shallow()->parameters(['subtitles' => 'subtitle'])->except(['show']);

    // Genre
    Route::resource('genres', GenreController::class)->except(['show']);

    // Users: hanya index, edit, update + toggle active
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
});
