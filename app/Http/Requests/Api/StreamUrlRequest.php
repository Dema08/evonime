<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StreamUrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'episode_id' => ['required', 'integer', 'exists:episodes,id'],
            'quality' => ['nullable', 'string', 'in:360p,480p,720p,1080p'],
        ];
    }

    public function messages(): array
    {
        return [
            'episode_id.required' => 'ID episode wajib diisi.',
            'episode_id.exists' => 'Episode tidak ditemukan di sistem.',
            'quality.in' => 'Kualitas video tidak valid. Pilih 360p, 480p, 720p, atau 1080p.',
        ];
    }
}
