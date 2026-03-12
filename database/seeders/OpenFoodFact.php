<?php

namespace Database\Seeders;

use App\Models\Foods;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class OpenFoodFact extends Seeder
{
    public function run(): void
    {
        $csvPath = database_path('datas/OpenFoodDactsDBHungaryFoods.csv');

        $file = fopen($csvPath, "r");
        $header = fgetcsv($file);
        
        $header[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header[0]);

        $count = 0;
        while (($row = fgetcsv($file)) !== FALSE) {
            $item = array_combine($header, $row);

            if (!isset($item['code']) || empty($item['code'])) {
                continue;
            }

            Foods::updateOrCreate(
                ['barcode' => $item['code']], 
                [
                    'brand'      => $item['brand'] ?? 'Ismeretlen',
                    'name'       => $item['name_main'] ?? 'Névtelen termék',
                    'name_hu'    => $item['name_hu'] ?? null,
                    'calorie'    => (int)($item['calorie'] ?? 0),
                    'protein'    => (int)($item['protein'] ?? 0),
                    'fat'        => (int)($item['fat'] ?? 0),
                    'carb'       => (int)($item['carb'] ?? 0),
                    'image' => $item['image_url'] ?? null,
                    'unit'       => 'g',
                    'amount'     => 100,
                ]
            );
            $count++;
        }

        fclose($file);
        $this->command->info("Sikeresen betöltve $count termék!");
    }
}