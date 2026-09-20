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
     * Konversi status dari Otakudesu ke VideoStatus.
     */
    public static function fromOtakudesu(?string $status): self
    {
        return match (strtolower((string) $status)) {
            'ongoing'  => self::Ongoing,
            'completed' => self::Completed,
            'drop'     => self::Dropped,
            'finished' => self::Completed,
            default    => self::Ready,
        };
    }
}