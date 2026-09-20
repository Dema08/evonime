<?php

namespace Tests\Feature;

use App\Enums\VideoStatus;
use App\Models\Anime;
use App\Models\Episode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WatchPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Seed one anime (absolute external poster/banner URLs) + N episodes.
     */
    private function createBorutoWithEpisodes(int $count = 293): Anime
    {
        $anime = Anime::create([
            'title' => 'Boruto',
            'title_alternative' => 'Boruto Uzumaki',
            'slug' => 'boruto',
            'synopsis' => 'Boruto follow-up.',
            'type' => 'tv',
            'status' => 'ongoing',
            'release_date' => '2022-07-25',
            'rating' => 8.0,
            'total_episodes' => $count,
            'duration' => 24,
            'studio' => 'Studio Pierrot',
            'season' => 'winter',
            'year' => 2022,
            'poster_path' => 'https://otakudesu.blog/wp-content/uploads/2020/05/Boruto-Sub-Indo.jpg',
            'banner_path' => 'https://otakudesu.blog/wp-content/uploads/2020/05/Boruto-Banner.jpg',
            'is_published' => true,
            'is_featured' => true,
            'views_count' => 12345,
        ]);

        $rows = [];
        for ($i = 1; $i <= $count; $i++) {
            $rows[] = [
                'anime_id' => $anime->id,
                'episode_number' => $i,
                'title' => 'Episode ' . $i,
                'synopsis' => null,
                'duration' => 24,
                'thumbnail_path' => null,
                'aired_at' => now()->toDateTimeString(),
                'status' => VideoStatus::Ready->value,
                'views_count' => 0,
                'external_id_otakudesu' => null,
                'external_id_consumet' => null,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ];
        }
        Episode::insert($rows);

        return $anime;
    }

    public function test_anime_detail_renders_absolute_media_without_storage_wrapping(): void
    {
        $this->createBorutoWithEpisodes(293);

        $response = $this->get('/anime/boruto');

        $response->assertStatus(200);

        $html = $response->content();

        // BUG 1: absolute external poster/banner must NEVER be wrapped with /storage/
        $this->assertStringNotContainsString('/storage/https://', $html);
        $this->assertStringContainsString(
            'https://otakudesu.blog/wp-content/uploads/2020/05/Boruto-Sub-Indo.jpg',
            $html
        );
        $this->assertStringContainsString(
            'https://otakudesu.blog/wp-content/uploads/2020/05/Boruto-Banner.jpg',
            $html
        );
    }

    public function test_watch_page_sidebar_lists_all_episodes(): void
    {
        $this->createBorutoWithEpisodes(293);

        $response = $this->get('/watch/boruto/1');

        $response->assertStatus(200);

        $html = $response->content();

        // Backdrop uses the absolute banner — must not be wrapped in /storage/
        $this->assertStringNotContainsString('/storage/https://', $html);
        $this->assertStringContainsString(
            'https://otakudesu.blog/wp-content/uploads/2020/05/Boruto-Banner.jpg',
            $html
        );

        // BUG 2: sidebar must report the real episode count (not "0 EPS")
        $this->assertStringContainsString('293 EPS', $html);
        $this->assertStringNotContainsString('0 EPS', $html);
        $this->assertStringContainsString('CHAPTERS & EPISODES', $html);

        // Episode links should point to /watch/{slug}/{number}
        $this->assertStringContainsString('/watch/boruto/1', $html);
        $this->assertStringContainsString('/watch/boruto/293', $html);
    }

    public function test_watch_page_has_download_section_and_no_quality_selector(): void
    {
        $this->createBorutoWithEpisodes(5);

        $response = $this->get('/watch/boruto/1');

        $response->assertStatus(200);

        $html = $response->content();

        // BUG 3: non-functional quality-selector buttons must be gone
        $this->assertStringNotContainsString('quality-selector', $html);
        $this->assertStringNotContainsString('>360p<', $html);
        $this->assertStringNotContainsString('>480p<', $html);
        $this->assertStringNotContainsString('>720p<', $html);

        // Download section (server-rendered container) + player note must exist
        $this->assertStringContainsString('download-section', $html);
        $this->assertStringContainsString('Unduh Episode Ini', $html);
        $this->assertStringContainsString('player-note', $html);
        $this->assertStringContainsString('Tidak ada link download tersedia.', $html);
    }
}
