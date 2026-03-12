<?php

namespace Database\Seeders;

use App\Models\Foods;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class FoodSeeder extends Seeder
{
    public function run(): void
    {
        // Ellenőrizd, hogy pontosan ez-e a fájlneved a storage/app mappában!
        $csvPath = storage_path('app/OpenFoodDactsDBHungaryFoods.csv');
        
        if (!File::exists($csvPath)) {
            $this->command->error("A CSV fájl nem található a megadott útvonalon: $csvPath");
            return;
        }

        $file = fopen($csvPath, "r");
        
        // Az első sor a fejléc
        $header = fgetcsv($file);
        
        // Ha van BOM (speciális karakter az elején), azt takarítsuk le a 'code' kulcsról
        $header[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header[0]);

        $count = 0;
        while (($row = fgetcsv($file)) !== FALSE) {
            // Összefésüljük a fejlécet az adattal
            $item = array_combine($header, $row);

            // Ellenőrizzük, hogy létezik-e a 'code' kulcs
            if (!isset($item['code']) || empty($item['code'])) {
                continue; // Ha nincs vonalkód, ugorjuk át ezt a sort
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