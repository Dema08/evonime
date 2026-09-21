<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ImportAnimeFromOtakudesu;
use App\Models\Anime;
use App\Services\Content\OtakudesuScraper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AnimeImportController extends Controller
{
    public function __construct(
        protected readonly OtakudesuScraper $scraper
    ) {}

    public function index()
    {
        return view('admin.anime-import');
    }

    public function search(Request $request)
    {
        $query = $request->input('q', '');
        if (empty($query)) {
            return response()->json(['results' => []]);
        }

        $results = $this->scraper->search($query);

        foreach ($results as &$item) {
            $existing = Anime::where('slug', $item['slug'])->first();
            $item['exists'] = $existing !== null;
            $item['anime_id'] = $existing?->id;
        }

        return response()->json(['results' => $results]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'slug' => ['required', 'string'],
        ]);

        $slug = $request->input('slug');

        Cache::put("import_progress_{$slug}", [
            'current' => 0,
            'total' => 0,
            'status' => 'queued',
            'title' => $slug,
        ], 3600);

        ImportAnimeFromOtakudesu::dispatch($slug);

        return response()->json([
            'success' => true,
            'message' => 'Proses import anime telah dimasukkan ke antrean background.',
            'slug' => $slug,
        ]);
    }

    public function status(Request $request, string $slug)
    {
        $progress = Cache::get("import_progress_{$slug}", [
            'current' => 0,
            'total' => 0,
            'status' => 'not_found',
            'title' => $slug,
        ]);

        return response()->json($progress);
    }
}
