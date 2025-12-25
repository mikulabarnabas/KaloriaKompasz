<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodDiary extends Model
{
    protected $table = "food_diary";

    protected $fillable = [
        'user_id',
        'date',
        'meal_type',
        'calories',
        'fat_g',
        'carbs_g',
        'protein_g',
    ];

    protected $casts = [
        'date' => 'date',
        'calories' => 'integer',
        'fat_g' => 'integer',
        'carbs_g' => 'integer',
        'protein_g' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}