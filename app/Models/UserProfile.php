<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $table = 'user_profiles';
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'gender',
        'date_of_birth',
        'height_cm',
        'weight_kg',
        'activity_level',
        'diet',
        'weight_goal',
        'target_weight_kg',
        'goal_period_weeks', 
        'calories_per_day',
        'protein_per_day',
        'fat_per_day',
        'carbs_per_day',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'height_cm' => 'integer',
        'weight_kg' => 'float',
        'target_weight_kg' => 'float',
        'goal_period_weeks' => 'integer',
        'calories_per_day' => 'float',
        'protein_per_day' => 'float',
        'fat_per_day' => 'float',
        'carbs_per_day' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
