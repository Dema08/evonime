<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateGenreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('name') && ! $this->filled('slug')) {
            $this->merge(['slug' => Str::slug($this->input('name'))]);
        }
    }

    public function rules(): array
    {
        $genreId = $this->route('genre')?->id ?? $this->route('genre');

        return [
            'name' => ['required', 'string', 'max:100', 'unique:genres,name,'.$genreId],
            'slug' => ['nullable', 'string', 'max:120', 'unique:genres,slug,'.$genreId, 'regex:/^[a-z0-9-]+$/'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama genre wajib diisi.',
            'name.unique' => 'Nama genre sudah ada.',
            'slug.unique' => 'Slug genre sudah ada.',
        ];
    }
}
