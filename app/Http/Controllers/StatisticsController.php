<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\FoodDiary;
use App\Models\WorkoutDiary;
use Illuminate\Support\Carbon;
use Log;

class StatisticsController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        $profile = $user->profile;
        $targets = [
            'calories' => $profile?->calories_per_day ?? 2000,
            'protein' => $profile?->protein_per_day ?? 150,
            'carbs' => $profile?->carbs_per_day ?? 200,
            'fat' => $profile?->fat_per_day ?? 70,
        ];

        return Inertia::render('statistics', [
            'hasProfile' => !is_null($profile),
            'targets' => $targets,
        ]);
    }

    public function getData(Request $request)
    {
        $user = $request->user();
        $date = $request['date'];

        $foodEntry = FoodDiary::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->with('foods')
            ->first();

        $workoutEntry = WorkoutDiary::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->with('exercises')
            ->first();

        $todayStats = [
            'calories' => $foodEntry?->foods->sum('pivot.calorie') ?? 0,
            'protein' => $foodEntry?->foods->sum('pivot.protein') ?? 0,
            'carbs' => $foodEntry?->foods->sum('pivot.carb') ?? 0,
            'fat' => $foodEntry?->foods->sum('pivot.fat') ?? 0,
            'burned' => $workoutEntry?->exercises->sum(fn($e) => $e->pivot->burned_calories) ?? 0,
        ];

        $recentActivity = collect()
            ->concat($foodEntry?->foods ?? [])
            ->concat($workoutEntry?->exercises ?? [])
            ->sortByDesc('pivot.created_at')
            ->take(4)
            ->values();

        return response()->json([
            'todayStats' => $todayStats,
            'recentActivity' => $recentActivity,
            'workoutDiary' => $user->workoutDiary()->with('exercises')->get(),
            'foodDiary' => $user->foodDiary()->with('foods')->get()
        ]);
    }

    public function getWeeklyStats(string $date)
    {
        $userId = auth()->id();
        $endDate = Carbon::parse($date)->endOfDay();
        $startDate = $endDate->copy()->subDays(6)->startOfDay();

        Log::info(FoodDiary::where('user_id', $userId)->with('foods')
            ->get());

        $foodDiaries = FoodDiary::where('user_id', $userId)
            ->whereBetween('date', [$startDate->toDateTimeString(), $endDate->toDateTimeString()])
            ->get()
            ->keyBy(fn($d) => Carbon::parse($d->date)->toDateString());

        $workoutDiaries = WorkoutDiary::where('user_id', $userId)
            ->whereBetween('date', [$startDate->toDateTimeString(), $endDate->toDateTimeString()])
            ->get()
            ->keyBy(fn($d) => Carbon::parse($d->date)->toDateString());



        $results = [];
        for ($i = 6; $i >= 0; $i--) {
            $currentDate = $endDate->copy()->subDays($i);
            $dateKey = $currentDate->toDateString();

            $foodEntry = $foodDiaries->get($dateKey);
            $workoutEntry = $workoutDiaries->get($dateKey);

            $results[] = [
                'date' => $dateKey,
                'label' => $currentDate->translatedFormat('D'),
                'calories' => (float) ($foodEntry?->foods->sum('pivot.calorie') ?? 0),
                'burned' => (float) ($workoutEntry?->exercises->sum(function ($e) {
                    return $e->pivot->amount * $e->calories_per_unit;
                }) ?? 0),
            ];
        }

        return response()->json($results);
    }
}