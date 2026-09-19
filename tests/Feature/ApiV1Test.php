<?php

namespace Tests\Feature;

use App\Enums\VideoQuality;
use App\Enums\VideoStatus;
use App\Models\Anime;
use App\Models\Episode;
use App\Models\Genre;
use App\Models\StreamSource;
use App\Models\Subtitle;
use App\Models\User;
use App\Models\WatchHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiV1Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_home_endpoint_returns_expected_structure(): void
    {
        $response = $this->getJson('/api/v1/home');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'featured',
                    'latest',
                    'popular',
                    'genres',
                ],
            ]);
    }

    public function test_animes_index_returns_paginated_response(): void
    {
        Anime::create([
            'title' => 'Frieren: Beyond Journey\'s End',
            'slug' => 'frieren',
            'synopsis' => 'An elf mage after the demon king was defeated.',
            'type' => 'tv',
            'status' => 'ongoing',
            'is_published' => true,
        ]);

        $response = $this->getJson('/api/v1/animes?per_page=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'slug',
                        'type',
                        'status',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page',
                ],
                'links' => [
                    'first',
                    'last',
                ],
            ]);
    }

    public function test_anime_detail_episodes_related_and_search(): void
    {
        $anime = Anime::create([
            'title' => 'Solo Leveling',
            'slug' => 'solo-leveling',
            'synopsis' => 'Sung Jin-woo becomes the shadow monarch.',
            'type' => 'tv',
            'status' => 'ongoing',
            'is_published' => true,
        ]);

        Episode::create([
            'anime_id' => $anime->id,
            'episode_number' => 1,
            'title' => 'I\'m Used to It',
            'status' => VideoStatus::Ready,
            'duration' => 24,
        ]);

        // Detail
        $this->getJson('/api/v1/animes/solo-leveling')
            ->assertStatus(200)
            ->assertJsonPath('data.title', 'Solo Leveling');

        // Episodes
        $this->getJson('/api/v1/animes/solo-leveling/episodes')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // Related
        $this->getJson('/api/v1/animes/solo-leveling/related')
            ->assertStatus(200);

        // Search
        $this->getJson('/api/v1/animes/search?q=Solo')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_episode_detail_sources_subtitles_and_latest(): void
    {
        $anime = Anime::create([
            'title' => 'Demon Slayer',
            'slug' => 'demon-slayer',
            'synopsis' => 'Tanjiro battles demons.',
            'type' => 'tv',
            'status' => 'ongoing',
            'is_published' => true,
        ]);

        $episode = Episode::create([
            'anime_id' => $anime->id,
            'episode_number' => 1,
            'title' => 'Cruelty',
            'status' => VideoStatus::Ready,
            'duration' => 24,
        ]);

        StreamSource::create([
            'episode_id' => $episode->id,
            'server_name' => 'FastCDN',
            'quality' => VideoQuality::P1080,
            'url' => 'https://cdn.example.com/ep1.m3u8',
            'format' => 'hls',
            'is_active' => true,
            'priority' => 1,
        ]);

        Subtitle::create([
            'episode_id' => $episode->id,
            'language' => 'id',
            'label' => 'Indonesian',
            'format' => 'vtt',
            'url' => 'https://cdn.example.com/ep1_id.vtt',
            'is_default' => true,
        ]);

        // Detail
        $this->getJson("/api/v1/episodes/{$episode->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.title', 'Cruelty');

        // Sources
        $this->getJson("/api/v1/episodes/{$episode->id}/sources")
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // Subtitles
        $this->getJson("/api/v1/episodes/{$episode->id}/subtitles")
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // Latest
        $this->getJson('/api/v1/episodes/latest')
            ->assertStatus(200);
    }

    public function test_genres_endpoints(): void
    {
        $genre = Genre::create([
            'name' => 'Action',
            'slug' => 'action',
            'description' => 'Exciting action series',
        ]);

        $this->getJson('/api/v1/genres')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        $this->getJson('/api/v1/genres/action')
            ->assertStatus(200)
            ->assertJsonPath('data.genre.slug', 'action');
    }

    public function test_stream_url_generation(): void
    {
        $anime = Anime::create([
            'title' => 'Stream Test Anime',
            'slug' => 'stream-test',
            'synopsis' => 'Test synopsis',
            'type' => 'tv',
            'status' => 'ongoing',
            'is_published' => true,
        ]);

        $episode = Episode::create([
            'anime_id' => $anime->id,
            'episode_number' => 1,
            'title' => 'Test EP',
            'status' => VideoStatus::Ready,
            'duration' => 20,
        ]);

        $response = $this->postJson('/api/v1/stream/url', [
            'episode_id' => $episode->id,
            'quality' => '1080p',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'stream_url',
                    'token',
                    'expires_at',
                ],
            ]);
    }

    public function test_auth_register_login_me_logout(): void
    {
        // Register
        $registerRes = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => 'newuser@example.com',
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
        ]);

        $registerRes->assertStatus(201)
            ->assertJsonPath('data.user.email', 'newuser@example.com')
            ->assertJsonStructure(['data' => ['token', 'token_type']]);

        // Login
        $loginRes = $this->postJson('/api/v1/auth/login', [
            'email' => 'newuser@example.com',
            'password' => 'secret12345',
        ]);

        $loginRes->assertStatus(200);
        $token = $loginRes->json('data.token');

        // Me
        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/auth/me')
            ->assertStatus(200)
            ->assertJsonPath('data.name', 'Test User');

        // Logout
        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/auth/logout')
            ->assertStatus(200);
    }

    public function test_unauthenticated_request_returns_401(): void
    {
        $this->getJson('/api/v1/auth/me')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_validation_error_returns_422(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'not-an-email',
            // missing password
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => ['email', 'password'],
            ]);
    }

    public function test_watch_history_and_progress(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $anime = Anime::create([
            'title' => 'Watch Progress Anime',
            'slug' => 'watch-progress-anime',
            'synopsis' => 'Test',
            'type' => 'tv',
            'status' => 'ongoing',
            'is_published' => true,
        ]);

        $episode = Episode::create([
            'anime_id' => $anime->id,
            'episode_number' => 1,
            'title' => 'Episode 1',
            'status' => VideoStatus::Ready,
            'duration' => 1440,
        ]);

        // Track Progress
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/watch/progress', [
                'episode_id' => $episode->id,
                'progress_seconds' => 600,
                'duration_seconds' => 1440,
            ])
            ->assertStatus(200);

        // Record history
        WatchHistory::updateOrCreate(
            ['user_id' => $user->id, 'episode_id' => $episode->id],
            [
                'progress_seconds' => 600,
                'duration_seconds' => 1440,
                'completed' => false,
                'last_watched_at' => now(),
            ]
        );

        // Watch Show
        $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/watch/{$episode->id}")
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'episode',
                    'history',
                    'continue',
                    'recommended',
                ],
            ]);

        // Continue Watching
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/watch/continue')
            ->assertStatus(200);

        // History
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/watch/history')
            ->assertStatus(200);

        // Delete History
        $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/watch/history/{$episode->id}")
            ->assertStatus(200);
    }

    public function test_user_profile_and_password_update(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword123'),
        ]);

        // Update Profile
        $this->actingAs($user, 'sanctum')
            ->patchJson('/api/v1/user/profile', [
                'name' => 'Updated User Name',
            ])
            ->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated User Name');

        // Update Password
        $this->actingAs($user, 'sanctum')
            ->patchJson('/api/v1/user/password', [
                'current_password' => 'oldpassword123',
                'password' => 'newpassword12345',
                'password_confirmation' => 'newpassword12345',
            ])
            ->assertStatus(200);
    }
}
