<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class WorkoutDiaryPivot extends Pivot
{
    protected $table = 'exercises_to_workout_diary';

    protected $fillable = [
        'exercise_id',
        'workout_diary_id',
        'amount',
        'unit',
        'burned_calories'
    ];
}
