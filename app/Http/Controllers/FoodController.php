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

    public function store(FoodDiaryRequest $request)
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
}