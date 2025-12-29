<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercises extends Model
{

    protected $fillable = [
        'name',
        'quantity',
        'burned_calories',
        'note',
    ];

    protected $casts = [
        'burned_calories' => 'integer',
    ];
}
