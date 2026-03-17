<?php

namespace App\Enums;

enum InfectionResult: string
{
    case POSITIVE = 'positive';
    case NEGATIVE = 'negative';
    case INCONCLUSIVE = 'inconclusive';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
