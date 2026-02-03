<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercises extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'calories_per_unit',
        'note',
    ];

    public function scopeSearch($query, $keywords)
    {
        return $query->where('name', 'LIKE', '%' . $keywords . '%');
    }

    public function workoutDiaries()
    {
        return $this->belongsToMany(WorkoutDiary::class, 'exercise_to_workout_diary')
            ->withPivot(['id', 'amount', 'unit', 'note'])
            ->withTimestamps();
    }
}
