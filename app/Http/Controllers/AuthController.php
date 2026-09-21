<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $clientId = trim((string) config('services.google.client_id'));
        $clientSecret = trim((string) config('services.google.client_secret'));
        $redirectUri = trim((string) config('services.google.redirect'));

        // Cek apakah kredensial Google OAuth telah diisi
        if (empty($clientId) || empty($clientSecret) || empty($redirectUri)) {
            Log::warning('Google OAuth credentials missing: client_id or client_secret is empty.');

            return redirect()->route('login')->withErrors([
                'email' => 'Konfigurasi Google OAuth belum lengkap. Silakan isi GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET di file .env terlebih dahulu.',
            ]);
        }

        // Mencegah host mismatch antara domain navigasi user dan domain callback Google
        // Contoh: User membuka http://127.0.0.1:8000 sementara GOOGLE_REDIRECT_URI adalah http://localhost:8000/...
        // Karena browser memisahkan cookie antara 127.0.0.1 dan localhost, perbedaan host akan
        // menyebabkan session state hilang saat kembali dari Google (InvalidStateException).
        $configuredHost = parse_url($redirectUri, PHP_URL_HOST);
        $configuredPort = parse_url($redirectUri, PHP_URL_PORT);
        $currentHost = $request->getHost();

        if ($configuredHost && $currentHost !== $configuredHost) {
            $configuredScheme = parse_url($redirectUri, PHP_URL_SCHEME) ?: $request->getScheme();
            $portSuffix = $configuredPort ? ':'.$configuredPort : ($request->getPort() && ! in_array($request->getPort(), [80, 443]) ? ':'.$request->getPort() : '');
            $alignedUrl = "{$configuredScheme}://{$configuredHost}{$portSuffix}/auth/google";

            return redirect()->away($alignedUrl);
        }

        try {
            return Socialite::driver('google')
                ->with(['prompt' => 'select_account'])
                ->redirect();
        } catch (\Throwable $e) {
            Log::error('Google OAuth redirect error: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return redirect()->route('login')->withErrors([
                'email' => 'Konfigurasi Google OAuth belum lengkap atau terjadi kesalahan koneksi.',
            ]);
        }
    }

    /**
     * Obtain the user information from Google OAuth callback.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        // 1. Cek apakah user membatalkan otorisasi Google
        if ($request->has('error') || $request->has('error_code')) {
            $errorDescription = $request->input('error_description', 'Login dengan Google dibatalkan.');
            Log::warning('Google OAuth cancelled or returned error: '.$errorDescription);

            return redirect()->route('login')->withErrors([
                'email' => 'Login dengan Google dibatalkan. Silakan coba lagi.',
            ]);
        }

        // 2. Ambil data user dari Google via Socialite
        $clientId = trim((string) config('services.google.client_id'));
        $clientSecret = trim((string) config('services.google.client_secret'));

        if (empty($clientId) || empty($clientSecret)) {
            Log::error('Google OAuth callback attempted but client_id or client_secret is missing.');

            return redirect()->route('login')->withErrors([
                'email' => 'Konfigurasi Google OAuth belum lengkap di file .env.',
            ]);
        }

        try {
            // Alur utama: ambil data user secara stateful (standar session)
            $googleUser = Socialite::driver('google')->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            Log::warning('Google OAuth session state mismatch (InvalidStateException). Memverifikasi via fallback.', [
                'host' => $request->getHost(),
                'session_id' => $request->session()->getId(),
                'session_has_state' => $request->session()->has('state'),
                'request_has_state' => $request->filled('state'),
                'request_has_code' => $request->filled('code'),
            ]);

            // Jika state session hilang (misal akibat cookie SameSite pada cross-site redirect atau host mismatch),
            // coba verifikasi kode otorisasi via stateless() agar autentikasi tidak gagal bagi user
            try {
                $googleUser = Socialite::driver('google')->stateless()->user();
            } catch (\Throwable $fallbackException) {
                Log::error('Google OAuth callback gagal pada stateful dan stateless: '.$fallbackException->getMessage(), [
                    'exception' => $fallbackException,
                ]);

                return redirect()->route('login')->withErrors([
                    'email' => 'Sesi Google OAuth telah kedaluwarsa atau terjadi ketidakcocokan domain. Silakan coba lagi.',
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Google OAuth callback error: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return redirect()->route('login')->withErrors([
                'email' => 'Login dengan Google gagal. Silakan coba lagi.',
            ]);
        }

        // 3. Validasi keberadaan email dari data Google
        $email = $googleUser->getEmail();
        if (empty($email)) {
            Log::warning('Google OAuth returned no email address for ID: '.$googleUser->getId());

            return redirect()->route('login')->withErrors([
                'email' => 'Alamat email tidak ditemukan dari akun Google Anda. Pastikan izin email telah disetujui.',
            ]);
        }

        $googleId = (string) $googleUser->getId();
        $name = $googleUser->getName() ?: ($googleUser->getNickname() ?: 'Pengguna Google');
        $avatar = $googleUser->getAvatar();

        // 4. Validasi jika Google ID ini sudah terhubung dengan email lain (edge case)
        $userWithGoogleId = User::where('google_id', $googleId)->first();
        if ($userWithGoogleId && $userWithGoogleId->email !== $email) {
            Log::warning("Google ID {$googleId} already tied to email {$userWithGoogleId->email}, conflict with {$email}");

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Google ini sudah terhubung dengan akun lain di sistem kami.',
            ]);
        }

        // 5. Cari user berdasarkan Email untuk mencegah duplicate account
        $user = User::where('email', $email)->first();

        if ($user) {
            // Cek apakah status user aktif
            if (! $user->is_active) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Akun Anda dinonaktifkan. Silakan hubungi admin.',
                ]);
            }

            // Tautkan google_id jika sebelumnya belum ditautkan
            if (empty($user->google_id)) {
                $user->google_id = $googleId;
            }

            // Tautkan avatar dari Google jika user belum memiliki custom avatar
            if (empty($user->avatar) && ! empty($avatar)) {
                $user->avatar = $avatar;
            }

            // Tandai email sudah terverifikasi jika belum terverifikasi
            if (empty($user->email_verified_at)) {
                $user->email_verified_at = now();
            }

            // PENTING: Password lama TIDAK ditimpa dan TIDAK dihapus
            $user->save();
        } else {
            // 6. Jika user belum ada: Buat user baru
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'google_id' => $googleId,
                'avatar' => $avatar,
                // Mekanisme password aman acak berkekuatan tinggi agar constraint database terpenuhi
                'password' => Hash::make(Str::random(32)),
                'role' => 'user',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        // 7. Login ke sistem
        Auth::login($user, remember: true);
        $request->session()->regenerate();

        // 8. Redirect ke halaman setelah login yang sesuai
        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        return redirect()->intended('/');
    }
}
