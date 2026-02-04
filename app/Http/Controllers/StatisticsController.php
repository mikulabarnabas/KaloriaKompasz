<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\FoodDiary;
use App\Models\WorkoutDiary;
use App\Models\User;

class StatisticsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $foodDiary = $request->user()
            ->foodDiary()
            ->with('foods')
            ->get();

        $workoutDiary = $request->user()
            ->workoutDiary()
            ->with('exercises')
            ->get();

        return Inertia::render('statistics', [
            'foodDiary' => $foodDiary,
            'workoutDiary' => $workoutDiary,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
