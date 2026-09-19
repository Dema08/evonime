<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['required', 'string', 'min:2', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'q.required' => 'Kata kunci pencarian wajib diisi.',
            'q.string' => 'Kata kunci pencarian harus berupa teks.',
            'q.min' => 'Kata kunci pencarian minimal :min karakter.',
            'q.max' => 'Kata kunci pencarian maksimal :max karakter.',
            'per_page.integer' => 'Parameter per_page harus berupa bilangan bulat.',
            'per_page.min' => 'Parameter per_page minimal :min.',
            'per_page.max' => 'Parameter per_page maksimal :max.',
            'page.integer' => 'Parameter page harus berupa bilangan bulat.',
            'page.min' => 'Parameter page minimal :min.',
        ];
    }
}
