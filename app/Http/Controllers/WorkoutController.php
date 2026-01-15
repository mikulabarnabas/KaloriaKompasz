<?php

namespace App\Http\Controllers;

use App\Models\Exercises;
use App\Http\Requests\WorkoutRequest;
use App\Models\WorkoutDiary;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class WorkoutController extends Controller
{
    public function show(Request $request)
    {
        $userId = (int) $request->user()->id;

        $selectedDate = $request->query('date')
            ? Carbon::parse($request->query('date'))->toDateString()
            : now()->toDateString();

        $selectedDiary = WorkoutDiary::query()
            ->where('user_id', $userId)
            ->whereDate('date', $selectedDate)
            ->with([
                'exercises' => function ($q) {
                    $q->select([
                        'exercises.id',
                        'exercises.name',
                        'exercises.unit',
                        'exercises.calories_per_unit',
                    ]);
                },
            ])
            ->first();

        return Inertia::render('workout_diary', [
            'exercises' => Exercises::query()->orderBy('name')->get(),
            'selectedDate' => $selectedDate,
            'selectedDiary' => $selectedDiary,
        ]);
    }

    public function store(WorkoutRequest $request)
    {
        $data = $request->validated();

        Exercises::query()->create($data);

        return redirect()->back()->with('success', 'Exercise created.');
    }

    public function getDiaryByDate(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
        ]);

        $userId = (int) $request->user()->id;
        $date = Carbon::parse($data['date'])->toDateString();

        $diary = WorkoutDiary::query()
            ->where('user_id', $userId)
            ->whereDate('date', $date)
            ->with([
                'exercises' => function ($q) {
                    $q->select([
                        'exercises.id',
                        'exercises.name',
                        'exercises.unit',
                        'exercises.calories_per_unit',
                    ]);
                },
            ])
            ->first();

        return response()->json([
            'date' => $date,
            'diary' => $diary,
        ]);
    }

    public function addEntry(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'exercise_id' => ['required', 'integer', 'exists:exercises,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'burned_calories' => ['nullable', 'integer', 'min:0', 'max:200000'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $userId = (int) $request->user()->id;
        $date = Carbon::parse($data['date'])->toDateString();

        $quantity = (int) ($data['quantity'] ?? 1);
        $burnedCalories = (int) ($data['burned_calories'] ?? 0);
        $note = $data['note'] ?? null;

        $diary = DB::transaction(function () use ($userId, $date, $data, $quantity, $burnedCalories, $note) {
            $diary = WorkoutDiary::query()->firstOrCreate([
                'user_id' => $userId,
                'date' => $date,
            ]);

            $diary->exercises()->attach([
                (int) $data['exercise_id'] => [
                    'quantity' => $quantity,
                    'burned_calories' => $burnedCalories,
                    'note' => $note,
                ]
            ]);

            return $diary->fresh()->load('exercises');
        });

        return response()->json([
            'ok' => true,
            'date' => $date,
            'diary' => $diary,
        ]);
    }

    public function deleteEntry(Request $request)
    {
        $data = $request->validate([
            'entry_id' => ['required', 'integer'],
        ]);

        $userId = (int) $request->user()->id;

        $deleted = DB::table('exercises_to_workout_diary as p')
            ->join('workout_diary as d', 'd.id', '=', 'p.workout_diary_id')
            ->where('p.id', (int) $data['entry_id'])
            ->where('d.user_id', $userId)
            ->delete();

        if ($deleted === 0) {
            return response()->json([
                'ok' => false,
                'message' => 'Entry not found.',
            ], 404);
        }

        return response()->json([
            'ok' => true,
        ]);
    }
}