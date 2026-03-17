<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exercises;
use App\Enums\WorkoutUnits;
use Illuminate\Support\Facades\File;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        $csvEn = database_path('datas/exercise_dataset.csv');
        $csvHu = database_path('datas/exercise_hungarian.csv');

        if (!File::exists($csvEn) || !File::exists($csvHu)) {
            $this->command->error("One or both CSV files are missing from storage/app/");
            return;
        }

        $handleEn = fopen($csvEn, 'r');
        $handleHu = fopen($csvHu, 'r');

        fgetcsv($handleEn);
        fgetcsv($handleHu);

        Exercises::updateOrCreate(
            ['name' => 'Walking (Steps)'],
            [
                'name_hu' => 'Gyaloglás (lépésszám alapján)',
                'unit' => 'steps',
                'calories_per_unit' => 0.04,
                'note' => 'System entry for health sync',
            ]
        );

        while (($dataEn = fgetcsv($handleEn)) !== false) {
            $dataHu = fgetcsv($handleHu);

            $caloriesPerHour = array_sum([
                (float) $dataEn[1],
                (float) $dataEn[2],
                (float) $dataEn[3],
                (float) $dataEn[4]
            ]) / 4;

            Exercises::create([
                'name' => $dataEn[0],
                'name_hu' => $dataHu[0] ?? null,
                'unit' => WorkoutUnits::HOURS->value,
                'calories_per_unit' => $caloriesPerHour,
                'note' => 'Imported from dataset (converted from hours)',
            ]);
        }

        fclose($handleEn);
        fclose($handleHu);

        $this->command->info('Exercises imported successfully with Hungarian translations.');
    }
}