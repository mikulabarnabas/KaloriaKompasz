<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $dumpPath = database_path('datas/dump.sql');

        if (File::exists($dumpPath)) {
            $this->command->info('Found SQL dump file!');
            DB::unprepared(File::get($dumpPath));
            $this->command->info('SQL dump file imported.');
        } else {
            $this->command->warn('SQL dump not found. Running CSV seeders...');

            $this->call([
                OpenFoodFact::class,
                CSVFood::class,
                ExerciseSeeder::class
            ]);

            $this->command->info('CSV seeding finished.');
        }
    }
}