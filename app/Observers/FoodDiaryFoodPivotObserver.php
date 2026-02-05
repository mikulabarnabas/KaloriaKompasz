<?php

namespace App\Observers;

use App\Models\FoodDiaryFoodPivot;
use App\Models\Foods;
use App\Enums\Units;

class FoodDiaryFoodPivotObserver
{
    public function creating(FoodDiaryFoodPivot $pivot): void
    {
        $this->fillMacros($pivot);
    }

    public function updating(FoodDiaryFoodPivot $pivot): void
    {
        $this->fillMacros($pivot);
    }

    private function fillMacros(FoodDiaryFoodPivot $pivot): void
    {
        $food = Foods::find($pivot->food_id);

        $unit = Units::from($pivot->unit);
        $consumedBase = $pivot->amount * $unit->toBaseFactor();
        $factor = $consumedBase / 100;

        $pivot->fat = round($food->fat * $factor, 2);
        $pivot->carb = round($food->carb * $factor, 2);
        $pivot->protein = round($food->protein * $factor, 2);
        $pivot->calorie = round($food->calorie * $factor, 2);
    }
}


