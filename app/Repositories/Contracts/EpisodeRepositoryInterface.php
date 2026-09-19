<?php

namespace App\Repositories\Contracts;

use App\Enums\VideoStatus;
use App\Models\Episode;
use Illuminate\Database\Eloquent\Collection;

interface EpisodeRepositoryInterface extends RepositoryInterface
{
    /** List episode per anime + sources aktif & subtitles, order number asc, cache 1 jam. */
    public function getByAnime(int $animeId, array $filters = []): Collection;

    /** Detail episode + anime + sources + subtitles. */
    public function findWithRelations(int $id): ?Episode;

    /** Episode ready terbaru + anime minimal, untuk homepage/updates. */
    public function getLatestReady(int $limit = 10): Collection;

    /** Episode berikutnya by episode_number. */
    public function getNextEpisode(int $animeId, int $currentEpisodeNumber): ?Episode;

    /** Episode sebelumnya by episode_number. */
    public function getPrevEpisode(int $animeId, int $currentEpisodeNumber): ?Episode;

    /** Atomic increment views_count tanpa load model. */
    public function incrementViews(int $id): int;

    /** Update status massal dalam 1 query. */
    public function bulkUpdateStatus(array $ids, VideoStatus $status): int;

    /** List admin paginasi: filter anime + search + status, eager anime minimal. */
    public function getAdminPaginated(?int $animeId = null, array $filters = [], int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator;
}
