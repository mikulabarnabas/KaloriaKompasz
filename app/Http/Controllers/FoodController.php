<?php

namespace App\Http\Controllers;

use App\Http\Requests\FoodRequest;
use App\Models\FoodDiary;
use App\Models\Foods;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use App\Enums\FoodUnits;
use Illuminate\Support\Facades\Log;

class FoodController extends Controller
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

        return Inertia::render('food_diary', [
            'hasProfile' => !is_null($profile),
            'targets' => $targets,
        ]);
    }

    public function getDiaryByDate(Request $request, string $date)
    {
        $userId = (int) $request->user()->id;
        $date = Carbon::parse($date)->toDateString();

        $diary = FoodDiary::query()
            ->where('user_id', $userId)
            ->whereDate('date', $date)
            ->first()?->foods ?? collect();

        $groupedByMealTypes = $diary->groupBy(fn($food) => $food->pivot->meal_type);

        $order = [
            'breakfast',
            'lunch',
            'dinner',
            'snack',
            'other'
        ];

        $sortedDiary = collect($order)
            ->mapWithKeys(fn($type) => [
                $type => $groupedByMealTypes->get($type, collect())
            ])
            ->filter(fn($group) => $group->isNotEmpty());

        $dailyTotals = [
            'calories' => $diary->sum('pivot.calorie'),
            'protein' => $diary->sum('pivot.protein'),
            'carbs' => $diary->sum('pivot.carb'),
            'fat' => $diary->sum('pivot.fat'),
        ];

        return response()->json([
            'diary' => $sortedDiary,
            'totals' => $dailyTotals,
        ]);
    }


    public function addEntry(Request $request)
    {
        $data = $request->validate([
            'food_id' => ['required', 'integer', 'exists:foods,id'],
            'meal_type' => ['in:breakfast,lunch,dinner,snack,other'],
            'amount' => ['required', 'numeric', 'min:0.1'],
            'unit' => ['required', 'string', 'in:' . implode(',', FoodUnits::values())]
        ]);

        $date = $request->validate([
            'date' => ['required', 'date'],
        ]);

        $date = Carbon::parse($date['date'])->toDateString();
        $userId = (int) $request->user()->id;
        $diary = FoodDiary::firstOrCreate([
            'user_id' => $userId,
            'date' => $date,
        ]);
        $diary->foods()->attach($data['food_id'], [
            'meal_type' => $data['meal_type'],
            'amount' => $data['amount'],
            'unit' => $data['unit'],
        ]);

        return response()->json(['ok' => true]);
    }

    public function deleteEntry(Request $request, string $date, string $entryId)
    {
        $userId = (int) $request->user()->id;
        $diary = FoodDiary::query()
            ->where('user_id', $userId)
            ->whereDate('date', $date)
            ->firstOrFail();
        $diary->foods()->newPivotQuery()->where('id', $entryId)->delete();
        return response()->noContent();
    }

    public function storeFood(FoodRequest $request)
    {
        $data = $request->validated();
        $food = Foods::create($data);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = "food_" . time() . "." . $file->extension();

            $file->storeAs(
                "foods/{$food->id}",
                $filename,
                'public'
            );

            $food->update([
                'image' => "storage/foods/{$food->id}/{$filename}",
            ]);
        }

        return response()->json([
            'success' => true,
            'food' => $food
        ]);
    }


    public function getFoods(string $searchTerm, string $page)
    {
        $page -= 1; #Beacuse It would skip the first page
        $foodPerPage = 10;
        $result = Foods::search($searchTerm)->skip($foodPerPage * $page)->limit($foodPerPage)->get() ?? [];
        return response()->json([
            'result' => $result,
        ]);
    }

    public function getPageCount(string $searchTerm)
    {
        $result = Foods::search($searchTerm)->count();
        return response()->json([
            'pageCount' => $result,
        ]);
    }
}
