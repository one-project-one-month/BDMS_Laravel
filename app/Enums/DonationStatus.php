<?php

namespace App\Enums;

enum DonationStatus: string
{
    case PENDING = 'pending';
    case SCREENING = 'screening';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
