<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Foods;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function foods()
    {
        return $this->belongsToMany(
            Foods::class,
            'food_to_food_diary',
            'food_diary_id', // THIS table's FK on pivot
            'food_id'        // Food FK on pivot
        );
    }
}