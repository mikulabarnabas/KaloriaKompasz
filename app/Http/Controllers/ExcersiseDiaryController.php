<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ExcersiseDiaryController extends Controller
{
    public function show()
    {
        return Inertia::render('workout_diary');
    }
}
