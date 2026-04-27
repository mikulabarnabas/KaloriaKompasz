<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Foods extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_hu',
        'fat',
        'carb',
        'protein',
        'calorie',
        'unit',
        'amount',
        'barcode',
        'brand',
        'image',
    ];

    protected $casts = [
        'fat' => 'integer',
        'carb' => 'integer',
        'protein' => 'integer',
        'calorie' => 'integer',
    ];

    public function scopeSearch($query, $keywords)
    {
        return $query->where(function ($q) use ($keywords) {
            $q->where('name', 'LIKE', '%' . $keywords . '%')
                ->orWhere('name_hu', 'LIKE', '%' . $keywords . '%')
                ->orWhere('brand', 'LIKE', '%' . $keywords . '%')
                ->orWhere('barcode', $keywords);
        })
            ->latest();
    }

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
