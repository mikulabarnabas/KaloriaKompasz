<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exercises extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_hu',
        'unit',
        'calories_per_unit',
        'note',
    ];

    public function scopeSearch($query, $keywords)
    {
        return $query->where('name', 'LIKE', '%' . $keywords . '%')
            ->orWhere('name_hu', 'LIKE', '%' . $keywords . '%');
    }

    public function workoutDiaries()
    {
        return $this->belongsToMany(WorkoutDiary::class, 'exercise_to_workout_diary')
            ->withPivot(['id', 'amount', 'unit', 'note'])
            ->withTimestamps();
    }
}
