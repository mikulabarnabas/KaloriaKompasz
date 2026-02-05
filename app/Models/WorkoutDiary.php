<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutDiary extends Model
{
    protected $table = 'workout_diary';

    protected $fillable = [
        'user_id',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exercises()
    {
        return $this->belongsToMany(
            \App\Models\Exercises::class,
            'exercises_to_workout_diary',
            'workout_diary_id',
            'exercise_id'
        )
            ->withPivot(['id', 'unit', 'amount', 'burned_calories'])
            ->withTimestamps();
    }
}
