<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'laravel',
            'services.google.client_id' => '1234567890-test.apps.googleusercontent.com',
            'services.google.client_secret' => 'GOCSPX-mocksecret123',
            'services.google.redirect' => 'http://localhost:8000/auth/google/callback',
        ]);
        \Illuminate\Support\Facades\DB::purge('mysql');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_login_page_renders_continue_with_google_button(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Continue with Google');
        $response->assertSee(route('auth.google'));
        $response->assertSee('atau');
    }

    public function test_register_page_renders_register_with_google_button(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Register with Google');
        $response->assertSee(route('auth.google'));
        $response->assertSee('atau');
    }

    public function test_auth_google_redirects_to_google(): void
    {
        config(['services.google.client_id' => 'test-client-id']);
        config(['services.google.client_secret' => 'test-client-secret']);
        config(['services.google.redirect' => 'http://localhost:8000/auth/google/callback']);

        $response = $this->get('/auth/google');

        $response->assertRedirect();
        $this->assertStringContainsString('accounts.google.com', $response->headers->get('Location'));
    }

    public function test_redirect_to_google_fails_safely_when_credentials_are_missing(): void
    {
        config(['services.google.client_id' => '']);
        config(['services.google.client_secret' => '']);

        $response = $this->get('/auth/google');

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
    }

    public function test_google_callback_handles_user_cancellation(): void
    {
        $response = $this->get('/auth/google/callback?error=access_denied');

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
    }

    public function test_google_callback_registers_new_user_when_email_not_registered(): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-unique-id-999');
        $socialiteUser->shouldReceive('getName')->andReturn('Zoro Roronoa');
        $socialiteUser->shouldReceive('getNickname')->andReturn('zoro');
        $socialiteUser->shouldReceive('getEmail')->andReturn('zoro@evonime.test');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar/zoro');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        // Pastikan user belum ada
        User::where('email', 'zoro@evonime.test')->delete();

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/');
        $this->assertAuthenticated();

        $user = User::where('email', 'zoro@evonime.test')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Zoro Roronoa', $user->name);
        $this->assertEquals('google-unique-id-999', $user->google_id);
        $this->assertEquals('https://lh3.googleusercontent.com/avatar/zoro', $user->avatar);
        $this->assertEquals('user', $user->role);
        $this->assertTrue((bool) $user->is_active);
        $this->assertNotNull($user->password); // Random secure hash
    }

    public function test_google_callback_logs_in_existing_user_without_overwriting_password(): void
    {
        $originalPassword = 'ExistingSecurePassword123!';
        $existingUser = User::updateOrCreate(
            ['email' => 'luffy@evonime.test'],
            [
                'name' => 'Monkey D. Luffy',
                'password' => Hash::make($originalPassword),
                'role' => 'user',
                'is_active' => true,
                'google_id' => null,
            ]
        );

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-unique-id-777');
        $socialiteUser->shouldReceive('getName')->andReturn('Monkey D. Luffy Google');
        $socialiteUser->shouldReceive('getNickname')->andReturn('luffy');
        $socialiteUser->shouldReceive('getEmail')->andReturn('luffy@evonime.test');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar/luffy');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($existingUser);

        // Refresh user dari database
        $refreshedUser = User::where('email', 'luffy@evonime.test')->first();
        // Google ID harus tertaut
        $this->assertEquals('google-unique-id-777', $refreshedUser->google_id);
        // Password lama HARUS tetap valid (tidak tertimpa atau terhapus)
        $this->assertTrue(Hash::check($originalPassword, $refreshedUser->password));
        // Tidak ada duplicate user
        $this->assertEquals(1, User::where('email', 'luffy@evonime.test')->count());
    }

    public function test_regular_email_password_login_still_works(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'sanji@evonime.test'],
            [
                'name' => 'Sanji',
                'password' => Hash::make('Cook12345'),
                'role' => 'user',
                'is_active' => true,
            ]
        );

        $response = $this->post('/login', [
            'email' => 'sanji@evonime.test',
            'password' => 'Cook12345',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_manual_register_creates_user_and_logs_in(): void
    {
        User::where('email', 'nami@evonime.test')->delete();

        $response = $this->post('/register', [
            'name' => 'Nami',
            'email' => 'nami@evonime.test',
            'password' => 'Navigator123!',
            'password_confirmation' => 'Navigator123!',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();

        $user = User::where('email', 'nami@evonime.test')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Nami', $user->name);
        $this->assertTrue(Hash::check('Navigator123!', $user->password));
    }

    public function test_google_callback_handles_oauth_exception_gracefully(): void
    {
        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andThrow(new \Exception('Connection timeout to Google'));

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
    }

    public function test_google_callback_handles_missing_email_gracefully(): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-id-no-email');
        $socialiteUser->shouldReceive('getName')->andReturn('No Email User');
        $socialiteUser->shouldReceive('getNickname')->andReturn('noemail');
        $socialiteUser->shouldReceive('getEmail')->andReturn(null);
        $socialiteUser->shouldReceive('getAvatar')->andReturn(null);

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
    }

    public function test_redirect_to_google_aligns_mismatched_host(): void
    {
        // Simulasi request datang dari 127.0.0.1 sementara config redirect adalah localhost
        $response = $this->get('http://127.0.0.1:8000/auth/google');

        $response->assertRedirect();
        $this->assertStringContainsString('localhost:8000/auth/google', $response->headers->get('Location'));
    }

    public function test_google_callback_recovers_from_invalid_state_exception(): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-id-recovered-123');
        $socialiteUser->shouldReceive('getName')->andReturn('Recovered User');
        $socialiteUser->shouldReceive('getNickname')->andReturn('recovered');
        $socialiteUser->shouldReceive('getEmail')->andReturn('recovered@evonime.test');
        $socialiteUser->shouldReceive('getAvatar')->andReturn(null);

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        // user() pertama melempar InvalidStateException
        $provider->shouldReceive('user')->once()->andThrow(new \Laravel\Socialite\Two\InvalidStateException('Invalid state'));
        // fallback stateless()->user() mengembalikan user berhasil
        $provider->shouldReceive('stateless')->once()->andReturnSelf();
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        User::where('email', 'recovered@evonime.test')->delete();

        $response = $this->get('/auth/google/callback?state=xyz&code=abc');

        $response->assertRedirect('/');
        $this->assertAuthenticated();

        $user = User::where('email', 'recovered@evonime.test')->first();
        $this->assertNotNull($user);
        $this->assertEquals('recovered@evonime.test', $user->email);
    }
}

