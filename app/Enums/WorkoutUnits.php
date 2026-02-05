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
            self::MINUTES => 1,       // base unit is minutes
            self::SECONDS => 1 / 60,  // seconds to minutes
            self::M => 1,             // base unit is meters
            self::KM => 1000,          // km to meters
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
