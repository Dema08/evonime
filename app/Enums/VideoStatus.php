<?php

namespace App\Enums;

enum VideoStatus: string
{
    case Draft = 'draft';
    case Processing = 'processing';
    case Ready = 'ready';
    case Failed = 'failed';
    case Hidden = 'hidden';

    // Additional statuses for anime sync
    case Ongoing = 'ongoing';
    case Completed = 'completed';
    case Dropped = 'dropped';

    /**
     * Konversi status dari Otakudesu ke status anime yang valid di DB.
     * Kolom animes.status adalah ENUM: ongoing, completed, upcoming, hiatus, dropped.
     * Otakudesu "Drop" (capitalized, tanpa -ped) harus jadi "dropped".
     */
    public static function mapAnimeStatus(?string $otakudesuStatus): string
    {
        if (empty($otakudesuStatus)) return 'ongoing';

        return match (strtolower(trim($otakudesuStatus))) {
            'ongoing' => 'ongoing',
            'completed', 'complete', 'finished', 'tamat' => 'completed',
            'drop', 'dropped' => 'dropped',
            'upcoming', 'coming soon' => 'upcoming',
            'hiatus' => 'hiatus',
            default => 'ongoing',
        };
    }

    /**
     * Konversi status dari Otakudesu ke VideoStatus (pipeline episode).
     */
    public static function fromOtakudesu(?string $status): self
    {
        return match (strtolower(trim((string) $status))) {
            'ongoing'  => self::Ongoing,
            'completed', 'complete', 'finished' => self::Completed,
            'drop', 'dropped'     => self::Dropped,
            'upcoming' => self::Ongoing,
            default    => self::Ready,
        };
    }
}