<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubtitleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'language' => $this->language instanceof \BackedEnum ? $this->language->value : $this->language,
            'label' => $this->label,
            'format' => $this->format,
            'url' => $this->url,
            'is_default' => (bool) $this->is_default,
        ];
    }
}
