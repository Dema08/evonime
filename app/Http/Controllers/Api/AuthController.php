<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Register a new user account.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => 'user',
            'is_active' => true,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return ApiResponse::success([
            'user' => UserResource::make($user),
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => null,
        ], 'Registrasi berhasil.', Response::HTTP_CREATED);
    }

    /**
     * Login user and issue Sanctum token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->input('email'))->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return ApiResponse::error('Kredensial yang diberikan tidak cocok dengan catatan kami.', Response::HTTP_UNAUTHORIZED);
        }

        if (! $user->is_active) {
            return ApiResponse::error('Akun Anda dinonaktifkan. Hubungi admin.', Response::HTTP_FORBIDDEN);
        }

        $deviceName = $request->input('device_name', 'frontend-web');
        $token = $user->createToken($deviceName)->plainTextToken;

        return ApiResponse::success([
            'user' => UserResource::make($user),
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => null,
        ], 'Login berhasil.');
    }

    /**
     * Logout user and revoke current Sanctum token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return ApiResponse::success(null, 'Logout berhasil.');
    }

    /**
     * Get currently authenticated user profile.
     */
    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success(UserResource::make($request->user()), 'Profil pengguna berhasil dimuat.');
    }
}
