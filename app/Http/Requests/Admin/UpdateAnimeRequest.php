<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateAnimeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('title') && ! $this->filled('slug')) {
            $this->merge(['slug' => Str::slug($this->input('title'))]);
        }
        $this->merge([
            'is_published' => $this->boolean('is_published'),
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }

    public function rules(): array
    {
        $animeId = $this->route('anime')?->id ?? $this->route('anime');

        return [
            'title' => ['required', 'string', 'max:255'],
            'title_alternative' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:animes,slug,'.$animeId, 'regex:/^[a-z0-9-]+$/'],
            'synopsis' => ['required', 'string'],
            'type' => ['required', 'in:tv,movie,ova,ona,special'],
            'status' => ['required', 'in:ongoing,completed,upcoming,hiatus'],
            'release_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:release_date'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'total_episodes' => ['nullable', 'integer', 'min:1'],
            'duration' => ['nullable', 'integer', 'min:1'],
            'studio' => ['nullable', 'string', 'max:150'],
            'season' => ['nullable', 'in:winter,spring,summer,fall'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'poster' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_published' => ['boolean'],
            'is_featured' => ['boolean'],
            'genres' => ['required', 'array', 'min:1'],
            'genres.*' => ['exists:genres,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul anime wajib diisi.',
            'slug.unique' => 'Slug anime sudah digunakan.',
            'slug.regex' => 'Slug hanya boleh berisi huruf kecil, angka, dan tanda hubung (-).',
            'genres.required' => 'Pilih minimal 1 genre.',
        ];
    }
}
