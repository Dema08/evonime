<?php

namespace Tests\Feature;

use App\Enums\VideoStatus;
use App\Models\Anime;
use App\Models\Episode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EpisodeAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_episodes_index_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $anime = Anime::create([
            'title' => 'Test Anime',
            'slug' => 'test-anime',
            'synopsis' => 'Test synopsis',
            'type' => 'tv',
            'status' => 'ongoing',
        ]);

        Episode::create([
            'anime_id' => $anime->id,
            'episode_number' => 1,
            'title' => 'Episode 1',
            'status' => VideoStatus::Ready,
            'duration' => 24,
            'views_count' => 100,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.animes.episodes.index', $anime));

        $response->assertStatus(200);
        $response->assertSee('ready');
    }
}
