<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Foods extends Model
{

    protected $fillable = [
        'name',
        'fat',
        'carb',
        'protein',
        'calorie',
        'unit',
        'amount',
        'notes',
        'image_path',
    ];

    protected $casts = [
        'fat' => 'integer',
        'carb' => 'integer',
        'protein' => 'integer',
        'calorie' => 'integer',
    ];

    public function diaries()
    {
        return $this->belongsToMany(
            FoodDiary::class,
            'food_to_food_diary',
            'food_id',
            'food_diary_id' 
        );
    }
}
