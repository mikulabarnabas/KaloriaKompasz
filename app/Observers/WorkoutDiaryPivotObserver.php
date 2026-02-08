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

        $exerciseUnit = WorkoutUnits::from($exercise->unit)->toBaseFactor();
        $entrytUnit = WorkoutUnits::from($entry->unit)->toBaseFactor();
        $factor = $entrytUnit / $exerciseUnit;

        $entry->burned_calories = round($exercise->calories_per_unit * $factor * $entry->amount, 2);
    }
}
