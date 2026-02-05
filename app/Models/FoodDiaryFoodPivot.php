<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class FoodDiaryFoodPivot extends Pivot
{
    protected $table = 'food_to_food_diary';

    protected $fillable = [
        'food_id',
        'food_diary_id',
        'meal_type',
        'amount',
        'unit',
        'fat',
        'carb',
        'protein',
        'calorie'
    ];
}

