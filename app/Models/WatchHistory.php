<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchHistory extends Model
{
    protected $fillable = [
        'user_id', 'episode_id', 'progress_seconds', 'duration_seconds',
        'completed', 'last_watched_at',
    ];

    protected function casts(): array
    {
        return [
            'progress_seconds' => 'integer',
            'duration_seconds' => 'integer',
            'completed' => 'boolean',
            'last_watched_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }

    /**
     * Anime dari episode yang ditonton.
     *
     * Tabel watch_histories tidak menyimpan anime_id (lihat migration
     * 2025_01_01_000008_create_watch_histories_table), jadi anime diturunkan
     * dari episode->anime agar tidak ada data denormalized.
     * Selalu eager load ['episode.anime'] agar tidak N+1.
     */
    protected function anime(): Attribute
    {
        return Attribute::get(fn (): ?Anime => $this->episode?->anime);
    }

    public function progressPercent(): int
    {
        if (! $this->duration_seconds) {
            return 0;
        }

        return (int) min(100, round($this->progress_seconds / $this->duration_seconds * 100));
    }
}
