<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProfileMenuTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'laravel',
        ]);
        \Illuminate\Support\Facades\DB::purge('mysql');
    }

    protected function tearDown(): void
    {
        User::whereIn('email', [
            'naruto@konoha.test',
            'kakashi@konoha.test',
            'goku@capsule.test',
            'testlogout@evonime.test',
        ])->delete();
        parent::tearDown();
    }

    public function test_unauthenticated_user_sees_unauthenticated_profile_info(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Navbar trigger
        $response->assertSee('AKUN');
        // Unauthenticated message
        $response->assertSee('Anda belum login');
        $response->assertSee('Silakan login untuk mengakses akun Anda.');
        // Login button pointing to route('login')
        $response->assertSee(route('login'));
        // Does not show logout form
        $response->assertDontSee('Logout');
    }

    public function test_guest_cannot_access_profile_page_directly(): void
    {
        $response = $this->get('/profile');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_sees_dynamic_profile_info_and_logout(): void
    {
        $user = User::factory()->create([
            'name' => 'Naruto Uzumaki',
            'email' => 'naruto@konoha.test',
            'role' => 'user',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        // Shows user name and email dynamically
        $response->assertSee('Naruto Uzumaki');
        $response->assertSee('naruto@konoha.test');
        // Role & status
        $response->assertSee('MEMBER');
        $response->assertSee('Akun Aktif');
        // Profil Saya link
        $response->assertSee(route('profile'));
        // Logout button and form
        $response->assertSee(route('logout'));
        // Must NOT see unauthenticated prompt
        $response->assertDontSee('Anda belum login');
    }

    public function test_authenticated_admin_sees_admin_badge_and_dashboard_shortcut(): void
    {
        $admin = User::factory()->create([
            'name' => 'Kakashi Hatake',
            'email' => 'kakashi@konoha.test',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Kakashi Hatake');
        $response->assertSee('kakashi@konoha.test');
        $response->assertSee('ADMIN');
        $response->assertSee(route('admin.dashboard'));
        $response->assertSee(route('logout'));
    }

    public function test_authenticated_google_user_shows_google_avatar_and_profile_page(): void
    {
        $googleUser = User::factory()->create([
            'name' => 'Goku Son',
            'email' => 'goku@capsule.test',
            'google_id' => 'google-oauth-uid-12345',
            'avatar' => 'https://lh3.googleusercontent.com/avatar/goku',
            'role' => 'user',
            'is_active' => true,
        ]);

        $response = $this->actingAs($googleUser)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Goku Son');
        $response->assertSee('goku@capsule.test');
        $response->assertSee('https://lh3.googleusercontent.com/avatar/goku');

        // Test accessing /profile page
        $profileResponse = $this->actingAs($googleUser)->get('/profile');
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee('PROFIL SAYA');
        $profileResponse->assertSee('Goku Son');
        $profileResponse->assertSee('goku@capsule.test');
        $profileResponse->assertSee('Google OAuth');
        $profileResponse->assertSee(route('logout'));
    }

    public function test_user_can_logout_via_post(): void
    {
        $user = User::factory()->create([
            'email' => 'testlogout@evonime.test',
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
