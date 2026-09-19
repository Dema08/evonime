<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anime;
use App\Models\Episode;
use App\Models\User;
use App\Services\AnimeService;
use App\Services\EpisodeService;

class DashboardController extends Controller
{
    public function __construct(
        protected readonly AnimeService $animeService,
        protected readonly EpisodeService $episodeService,
    ) {}

    public function index()
    {
        $totalAnimes = Anime::count();
        $totalEpisodes = Episode::count();
        $totalUsers = User::count();
        $totalViews = (int) Anime::sum('views_count') + (int) Episode::sum('views_count');

        $latestAnimes = Anime::latest()->limit(8)->get(['id', 'title', 'slug', 'status', 'type', 'rating', 'poster_path', 'created_at']);
        $latestEpisodes = Episode::with('anime:id,title,slug')->latest('aired_at')->limit(5)->get(['id', 'anime_id', 'episode_number', 'title', 'aired_at']);
        $topAnimes = Anime::orderByDesc('views_count')->limit(5)->get(['id', 'title', 'slug', 'views_count', 'poster_path']);

        return view('admin.dashboard', compact(
            'totalAnimes',
            'totalEpisodes',
            'totalUsers',
            'totalViews',
            'latestAnimes',
            'latestEpisodes',
            'topAnimes'
        ));
    }
}

