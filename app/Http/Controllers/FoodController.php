<?php

namespace App\Http\Controllers;

use App\Http\Requests\FoodRequest;
use App\Models\FoodDiary;
use App\Models\Foods;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\App;
use phpDocumentor\Reflection\Types\Object_;

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

    private const UNIT_TO_BASE = [
        'g' => 1,
        'dkg' => 10,
        'kg' => 1000,
        'ml' => 1,
        'dl' => 100,
        'l' => 1000,
    ];

    public function normalizeFood(array $data): array
    {
        $unitFactor = self::UNIT_TO_BASE[$data['unit']] ?? 1;

        // convert submitted amount to grams/ml
        $baseAmount = $data['amount'] * $unitFactor;

        if ($baseAmount <= 0) {
            throw new \InvalidArgumentException('Amount must be > 0');
        }

        // normalize to per 100g/ml
        $factor = 100 / $baseAmount;

        $data['calorie'] = round($data['calorie'] * $factor, 2);
        $data['fat']     = round($data['fat'] * $factor, 2);
        $data['carb']    = round($data['carb'] * $factor, 2);
        $data['protein'] = round($data['protein'] * $factor, 2);

        // store normalized base reference
        $data['amount'] = 100;
        $data['unit']   = in_array($data['unit'], ['g', 'dkg', 'kg']) ? 'g' : 'ml';

        return $data;
    }

    public function storeFood(FoodRequest $request)
    {
        $data = $request->validated();

        $data = $this->normalizeFood($data);

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
