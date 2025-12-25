<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\FoodDiary;

class FoodDiaryController extends Controller
{
    public function show()
    {
        return Inertia::render('food_diary');
    }
}
