<?php

use App\Models\Anime;
use App\Services\WatchHistoryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

if (! function_exists('getAnimeData')) {
    function getAnimeData(): array
    {
        try {
            if (Schema::hasTable('animes')) {
                $dbAnimes = Anime::with(['genres', 'episodes'])->published()->get();
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
                            'rating' => (float) ($a->rating ?? 9.0),
                            'year' => (int) ($a->year ?? 2024),
                            'type' => strtoupper($a->type ?? 'TV'),
                            'episodes' => (int) $epCount,
                            'status' => ucfirst($a->status ?? 'Ongoing'),
                            'genres' => $a->genres->pluck('name')->all() ?: ['Action', 'Fantasy'],
                            'synopsis' => $a->synopsis,
                            'is_featured' => (bool) $a->is_featured,
                            'trending_rank' => $a->is_featured ? ($index + 1) : null,
                            'latest_ep' => 'EP '.$epCount,
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
        } catch (\Throwable $e) {
        }

        return [];
    }
}

if (! function_exists('getContinueWatchingCards')) {
    function getContinueWatchingCards(int $limit = 6): array
    {
        $userId = Auth::id();

        if (! $userId || ! Schema::hasTable('watch_histories')) {
            return [];
        }

        try {
            $histories = app(WatchHistoryService::class)->continueWatching($userId, $limit);
        } catch (\Throwable $e) {
            return [];
        }

        $cards = [];
        $seenAnime = [];

        foreach ($histories as $history) {
            $anime = $history->anime;
            $episode = $history->episode;

            if (! $anime || ! $episode) {
                continue;
            }

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
