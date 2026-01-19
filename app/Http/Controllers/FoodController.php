<?php

namespace App\Http\Controllers;

use App\Http\Requests\FoodRequest;
use App\Models\FoodDiary;
use App\Models\Foods;
use Illuminate\Http\Client\Events\RequestSending;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class FoodController extends Controller
{
    public function show(Request $request)
    {
        return Inertia::render('food_diary', [
            'foods' => Foods::all()
        ]);
    }

    public function getDiaryByDate(Request $request, string $date)
    {
        $userId = (int) $request->user()->id;

        try {
            $date = Carbon::parse($date)->toDateString();
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Invalid date format.',
            ], 422);
        }

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
                    ])->withPivot(['id', 'amount', 'meal_type', 'created_at']);
                },
            ])
            ->first();

        return response()->json([
            'ok' => true,
            'date' => $date,
            'diary' => $diary,
        ]);
    }

    public function addEntry(Request $request)
    {
        $data = $request->validate([
            'food_id' => ['required', 'integer', 'exists:foods,id'],
            'meal_type' => ['in:breakfast,lunch,dinner,snack,other'],
            'amount' => ['integer', 'min:1'],
            'unit' => ['in:g,kg,dkg,l,dl,cl']
        ]);

        $date = $request->validate([
            'date' => ['required', 'date'],
        ]);

        $date = Carbon::parse($date['date'])->toDateString();
        $userId = (int) $request->user()->id;
        
        $diary = FoodDiary::query()->firstOrCreate([
            'user_id' => $userId,
            'date' => $date,
        ]);

        $diary->foods()->attach($data['food_id'], $data);

        return response()->json(['ok' => true]);
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