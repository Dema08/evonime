<?php

namespace App\Actions\Admin;

use App\Models\Anime;
use App\Services\AnimeService;
use App\Services\FileUploadService;

class DeleteAnimeAction
{
    public function __construct(
        protected readonly FileUploadService $fileUpload,
        protected readonly AnimeService $animeService,
    ) {}

    public function __invoke(Anime $anime): bool
    {
        $this->fileUpload->deleteFile($anime->poster_path);
        $this->fileUpload->deleteFile($anime->banner_path);

        $animeId = $anime->id;
        $deleted = $anime->delete();

        $this->animeService->invalidateCache($animeId);

        return $deleted;
    }
}
