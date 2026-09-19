<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class TrackProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'episode_id' => ['required', 'integer', 'exists:episodes,id'],
            'progress_seconds' => ['required', 'integer', 'min:0'],
            'duration_seconds' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'episode_id.required' => 'ID episode wajib diisi.',
            'episode_id.exists' => 'Episode tidak ditemukan di sistem.',
            'progress_seconds.required' => 'Progres tontonan (detik) wajib diisi.',
            'progress_seconds.integer' => 'Progres tontonan harus berupa bilangan bulat.',
            'progress_seconds.min' => 'Progres tontonan tidak boleh negatif.',
            'duration_seconds.required' => 'Durasi total (detik) wajib diisi.',
            'duration_seconds.integer' => 'Durasi total harus berupa bilangan bulat.',
            'duration_seconds.min' => 'Durasi total minimal :min detik.',
        ];
    }
}
