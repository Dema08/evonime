<?php

namespace Tests\Feature;

use App\Enums\VideoStatus;
use App\Models\Anime;
use App\Models\Episode;
use App\Models\User;
use App\Models\WatchHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Continue Watching (Lanjut Tonton) — tracking level EPISODE.
 *
 * Video diputar via iframe pihak ketiga yang cross-origin, jadi
 * currentTime tidak bisa dibaca dan progres tidak dikirim dari browser.
 */
class ContinueWatchingTest extends TestCase
{
    use RefreshDatabase;

    private function makeAnimeWithEpisodes(string $slug = 'boruto', int $count = 3): Anime
    {
        $anime = Anime::create([
            'title' => 'Boruto',
            'title_alternative' => 'Boruto Uzumaki',
            'slug' => $slug,
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
            'views_count' => 0,
        ]);

        $rows = [];
        for ($i = 1; $i <= $count; $i++) {
            $rows[] = [
                'anime_id' => $anime->id,
                'episode_number' => $i,
                'title' => 'Episode ' . $i,
                'synopsis' => null,
                'duration' => 1440,
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

    private function episodeOf(Anime $anime, int $number): Episode
    {
        return Episode::where('anime_id', $anime->id)->where('episode_number', $number)->firstOrFail();
    }

    public function test_guest_cannot_record_watch_history(): void
    {
        $anime = $this->makeAnimeWithEpisodes();
        $ep = $this->episodeOf($anime, 1);

        $this->postJson('/api/v1/watch/record', ['episode_id' => $ep->id])->assertStatus(401);

        $this->assertDatabaseCount('watch_histories', 0);
    }

    public function test_authenticated_user_records_episode_level_history(): void
    {
        $anime = $this->makeAnimeWithEpisodes(count: 5);
        $ep = $this->episodeOf($anime, 5);
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/watch/record', ['episode_id' => $ep->id])
            ->assertStatus(200)
            ->assertJsonPath('data.episode_id', $ep->id)
            ->assertJsonPath('data.completed', false);

        $this->assertDatabaseHas('watch_histories', [
            'user_id' => $user->id,
            'episode_id' => $ep->id,
            'progress_seconds' => 0,
            'completed' => false,
        ]);
    }

    public function test_recording_again_updates_last_watched_without_resetting_progress(): void
    {
        $anime = $this->makeAnimeWithEpisodes(count: 5);
        $ep = $this->episodeOf($anime, 5);
        $user = User::factory()->create();

        WatchHistory::create([
            'user_id' => $user->id,
            'episode_id' => $ep->id,
            'progress_seconds' => 120,
            'duration_seconds' => 1440,
            'completed' => false,
            'last_watched_at' => now()->subDay(),
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/watch/record', ['episode_id' => $ep->id])
            ->assertStatus(200);

        $this->assertDatabaseCount('watch_histories', 1);

        $row = WatchHistory::where('user_id', $user->id)->firstOrFail();
        $this->assertSame(120, $row->progress_seconds);
        $this->assertTrue($row->last_watched_at->greaterThan(now()->subMinute()));
    }

    public function test_record_validates_episode_id(): void
    {
        $this->makeAnimeWithEpisodes();
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/watch/record', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['episode_id']);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/watch/record', ['episode_id' => 999999])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['episode_id']);
    }

    public function test_continue_endpoint_returns_latest_episodes_and_hides_completed(): void
    {
        $anime = $this->makeAnimeWithEpisodes(count: 5);
        $user = User::factory()->create();

        foreach ([1, 3] as $number) {
            $this->actingAs($user, 'sanctum')
                ->postJson('/api/v1/watch/record', ['episode_id' => $this->episodeOf($anime, $number)->id])
                ->assertStatus(200);
        }

        $continue = $this->actingAs($user, 'sanctum')->getJson('/api/v1/watch/continue');
        $continue->assertStatus(200);
        $this->assertCount(2, $continue->json('data'));

        // Episode 3 dicatat terakhir → harus muncul lebih dulu.
        $this->assertSame(3, $continue->json('data.0.episode.episode_number'));

        // Tandai episode 3 selesai (lewat service, agar cache ikut di-invalidate)
        // → hilang dari continue watching.
        $row = WatchHistory::where('user_id', $user->id)
            ->where('episode_id', $this->episodeOf($anime, 3)->id)->firstOrFail();
        app(\App\Services\WatchHistoryService::class)->markCompleted($user->id, $row->id);

        $continue2 = $this->actingAs($user, 'sanctum')->getJson('/api/v1/watch/continue');
        $this->assertCount(1, $continue2->json('data'));
        $this->assertSame(1, $continue2->json('data.0.episode.episode_number'));
    }

    public function test_watch_page_pushes_episode_tracking_script(): void
    {
        $anime = $this->makeAnimeWithEpisodes(count: 5);
        $user = User::factory()->create();

        // Guest → simpan lokal, tanpa memanggil API.
        $guest = $this->get('/watch/boruto/5');
        $guest->assertStatus(200);
        $guest->assertSee('evonime_watch_history', false);
        $guest->assertDontSee('Record watch failed', false);

        // User login → kirim ke endpoint tracking (URL di-escape oleh @json).
        $auth = $this->actingAs($user)->get('/watch/boruto/5');
        $auth->assertStatus(200);
        $auth->assertSee('Record watch failed', false);
        $auth->assertSee('watch\/record', false);
        $auth->assertSee('X-CSRF-TOKEN', false);
        $auth->assertDontSee('evonime_watch_history', false);
    }

    public function test_home_shows_lanjut_tonton_section_for_logged_in_user(): void
    {
        $anime = $this->makeAnimeWithEpisodes(count: 5);
        $user = User::factory()->create();

        // Guest: section tersembunyi.
        $guest = $this->get('/');
        $guest->assertStatus(200);
        $guest->assertDontSee('id="continue-watching"', false);

        // Tonton episode 3 lalu 5 (yang terakhir harus menang).
        foreach ([3, 5] as $number) {
            $this->actingAs($user, 'sanctum')
                ->postJson('/api/v1/watch/record', ['episode_id' => $this->episodeOf($anime, $number)->id])
                ->assertStatus(200);
        }

        $home = $this->actingAs($user)->get('/');
        $home->assertStatus(200);
        $home->assertSee('LANJUT TONTON', false);
        $home->assertSee('id="continue-watching"', false);
        $home->assertSee('/watch/boruto/5', false);
        // 1 kartu per anime → episode 3 tidak muncul sebagai kartu terpisah.
        $home->assertDontSee('/watch/boruto/3', false);
    }

    public function test_browser_session_records_via_web_route(): void
    {
        $anime = $this->makeAnimeWithEpisodes(count: 5);
        $ep = $this->episodeOf($anime, 5);

        $user = User::factory()->create([
            'email' => 'sess@evonime.test',
            'password' => bcrypt('password123'),
        ]);

        // Login lewat route web (session cookie), bukan sanctum token.
        $this->post('/login', [
            'email' => 'sess@evonime.test',
            'password' => 'password123',
        ])->assertRedirect('/');

        $this->postJson('/watch/record', ['episode_id' => $ep->id])
            ->assertStatus(200)
            ->assertJsonPath('episode_id', $ep->id);

        $this->assertDatabaseHas('watch_histories', [
            'user_id' => $user->id,
            'episode_id' => $ep->id,
        ]);
    }

    public function test_api_record_accepts_bearer_token(): void
    {
        $anime = $this->makeAnimeWithEpisodes(count: 5);
        $ep = $this->episodeOf($anime, 5);
        $user = User::factory()->create();

        $token = $user->createToken('api-test')->plainTextToken;

        $this->postJson('/api/v1/watch/record', ['episode_id' => $ep->id], [
            'Authorization' => 'Bearer ' . $token,
        ])
            ->assertStatus(200)
            ->assertJsonPath('data.episode_id', $ep->id);

        $this->assertDatabaseHas('watch_histories', [
            'user_id' => $user->id,
            'episode_id' => $ep->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_history(): void
    {
        $anime = $this->makeAnimeWithEpisodes();
        $ep = $this->episodeOf($anime, 1);

        $owner = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($owner, 'sanctum')
            ->postJson('/api/v1/watch/record', ['episode_id' => $ep->id])
            ->assertStatus(200);

        $this->actingAs($other, 'sanctum')
            ->deleteJson('/api/v1/watch/history/' . $ep->id)
            ->assertStatus(404);

        $this->assertDatabaseHas('watch_histories', ['user_id' => $owner->id, 'episode_id' => $ep->id]);
    }

    public function test_history_page_shows_real_history_and_requires_login(): void
    {
        $anime = $this->makeAnimeWithEpisodes(count: 5);
        $user = User::factory()->create();

        // Guest diarahkan ke halaman login.
        $this->get('/history')->assertRedirect('/login');

        // Belum ada riwayat → empty state.
        $this->actingAs($user)->get('/history')
            ->assertStatus(200)
            ->assertSee('BELUM ADA RIWAYAT TONTONAN', false);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/watch/record', ['episode_id' => $this->episodeOf($anime, 5)->id])
            ->assertStatus(200);

        $page = $this->actingAs($user)->get('/history');
        $page->assertStatus(200);
        $page->assertSee('WATCH HISTORY CHAPTERS', false);
        $page->assertSee('Boruto', false);
        $page->assertSee('Episode 5', false);
        $page->assertSee('/watch/boruto/5', false);
        $page->assertSee('TANDAI SELESAI', false);
        $page->assertSee('HAPUS', false);
    }

    public function test_history_complete_and_delete_actions(): void
    {
        $anime = $this->makeAnimeWithEpisodes(count: 5);
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/watch/record', ['episode_id' => $this->episodeOf($anime, 5)->id]);

        $row = WatchHistory::where('user_id', $user->id)->firstOrFail();

        $this->actingAs($user)->patch('/history/' . $row->id . '/complete')->assertRedirect();
        $this->assertTrue($row->fresh()->completed);

        $this->actingAs($user)->delete('/history/' . $row->id)->assertRedirect();
        $this->assertDatabaseCount('watch_histories', 0);
    }

    public function test_history_actions_are_scoped_to_owner_and_clear_all_works(): void
    {
        $anime = $this->makeAnimeWithEpisodes(count: 5);
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($owner, 'sanctum')
            ->postJson('/api/v1/watch/record', ['episode_id' => $this->episodeOf($anime, 5)->id]);

        $row = WatchHistory::where('user_id', $owner->id)->firstOrFail();

        // User lain tidak boleh menandai selesai / menghapus riwayat orang lain.
        $this->actingAs($other)->patch('/history/' . $row->id . '/complete')->assertRedirect();
        $this->actingAs($other)->delete('/history/' . $row->id)->assertRedirect();

        $this->assertFalse($row->fresh()->completed);
        $this->assertDatabaseHas('watch_histories', ['id' => $row->id]);

        // Pemilik bisa membersihkan semua riwayatnya.
        $this->actingAs($owner)->delete('/history')->assertRedirect();
        $this->assertDatabaseCount('watch_histories', 0);
    }
}
