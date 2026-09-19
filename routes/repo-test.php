<?php

use App\Services\AnimeService;
use App\Services\EpisodeService;
use App\Services\GenreService;
use App\Services\WatchHistoryService;
use Illuminate\Support\Facades\Route;

Route::get('/__repo-test', function (
    AnimeService $animeService,
    EpisodeService $episodeService,
    GenreService $genreService,
    WatchHistoryService $historyService,
) {
    $home = $animeService->getHomePageData();
    $detail = $animeService->getAnimeDetail('solo-leveling');
    $ep = $episodeService->getEpisodeDetail(1);
    $watch = $episodeService->getWatchPageData(1, 2);

    $sourcesRepo = app(App\Repositories\Contracts\StreamSourceRepositoryInterface::class);
    $subtitlesRepo = app(App\Repositories\Contracts\SubtitleRepositoryInterface::class);

    return response()->json([
        'bindings' => [
            'anime' => get_class(app(App\Repositories\Contracts\AnimeRepositoryInterface::class)),
            'episode' => get_class(app(App\Repositories\Contracts\EpisodeRepositoryInterface::class)),
            'genre' => get_class(app(App\Repositories\Contracts\GenreRepositoryInterface::class)),
            'history' => get_class(app(App\Repositories\Contracts\WatchHistoryRepositoryInterface::class)),
            'sources' => get_class($sourcesRepo),
            'subtitles' => get_class($subtitlesRepo),
        ],
        'home' => [
            'featured' => $home['featured']->count(),
            'latest' => $home['latest']->count(),
            'popular' => $home['popular']->count(),
        ],
        'detail' => $detail ? [
            'title' => $detail->title,
            'episodes' => $detail->episodes->count(),
            'related' => $detail->related->count(),
        ] : null,
        'episode' => $ep ? [
            'id' => $ep->id,
            'sources' => $sourcesRepo->getActiveByEpisode($ep->id)->count(),
            'subtitles' => $subtitlesRepo->getByEpisode($ep->id)->count(),
            'default_sub' => $subtitlesRepo->getDefault($ep->id)?->label,
            'next' => $ep->next?->episode_number,
            'prev' => $ep->prev?->episode_number,
        ] : null,
        'watch' => $watch ? [
            'has_episode' => (bool) ($watch['episode'] ?? null),
            'recommended' => count($watch['recommended']),
        ] : null,
        'genre_menu' => $genreService->getMenu()->count(),
        'catalog_total' => $animeService->getCatalog([], 15)->total(),
        'search_total' => $animeService->searchAnime('Solo', 15, 1)->total(),
        'progress_pct' => $historyService->progressPercent(2, 1),
    ]);
});

