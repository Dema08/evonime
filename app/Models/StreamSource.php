<?php

namespace App\Models;

use App\Enums\VideoQuality;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StreamSource extends Model
{
    protected $fillable = [
        'episode_id', 'server_name', 'quality', 'url', 'format', 'is_active', 'priority',
    ];

    protected function casts(): array
    {
        return [
            'quality' => VideoQuality::class,
            'is_active' => 'boolean',
            'priority' => 'integer',
        ];
    }

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
