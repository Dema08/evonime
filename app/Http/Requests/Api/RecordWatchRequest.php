<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Catat tontonan level-episode (Continue Watching).
 * Hanya butuh episode_id karena player iframe cross-origin
 * tidak bisa dibaca currentTime/duration-nya.
 */
class RecordWatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'episode_id' => ['required', 'integer', 'exists:episodes,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'episode_id.required' => 'ID episode wajib diisi.',
            'episode_id.integer' => 'ID episode harus berupa bilangan bulat.',
            'episode_id.exists' => 'Episode tidak ditemukan di sistem.',
        ];
    }
}
