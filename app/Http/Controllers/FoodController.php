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

        $userId = (int) auth()->id();
        $today = now()->toDateString();

        $todayDiary = FoodDiary::query()
            ->where('user_id', $userId)
            ->whereDate('date', $today)
            ->with(['foods'])
            ->first();

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
            'todayDiary' => $todayDiary,
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
            'quantity' => ['nullable', 'integer', 'min:1', 'max:100'],
            'date' => ['nullable', 'date'],
        ]);

        $userId = (int) $request->user()->id;

        $date = isset($data['date'])
            ? Carbon::parse($data['date'])->toDateString()
            : now()->toDateString();

        $mealType = $data['meal_type'] ?? 'other';
        $quantity = (int) ($data['quantity'] ?? 1);

        return DB::transaction(function () use ($userId, $date, $mealType, $quantity, $data) {
            // One diary header per user per day
            $diary = FoodDiary::query()->firstOrCreate([
                'user_id' => $userId,
                'date' => $date,
            ]);

            // Allow duplicates: always create a new pivot row
            $diary->foods()->attach([
                (int) $data['food_id'] => [
                    'meal_type' => $mealType,
                    'quantity' => $quantity,
                ]
            ]);

            return back()->with('success', 'Food added to today.');
        });
    }
}