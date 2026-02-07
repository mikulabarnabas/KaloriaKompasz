<?php

namespace App\Enums;

enum WorkoutUnits: string
{
    case MINUTES = 'minutes';
    case SECONDS = 'seconds';
    case M = 'm';
    case KM = 'km';

    public function toBaseFactor(): int
    {
        return match($this) {
            self::MINUTES => 1,
            self::SECONDS => 1 / 60,
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
