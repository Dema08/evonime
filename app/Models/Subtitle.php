<?php

namespace App\Models;

use App\Enums\SubtitleLanguage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subtitle extends Model
{
    protected $fillable = [
        'episode_id', 'language', 'label', 'format', 'url', 'is_default',
    ];

    protected function casts(): array
    {
        return [
            'language' => SubtitleLanguage::class,
            'is_default' => 'boolean',
        ];
    }

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }
}
