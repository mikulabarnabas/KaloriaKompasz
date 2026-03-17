<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $date = $request->input('date', now()->toDateString());

        $profile = $user->profile;
        $targets = [
            'calories' => $profile?->calories_per_day ?? 2000,
            'protein' => $profile?->protein_per_day ?? 150,
            'carbs' => $profile?->carbs_per_day ?? 200,
            'fat' => $profile?->fat_per_day ?? 70,
        ];

        $foodEntry = $user->foodDiary()->where('date', $date)->with('foods')->first();
        $workoutEntry = $user->workoutDiary()->where('date', $date)->with('exercises')->first();

        $todayStats = [
            'calories' => $foodEntry?->foods->sum('pivot.calorie') ?? 0,
            'protein' => $foodEntry?->foods->sum('pivot.protein') ?? 0,
            'carbs' => $foodEntry?->foods->sum('pivot.carb') ?? 0,
            'fat' => $foodEntry?->foods->sum('pivot.fat') ?? 0,
            'burned' => $workoutEntry?->exercises->sum(fn($e) => $e->pivot->burned_calories) ?? 0,
        ];

        // 4. Combine Activity
        $recentActivity = collect()
            ->concat($foodEntry?->foods ?? [])
            ->concat($workoutEntry?->exercises ?? [])
            ->sortByDesc('pivot.created_at')
            ->take(4)
            ->values();

        return Inertia::render('statistics', [
            'hasProfile' => !is_null($profile),
            'targets' => $targets,
            'todayStats' => $todayStats,
            'recentActivity' => $recentActivity,
            'workoutDiary' => $user->workoutDiary()->with('exercises')->get(),
            'foodDiary' => $user->foodDiary()->with('foods')->get()
        ]);
    }
}