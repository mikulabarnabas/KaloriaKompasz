<?php

namespace App\Http\Controllers;

use App\Http\Requests\FoodRequest;
use Illuminate\Http\Request;
use App\Models\Foods;
use App\Models\FoodDiary;
use Inertia\Inertia;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FoodController extends Controller
{
    public function show()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        return Inertia::render('food_diary', [
            'foods' => Foods::query()
                ->orderBy('name')
                ->get([
                    'id',
                    'name',
                    'calories',
                    'fat_g',
                    'carbs_g',
                    'protein_g',
                    'image_path',
                ]),
        ]);
    }

    public function storeFood(FoodRequest $request)
    {
        $data = $request->validated();

        $food = Foods::create([
            'name' => $data['name'],
            'calories' => $data['calories'],
            'fat_g' => $data['fat_g'],
            'carbs_g' => $data['carbs_g'],
            'protein_g' => $data['protein_g'],
            'notes' => $data['notes'],
            'image_path' => null,
        ]);

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

    public function storeDiary(Request $request)
    {

        $data = $request->validate([
            'food_id' => ['required', 'integer', 'exists:foods,id'],
            'meal_type' => ['nullable', 'in:breakfast,lunch,dinner,snack,other'],
            'date' => ['nullable', 'date'],
        ]);

        $userId = (int) $request->user()->id;

        $date = isset($data['date'])
            ? Carbon::parse($data['date'])->toDateString()
            : now()->toDateString();

        $mealType = $data['meal_type'] ?? 'other';

        return DB::transaction(function () use ($userId, $date, $mealType, $data) {
            // Create today's diary header for THIS user (user_id = users.id)
            $diary = FoodDiary::query()->firstOrCreate(
                [
                    'user_id' => $userId,
                    'date' => $date,
                ],
                [
                    'meal_type' => $mealType,
                    'calories' => 0,
                    'fat_g' => 0,
                    'carbs_g' => 0,
                    'protein_g' => 0,
                ]
            );

            // Attach food in pivot (requires foods() relationship on FoodDiary)
            $diary->foods()->syncWithoutDetaching([(int) $data['food_id']]);

            return back()->with('success', 'Food added to today.');
        });
    }
}