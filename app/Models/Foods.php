<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Foods extends Model
{

    protected $fillable = [
        'name',
        'fat_g',
        'carbs_g',
        'protein_g',
        'calories',
        'notes',
        'image_path',
    ];

    protected $casts = [
        'fat_g' => 'integer',
        'carbs_g' => 'integer',
        'protein_g' => 'integer',
        'calories' => 'integer',
    ];

    public function diaries()
    {
        return $this->belongsToMany(
            FoodDiary::class,
            'food_to_food_diary',
            'food_id',       // THIS table's FK on pivot
            'food_diary_id'  // Diary FK on pivot
        );
    }
}
