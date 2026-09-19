<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnimeResource;
use App\Services\AnimeService;
use Illuminate\Http\Request;

class AnimeController extends Controller
{
    public function __construct(protected readonly AnimeService $service) {}

    public function index(Request $request)
    {
        $data = $this->service->getCatalog($request->only(['status', 'type', 'year', 'genre']), 15);
        return AnimeResource::collection($data);
    }

    public function home(AnimeService $service)
    {
        $data = $service->getHomePageData();
        return response()->json(['data' => [
            'featured' => AnimeResource::collection($data['featured']),
            'latest' => AnimeResource::collection($data['latest']),
            'popular' => AnimeResource::collection($data['popular']),
        ]]);
    }

    public function show(string $slug, AnimeService $service)
    {
        $anime = $service->getAnimeDetail($slug);
        abort_if(! $anime, 404);
        return new AnimeResource($anime);
    }

    public function search(Request $request, AnimeService $service)
    {
        $request->validate([
            'q' => 'required|string|min:1|max:100',
            'page' => 'sometimes|integer|min:1',
        ]);
        $page = (int) $request->input('page', 1);
        return AnimeResource::collection($service->searchAnime($request->string('q')->toString(), 15, $page));
    }
}

