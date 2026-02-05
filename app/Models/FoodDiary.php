<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodDiary extends Model
{
    protected $table = 'food_diary';

    protected $fillable = [
        'user_id',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function foods()
    {
        return $this->belongsToMany(
            Foods::class,
            'food_to_food_diary',
            'food_diary_id',
            'food_id'
        )
            ->using(FoodDiaryFoodPivot::class)
            ->withPivot(['id', 'meal_type', 'amount', 'unit', 'fat', 'carb', 'protein', 'calorie'])
            ->withTimestamps();
    }
}