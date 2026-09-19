<?php

namespace App\Models;

use App\Enums\SubtitleLanguage;
use App\Enums\VideoQuality;
use App\Enums\VideoStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Episode extends Model
{
    protected $fillable = [
        'anime_id', 'episode_number', 'title', 'synopsis', 'duration',
        'thumbnail_path', 'aired_at', 'status', 'views_count',
    ];

    protected function casts(): array
    {
        return [
            'episode_number' => 'integer',
            'duration' => 'integer',
            'aired_at' => 'datetime',
            'status' => VideoStatus::class,
            'views_count' => 'integer',
        ];
    }

    // ---- Relasi ----
    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class);
    }

    public function streamSources(): HasMany
    {
        return $this->hasMany(StreamSource::class);
    }

    public function subtitles(): HasMany
    {
        return $this->hasMany(Subtitle::class);
    }

    public function watchHistories(): HasMany
    {
        return $this->hasMany(WatchHistory::class);
    }

    // ---- Accessor ----
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail_path ? Storage::disk('public')->url($this->thumbnail_path) : null;
    }

    // ---- Helper ----
    public function getActiveSources()
    {
        return $this->streamSources()
            ->where('is_active', true)
            ->orderByDesc('priority')
            ->orderBy('quality')
            ->get();
    }

    public function getDefaultSubtitle(): ?Subtitle
    {
        return $this->subtitles()->where('is_default', true)->first()
            ?? $this->subtitles()->where('language', SubtitleLanguage::Id)->first()
            ?? $this->subtitles()->first();
    }
}
