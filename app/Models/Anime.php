<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Anime extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'title_alternative', 'slug', 'synopsis', 'type', 'status',
        'release_date', 'end_date', 'rating', 'total_episodes', 'duration',
        'studio', 'season', 'year', 'poster_path', 'banner_path',
        'is_published', 'is_featured', 'views_count',
    ];

    protected function casts(): array
    {
        return [
            'release_date' => 'date',
            'end_date' => 'date',
            'rating' => 'decimal:1',
            'total_episodes' => 'integer',
            'duration' => 'integer',
            'year' => 'integer',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'views_count' => 'integer',
        ];
    }

    // ---- Relasi ----
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'anime_genre');
    }

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class)->orderBy('episode_number');
    }

    // ---- Scope ----
    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true);
    }

    public function scopeFeatured(Builder $q): Builder
    {
        return $q->where('is_featured', true);
    }

    public function scopeByGenre(Builder $q, string $genreSlug): Builder
    {
        return $q->whereHas('genres', fn (Builder $g) => $g->where('slug', $genreSlug));
    }

    public function scopeSearch(Builder $q, string $keyword): Builder
    {
        // SQLite fallback atau keyword pendek (<4 char).
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite' || mb_strlen($keyword) < 4) {
            return $q->where(function (Builder $sub) use ($keyword) {
                $sub->where('title', 'like', "%{$keyword}%")
                    ->orWhere('title_alternative', 'like', "%{$keyword}%")
                    ->orWhere('synopsis', 'like', "%{$keyword}%");
            });
        }

        return $q->whereFullText(['title', 'title_alternative', 'synopsis'], $keyword);
    }

    public function scopeLatest(Builder $q): Builder
    {
        return $q->orderByDesc('created_at');
    }

    // ---- Accessor ----
    public function getPosterUrlAttribute(): ?string
    {
        $path = $this->poster_path;
        if (empty($path)) return null;
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }
        return Storage::disk('public')->url($path);
    }

    public function getBannerUrlAttribute(): ?string
    {
        $path = $this->banner_path;
        if (empty($path)) return null;
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }
        return Storage::disk('public')->url($path);
    }

    // ---- Helper ----
    public function getLatestEpisodes(int $limit = 5)
    {
        return $this->episodes()->where('status', 'ready')->latest('aired_at')->limit($limit)->get();
    }

    public function getTotalViews(): int
    {
        return (int) $this->views_count + (int) $this->episodes()->sum('views_count');
    }
}
