<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutDiary extends Model
{
    protected $table = 'workout_diary';

    protected $fillable = [
        'user_id',
        'date',
        'workout_name',
        'burned_calories',
        'duration_minutes',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
    ];

    public function exercises()
    {
        return $this->belongsToMany(
            Exercises::class,
            'exercises_to_workout_diary',
            'workout_diary_id',
            'exercise_id'
        )
            ->withPivot(['id', 'unit', 'amount'])
            ->withTimestamps();
    }
}
