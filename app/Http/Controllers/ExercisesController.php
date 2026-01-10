<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkoutDiaryRequest;
use App\Models\Exercises;
use Inertia\Inertia;

class ExercisesController extends Controller
{
    public function show()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        return Inertia::render('workout_diary', [
            'exercises' => Exercises::all()]);
    }

    public function store(WorkoutDiaryRequest $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $data = $request->validated();

        Exercises::create($data);

        return redirect()->back()->with('success', 'Exercise created.');
    }
}