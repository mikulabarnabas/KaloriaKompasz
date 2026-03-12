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
        $profile = $user->profile;
        $hasProfile = !is_null($profile);
        $targets = [
            'calories' => $profile?->calories_per_day ?? 2000,
            'protein'  => $profile?->protein_per_day ?? 150,
            'carbs'    => $profile?->carbs_per_day ?? 200,
            'fat'      => $profile?->fat_per_day ?? 70,
        ];

        $date = $request->query('date', Carbon::today()->toDateString());
        $foodEntry = $user->foodDiary()
            ->where('date', $date)
            ->with('foods')
            ->first();

        $todayStats = ['calories' => 0, 'protein' => 0, 'carbs' => 0, 'fat' => 0];
        $activityItems = collect();

        if ($foodEntry) {
            foreach ($foodEntry->foods as $food) {
                $todayStats['calories'] += $food->pivot->calorie;
                $todayStats['protein']  += $food->pivot->protein;
                $todayStats['carbs']    += $food->pivot->carb;
                $todayStats['fat']      += $food->pivot->fat;

                $activityItems->push([
                    'id' => 'food-' . $food->pivot->id,
                    'sort_id' => $food->pivot->id,
                    'name' => $food->name,
                    'type' => 'food',
                    'amount' => $food->pivot->amount,
                    'unit' => $food->unit,
                    'value' => $food->pivot->calorie
                ]);
            }
        }

        $workoutEntry = $user->workoutDiary()
            ->where('date', $date)
            ->with('exercises')
            ->first();

        $burned = 0;
        if ($workoutEntry) {
            foreach ($workoutEntry->exercises as $ex) {
                $calBurned = $ex->pivot->amount * $ex->calories_per_unit;
                $burned += $calBurned;

                $activityItems->push([
                    'id' => 'workout-' . $ex->pivot->id,
                    'sort_id' => $ex->pivot->id,
                    'name' => $ex->name,
                    'name_hu' => $ex->name_hu,
                    'type' => 'workout',
                    'amount' => $ex->pivot->amount,
                    'unit' => $ex->unit,
                    'value' => $calBurned
                ]);
            }
        }

        $recentActivity = $activityItems->sortByDesc('sort_id')->take(4)->values();

        return Inertia::render('statistics', [
            'hasProfile'     => $hasProfile,
            'targets'        => $targets,
            'todayStats'     => $todayStats,
            'burned'         => $burned,
            'recentActivity' => $recentActivity,
            'selectedDate'   => $date,
            'foodDiary'      => $user->foodDiary()->with('foods')->get(),
            'workoutDiary'   => $user->workoutDiary()->with('exercises')->get(),
        ]);
    }
}