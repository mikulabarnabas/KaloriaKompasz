<?php

namespace App\Enums;

enum WorkoutUnits: string
{
    case MINUTES = 'minutes';
    case HOURS = 'hours';
    case M = 'm';
    case KM = 'km';

    public function toBaseFactor(): float
    {
        return match($this) {
            self::MINUTES => 1,
            self::HOURS => 60,
            self::M => 1,
            self::KM => 1000,
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function baseValues(): array
    {
        return ['minutes', 'm'];
    }
}
