<?php

namespace App\Enums;

enum BloodInventoryStatus: string
{
    case AVAILABLE = 'available';
    case USED = 'used';
    case EXPIRED = 'expired';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
