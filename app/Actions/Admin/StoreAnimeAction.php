<?php

namespace App\Actions\Admin;

use App\Models\Anime;
use App\Services\AnimeService;
use App\Services\FileUploadService;
use Illuminate\Support\Str;

class StoreAnimeAction
{
    public function __construct(
        protected readonly FileUploadService $fileUpload,
        protected readonly AnimeService $animeService,
    ) {}

    public function __invoke(array $data): Anime
    {
        if (isset($data['poster']) && $data['poster'] instanceof \Illuminate\Http\UploadedFile) {
            $data['poster_path'] = $this->fileUpload->uploadImage($data['poster'], 'posters');
        }
        if (isset($data['banner']) && $data['banner'] instanceof \Illuminate\Http\UploadedFile) {
            $data['banner_path'] = $this->fileUpload->uploadImage($data['banner'], 'banners');
        }

        $genres = $data['genres'] ?? [];
        unset($data['poster'], $data['banner'], $data['genres']);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $anime = Anime::create($data);
        if (! empty($genres)) {
            $anime->genres()->sync($genres);
        }

        $this->animeService->invalidateCache($anime->id);

        return $anime;
    }
}
