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
            \App\Models\Foods::class,
            'food_to_food_diary',
            'food_diary_id',
            'food_id'
        )
            ->withPivot(['id', 'meal_type', 'quantity', 'created_at'])
            ->withTimestamps();
    }
}