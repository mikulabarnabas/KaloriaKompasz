<?php

namespace Database\Seeders;

use App\Models\Foods;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CSVFood extends Seeder
{
    public function run(): void
    {
        // Paths to your two files
        $enPath = database_path('datas/filtered_nutrition_data.csv');
        $huPath = database_path('datas/nutrition_translated.csv');

        if (!File::exists($enPath) || !File::exists($huPath)) {
            $this->command->error("One or both CSV files are missing.");
            return;
        }

        $enFile = fopen($enPath, "r");
        $huFile = fopen($huPath, "r");

        // Get headers and clean BOM/special chars from the first key
        $enHeader = fgetcsv($enFile);
        $huHeader = fgetcsv($huFile);
        $enHeader[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $enHeader[0]);
        $huHeader[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $huHeader[0]);

        $count = 0;

        // Read both files line by line simultaneously
        while (($enRow = fgetcsv($enFile)) !== FALSE && ($huRow = fgetcsv($huFile)) !== FALSE) {
            $enItem = array_combine($enHeader, $enRow);
            $huItem = array_combine($huHeader, $huRow);

            // Using the English name as the unique identifier since 'code' is missing
            Foods::updateOrCreate(
                ['name' => $enItem['names']], 
                [
                    'name_hu'  => $huItem['names'] ?? null,
                    'calorie'  => (float)($enItem['calorie'] ?? 0),
                    'protein'  => (float)($enItem['protein'] ?? 0),
                    'fat'      => (float)($enItem['total fats'] ?? 0),
                    'carb'     => (float)($enItem['carbs'] ?? 0),
                    'brand'    => 'Ismeretlen',
                    'unit'     => 'g',
                    'amount'   => 100,
                ]
            );
            $count++;
        }

        fclose($enFile);
        fclose($huFile);

        $this->command->info("Successfully imported $count food items!");
    }
}