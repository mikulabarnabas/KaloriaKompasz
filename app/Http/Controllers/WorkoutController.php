<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkoutRequest;
use App\Models\Exercises;
use App\Models\WorkoutDiary;
use App\Enums\WorkoutUnits;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class WorkoutController extends Controller
{
    public function show(Request $request)
    {
        return Inertia::render('workout_diary');
    }

    public function getDiaryByDate(Request $request, string $date)
    {
        $userId = (int) $request->user()->id;
        $date = Carbon::parse($date)->toDateString();

        $diary = WorkoutDiary::query()
            ->where('user_id', $userId)
            ->whereDate('date', $date)
            ->with('exercises')
            ->first();

        return response()->json([
            'diary' => $diary,
        ]);
    }


    public function storeExercise(WorkoutRequest $request)
    {
        $data = $request->validated();

        Exercises::create($data);

        return response()->json([
            'success' => true,
        ]);
    }


    public function addEntry(Request $request)
    {
        $data = $request->validate([
            'exercise_id' => ['required', 'integer', 'exists:exercises,id'],
            'amount' => ['integer', 'min:1'],
            'unit' => ['required', 'in:' . implode(',', WorkoutUnits::values())],
        ]);

        $date = $request->validate([
            'date' => ['required', 'date'],
        ]);

        $date = Carbon::parse($date['date'])->toDateString();
        $userId = (int) $request->user()->id;

        $diary = WorkoutDiary::firstOrCreate([
            'user_id' => $userId,
            'date' => $date,
        ]);

        $diary->exercises()->attach($data['exercise_id'], $data);

        return response()->json(['ok' => true]);
    }


    public function deleteEntry(Request $request, string $date, string $entryId)
    {
        $userId = (int) $request->user()->id;

        $diary = WorkoutDiary::query()
            ->where('user_id', $userId)
            ->whereDate('date', $date)
            ->firstOrFail();

        $diary->exercises()
            ->newPivotQuery()
            ->where('id', $entryId)
            ->delete();

        return response(204);
    }


    public function getExercises(string $searchTerm, string $page)
    {
        $page -= 1;
        $perPage = 5;

        $result = Exercises::search($searchTerm)
            ->skip($perPage * $page)
            ->limit($perPage)
            ->get() ?? [];

        return response()->json([
            'result' => $result,
        ]);
    }


    public function getPageCount(string $searchTerm)
    {
        return response()->json([
            'pageCount' => Exercises::search($searchTerm)->count(),
        ]);
    }
}
