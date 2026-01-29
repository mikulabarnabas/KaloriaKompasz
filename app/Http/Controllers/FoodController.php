<?php

namespace App\Http\Controllers;

use App\Http\Requests\FoodRequest;
use App\Models\FoodDiary;
use App\Models\Foods;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\App;

class FoodController extends Controller
{
    public function show(Request $request)
    {
        return Inertia::render('food_diary');
    }

    public function getDiaryByDate(Request $request, string $date)
    {
        $userId = (int) $request->user()->id;
        $date = Carbon::parse($date)->toDateString();
        $diary = FoodDiary::getDiaryByIdAndDate($userId, $date)->first()?->foods ?? [];
        return response()->json([
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
        $diary = FoodDiary::firstOrCreate([
            'user_id' => $userId,
            'date'    => $date,
        ]);
        $diary->foods()->attach($data['food_id'], $data);

        return response()->json(['ok' => true]);
    }

    public function deleteEntry(Request $request, string $date, string $entryId)
    {
        $userId = (int) $request->user()->id;
        $diary = FoodDiary::getDiaryByIdAndDate($userId, $date)->firstOrFail();
        $diary->foods()->newPivotQuery()->where('id', $entryId)->delete();
        return response(204);
    }

public function storeFood(FoodRequest $request)
{
    $data = $request->safe()->except(['images']);
    $food = Foods::create($data);

    if ($request->hasFile('images')) {
        $paths = "{$food->id}:";

        foreach ($request->file('images') as $index => $file) {
            $filename = "image_{$index}." . $file->extension();
            $paths .= $filename . ":";
            $file->storeAs(
                "foods/{$food->id}",
                $filename,
                'public'
            );
        }
        $food->update([
            'image_paths' => $paths,
        ]);
    }
     return response()->json(['success' => true]);
}



    public function getFoods(string $searchTerm, string $page)
    {
        $page -= 1; #Beacuse It would skip the first page
        $foodPerPage = 5;
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
