<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubtitleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_default' => $this->boolean('is_default')]);
    }

    public function rules(): array
    {
        return [
            'episode_id' => ['required', 'exists:episodes,id'],
            'language' => [
                'required',
                'in:id,en,jp',
                Rule::unique('subtitles')->where(fn ($q) => $q->where('episode_id', $this->input('episode_id'))),
            ],
            'label' => ['required', 'string', 'max:50'],
            'format' => ['required', 'in:vtt,srt,ass'],
            'file' => ['nullable', 'file', 'mimes:vtt,srt,ass', 'max:1024'],
            'url' => ['required_without:file', 'nullable', 'string'],
            'is_default' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'language.unique' => 'Subtitle dengan bahasa ini sudah ada untuk episode tersebut.',
            'url.required_without' => 'URL wajib diisi jika file subtitle tidak di-upload.',
            'file.max' => 'Ukuran file subtitle maksimal 1MB.',
        ];
    }
}
