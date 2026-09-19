<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentSource extends Model
{
    protected $fillable = [
        'provider_name', 'external_id', 'entity_type', 'payload',
        'fetched_at', 'status', 'error_message',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'fetched_at' => 'datetime',
        ];
    }
}
