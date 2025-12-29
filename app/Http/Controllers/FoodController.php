<?php

namespace App\Http\Controllers;

use App\Http\Requests\FoodDiaryRequest;
use App\Models\Foods;
use Inertia\Inertia;

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

    public function create(FoodDiaryRequest $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $food = Foods::create($request->validated());

        return redirect()
            ->back()
            ->with('success', 'Food created successfully.');

    }
}