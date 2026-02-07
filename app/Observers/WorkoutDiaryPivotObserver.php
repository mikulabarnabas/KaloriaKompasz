<?php

namespace App\Observers;

use App\Models\WorkoutDiaryPivot;
use App\Models\Exercises;
use App\Enums\WorkoutUnits;

class WorkoutDiaryPivotObserver
{
    public function creating(WorkoutDiaryPivot $entry): void
    {
        $this->fillBurnedCalories($entry);
    }

    public function updating(WorkoutDiaryPivot $entry): void
    {
        $this->fillBurnedCalories($entry);
    }

    private function fillBurnedCalories(WorkoutDiaryPivot $entry): void
    {
        $exercise = Exercises::find($entry->exercise_id);

        $unit = WorkoutUnits::from($entry->unit);
        $amountInBase = $entry->amount * $unit->toBaseFactor();

        $entry->burned_calories = round($amountInBase * $exercise->calories_per_unit, 2);
    }
}
