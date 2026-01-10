<?php

namespace App\Http\Controllers;

use App\Http\Requests\FoodRequest;
use App\Models\FoodDiary;
use App\Models\Foods;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FoodController extends Controller
{
    public function show(Request $request)
    {

        $userId = (int) $request->user()->id;
        $selectedDate = $request->query('date')
            ? Carbon::parse($request->query('date'))->toDateString()
            : now()->toDateString();

        $selectedDiary = FoodDiary::query()
            ->where('user_id', $userId)
            ->whereDate('date', $selectedDate)
            ->with([
                'foods' => function ($q) {
                    $q->select([
                        'foods.id',
                        'foods.name',
                        'foods.calorie',
                        'foods.fat',
                        'foods.carb',
                        'foods.protein',
                        'foods.image_path',
                    ]);
                },
            ])
            ->first();

        return Inertia::render('food_diary', [
            'foods' => Foods::all(),
            'selectedDate' => $selectedDate,
            'selectedDiary' => $selectedDiary,
        ]);
    }

    public function getDiaryByDate(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
        ]);

        $userId = (int) $request->user()->id;
        $date = Carbon::parse($data['date'])->toDateString();

        $diary = FoodDiary::query()
            ->where('user_id', $userId)
            ->whereDate('date', $date)
            ->with([
                'foods' => function ($q) {
                    $q->select([
                        'foods.id',
                        'foods.name',
                        'foods.calorie',
                        'foods.fat',
                        'foods.carb',
                        'foods.protein',
                        'foods.image_path',
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
            'food_id' => ['required', 'integer', 'exists:foods,id'],
            'meal_type' => ['nullable', 'in:breakfast,lunch,dinner,snack,other'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $userId = (int) $request->user()->id;
        $date = Carbon::parse($data['date'])->toDateString();
        $mealType = $data['meal_type'] ?? 'other';
        $quantity = (int) ($data['quantity'] ?? 1);

        $diary = DB::transaction(function () use ($userId, $date, $data, $mealType, $quantity) {
            $diary = FoodDiary::query()->firstOrCreate([
                'user_id' => $userId,
                'date' => $date,
            ]);

            $diary->foods()->attach([(int) $data['food_id'] => [
                'meal_type' => $mealType,
                'quantity' => $quantity,
            ]]);

            return $diary->fresh()->load('foods');
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

        $deleted = DB::table('food_to_food_diary as p')
            ->join('food_diary as d', 'd.id', '=', 'p.food_diary_id')
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

    public function storeFood(FoodRequest $request)
    {
        $data = $request->validated();

        $food = Foods::create($data);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = strtolower($file->extension());

            $storedPath = $file->storeAs(
                'foods',
                "food_{$food->id}.{$ext}",
                'public'
            );

            $food->update([
                'image_path' => $storedPath,
            ]);
        }

        return redirect()->back()->with('success', 'Food created.');
    }
}