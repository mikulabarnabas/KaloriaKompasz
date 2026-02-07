<?php

namespace App\Observers;

use App\Models\Foods;
use App\Enums\FoodUnits;

class FoodsObserver
{
    public function creating(Foods $food): void
    {
        $this->normalize($food);
    }

    public function updating(Foods $food): void
    {
        $this->normalize($food);
    }

    private function normalize(Foods $food): void
    {
        if (!$food->unit || !$food->amount) {
            return;
        }

        $unit = $food->unit instanceof FoodUnits
            ? $food->unit
            : FoodUnits::from($food->unit);

        $baseAmount = $food->amount * $unit->toBaseFactor();

        if ($baseAmount <= 0) {
            return;
        }

        $factor = 100 / $baseAmount;

        $food->calorie = round($food->calorie * $factor, 2);
        $food->fat = round($food->fat * $factor, 2);
        $food->carb = round($food->carb * $factor, 2);
        $food->protein = round($food->protein * $factor, 2);

        $food->amount = 100;
        $food->unit = $unit->baseUnit();

        unset($food->amount);
    }
}


