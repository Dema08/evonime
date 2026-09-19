<?php

namespace App\Actions\Admin;

use App\Models\Genre;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class StoreGenreAction
{
    public function __invoke(array $data): Genre
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $genre = Genre::create($data);
        Cache::forget('genre:menu');
        Cache::forget('genre:list');
        return $genre;
    }
}
