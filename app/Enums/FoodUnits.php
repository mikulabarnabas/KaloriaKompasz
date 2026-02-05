<?php

namespace App\Enums;

enum FoodUnits: string
{
    case G = 'g';
    case DKG = 'dkg';
    case KG = 'kg';

    case ML = 'ml';
    case CL = 'cl';
    case DL = 'dl';
    case L = 'l';

    public function toBaseFactor(): int
    {
        return match($this) {
            self::G => 1,
            self::DKG => 10,
            self::KG => 1000,

            self::ML => 1,
            self::CL => 10,
            self::DL => 100,
            self::L => 1000,
        };
    }

    public function baseUnit(): string
    {
        return match($this) {
            self::G, self::DKG, self::KG => 'g',
            default => 'ml',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function baseValues(): array 
    {
        return ['g', 'ml'];
    }
}

