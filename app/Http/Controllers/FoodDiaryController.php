<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class FoodDiaryController extends Controller
{
    public function show()
    {
        return Inertia::render('food_diary');
    }
}
