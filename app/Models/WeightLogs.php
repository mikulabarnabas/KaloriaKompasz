<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class WeightLogs extends Model
{
    protected $fillable = [
        'user_id',
        'measured_at',
        'weight',
        'note',
    ];

    protected $casts = [
        'measured_at' => 'datetime',
        'weight' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
