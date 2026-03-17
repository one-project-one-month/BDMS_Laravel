<?php

namespace App\Enums;

enum ScreeningStatus: string
{
    case PENDING = 'pending';
    case FAILED = 'failed';
    case PASSED = 'passed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
