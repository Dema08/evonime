<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEpisodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        $episodeId = $this->route('episode')?->id ?? $this->route('episode');
        $animeId = $this->input('anime_id');

        return [
            'anime_id' => ['required', 'exists:animes,id'],
            'episode_number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('episodes')
                    ->where(fn ($query) => $query->where('anime_id', $animeId))
                    ->ignore($episodeId),
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'synopsis' => ['nullable', 'string'],
            'duration' => ['nullable', 'integer', 'min:1'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'aired_at' => ['nullable', 'date'],
            'status' => ['required', 'in:draft,processing,ready,failed,hidden'],
        ];
    }

    public function messages(): array
    {
        return [
            'episode_number.unique' => 'Nomor episode ini sudah ada untuk anime tersebut.',
        ];
    }
}
