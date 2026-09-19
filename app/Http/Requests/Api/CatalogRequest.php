<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CatalogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', 'string', 'in:ongoing,completed,upcoming,hiatus'],
            'type' => ['nullable', 'string', 'in:tv,movie,ova,ona,special'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2099'],
            'genre' => ['nullable', 'string'],
            'sort' => ['nullable', 'string', 'in:latest,popular,rating,title'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Status anime tidak valid. Pilih antara ongoing, completed, upcoming, atau hiatus.',
            'type.in' => 'Format tayangan tidak valid. Pilih antara tv, movie, ova, ona, atau special.',
            'year.integer' => 'Tahun rilis harus berupa angka tahun yang valid.',
            'year.min' => 'Tahun rilis minimal tahun :min.',
            'year.max' => 'Tahun rilis maksimal tahun :max.',
            'sort.in' => 'Pilihan pengurutan tidak valid.',
            'page.integer' => 'Parameter page harus berupa bilangan bulat.',
            'page.min' => 'Parameter page minimal :min.',
            'per_page.integer' => 'Parameter per_page harus berupa bilangan bulat.',
            'per_page.min' => 'Parameter per_page minimal :min.',
            'per_page.max' => 'Parameter per_page maksimal :max.',
        ];
    }
}
