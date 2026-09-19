<?php

namespace App\Providers;

use App\Models\Anime;
use App\Models\Episode;
use App\Models\Genre;
use App\Models\StreamSource;
use App\Models\Subtitle;
use App\Models\WatchHistory;
use App\Repositories\AnimeRepository;
use App\Repositories\Contracts\AnimeRepositoryInterface;
use App\Repositories\Contracts\EpisodeRepositoryInterface;
use App\Repositories\Contracts\GenreRepositoryInterface;
use App\Repositories\Contracts\StreamSourceRepositoryInterface;
use App\Repositories\Contracts\SubtitleRepositoryInterface;
use App\Repositories\Contracts\WatchHistoryRepositoryInterface;
use App\Repositories\EpisodeRepository;
use App\Repositories\GenreRepository;
use App\Repositories\StreamSourceRepository;
use App\Repositories\SubtitleRepository;
use App\Repositories\WatchHistoryRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AnimeRepositoryInterface::class, fn ($app) => new AnimeRepository($app->make(Anime::class)));
        $this->app->singleton(EpisodeRepositoryInterface::class, fn ($app) => new EpisodeRepository($app->make(Episode::class)));
        $this->app->singleton(GenreRepositoryInterface::class, fn ($app) => new GenreRepository($app->make(Genre::class)));
        $this->app->singleton(WatchHistoryRepositoryInterface::class, fn ($app) => new WatchHistoryRepository($app->make(WatchHistory::class)));
        $this->app->singleton(StreamSourceRepositoryInterface::class, fn ($app) => new StreamSourceRepository($app->make(StreamSource::class)));
        $this->app->singleton(SubtitleRepositoryInterface::class, fn ($app) => new SubtitleRepository($app->make(Subtitle::class)));
    }
}

