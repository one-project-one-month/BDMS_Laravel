<?php

namespace App\Enums;

enum BloodRequestStatus: string
{
    case PENDING = 'pending';
    case CANCELLED = 'cancelled';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case FULFILLED = 'fulfilled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
