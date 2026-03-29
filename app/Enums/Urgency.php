<?php

namespace App\Enums;

enum Urgency: string
{
    case EMERGENCY = 'emergency';
    case PRE_BOOKED = 'pre_booked';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
