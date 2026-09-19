<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStreamSourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }

    public function rules(): array
    {
        return [
            'episode_id' => ['required', 'exists:episodes,id'],
            'server_name' => ['required', 'string', 'max:50'],
            'quality' => [
                'required',
                'in:360p,480p,720p,1080p',
                Rule::unique('stream_sources')->where(function ($query) {
                    return $query->where('episode_id', $this->input('episode_id'))
                        ->where('server_name', $this->input('server_name'))
                        ->where('quality', $this->input('quality'));
                }),
            ],
            'url' => ['required', 'string'],
            'format' => ['required', 'in:hls,mp4,dash'],
            'is_active' => ['boolean'],
            'priority' => ['integer', 'min:0', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'quality.unique' => 'Kombinasi episode, server, dan kualitas ini sudah ada.',
        ];
    }
}
