<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdatePasswordRequest;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct(
        protected readonly FileUploadService $fileUploadService,
    ) {}

    /**
     * Get user profile details.
     */
    public function profile(Request $request): JsonResponse
    {
        return ApiResponse::success(UserResource::make($request->user()), 'Profil berhasil dimuat.');
    }

    /**
     * Update user profile information (name, avatar).
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        if ($request->has('name')) {
            $user->name = $validated['name'];
        }

        if ($request->hasFile('avatar')) {
            $avatarPath = $this->fileUploadService->uploadImage($request->file('avatar'), 'avatars', $user->avatar);
            $user->avatar = $avatarPath;
        }

        $user->save();

        return ApiResponse::success(UserResource::make($user), 'Profil berhasil diperbarui.');
    }

    /**
     * Update user account password.
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return ApiResponse::success(null, 'Password berhasil diperbarui.');
    }
}
