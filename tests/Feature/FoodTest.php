<?php

use App\Models\User;
use App\Models\Foods;
use App\Models\FoodDiary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('az ételnapló főoldala elérhető', function () {
    $felhasznalo = User::factory()->create();

    $this->actingAs($felhasznalo)
        ->get(route('food.index'))
        ->assertStatus(200)
        ->assertInertia(fn($oldal) => $oldal->component('food_diary'));
});

test('a napló lekérése dátum alapján visszaadja az összesítést', function () {
    $felhasznalo = User::factory()->create();
    $etel = Foods::factory()->create();
    $datumStr = '2026-03-20';

    // FoodDiary létrehozása - Kifejezetten csak dátummal, idő nélkül
    $naplo = FoodDiary::create([
        'user_id' => $felhasznalo->id,
        'date' => $datumStr
    ]);

    $naplo->foods()->attach($etel->id, [
        'meal_type' => 'breakfast',
        'amount' => 100,
        'unit' => 'g',
        'calorie' => 200,
        'protein' => 10,
        'carb' => 20,
        'fat' => 5
    ]);

    // Ellenőrizzük a kontrollert
    $this->actingAs($felhasznalo)
        ->getJson("/fdiary/diary/{$datumStr}")
        ->assertOk()
        ->assertJsonPath('totals.calories', 200);
});

test('új étel rögzíthető a naplóba', function () {
    $felhasznalo = User::factory()->create();
    $etel = Foods::factory()->create();
    $datum = '2026-03-20';

    $adatok = [
        'food_id' => $etel->id,
        'meal_type' => 'lunch',
        'amount' => 100,
        'unit' => 'g',
        'date' => $datum
    ];

    $this->actingAs($felhasznalo)
        ->postJson('/fdiary/entry', $adatok)
        ->assertOk()
        ->assertJson(['ok' => true]);

    $this->assertDatabaseHas('food_diaries', [
        'user_id' => $felhasznalo->id,
        'date' => $datum
    ]);
});

test('új étel létrehozható képpel együtt', function () {
    Storage::fake('public');
    $felhasznalo = User::factory()->create();
    $kep = UploadedFile::fake()->image('etel.jpg');

    $kamuEtel = Foods::factory()->make();

    $adatok = [
        'name' => $kamuEtel->name,
        'calorie' => $kamuEtel->calorie,
        'protein' => $kamuEtel->protein,
        'carb' => $kamuEtel->carb,
        'fat' => $kamuEtel->fat,
        'unit' => $kamuEtel->unit,
        'image' => $kep
    ];

    $valasz = $this->actingAs($felhasznalo)
        ->postJson('/fdiary/create', $adatok);

    $valasz->assertOk()
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('foods', ['name' => $kamuEtel->name]);

    $etelId = $valasz->json('food.id');
    Storage::disk('public')->assertExists("foods/{$etelId}");
});

test('egy bejegyzés törölhető a naplóból', function () {
    $felhasznalo = User::factory()->create();
    $datum = '2026-03-20';
    $etel = Foods::factory()->create();
    $naplo = FoodDiary::create(['user_id' => $felhasznalo->id, 'date' => $datum]);

    $naplo->foods()->attach($etel->id, [
        'meal_type' => 'snack',
        'amount' => 50,
        'unit' => 'g'
    ]);

    $pivotId = $naplo->foods()->first()->pivot->id;

    $this->actingAs($felhasznalo)
        ->deleteJson("/fdiary/entry/{$datum}/{$pivotId}")
        ->assertStatus(204);

    $this->assertDatabaseMissing('food_to_food_diary', [
        'id' => $pivotId
    ]);
});

test('az ételek között lehet keresni', function () {
    $felhasznalo = User::factory()->create();
    $nev = 'Zabkása';
    Foods::factory()->create(['name' => $nev]);

    $this->actingAs($felhasznalo)
        ->getJson("/fdiary/getFoods/" . substr($nev, 0, 3) . "/1")
        ->assertOk()
        ->assertJsonCount(1, 'result')
        ->assertJsonPath('result.0.name', $nev);
});

test('a keresés visszaadja a találatok számát', function () {
    $felhasznalo = User::factory()->create();
    $nev = 'Körte';
    Foods::factory()->count(5)->create(['name' => $nev]);

    $this->actingAs($felhasznalo)
        ->getJson("/fdiary/getPageCount/{$nev}")
        ->assertOk()
        ->assertJson(['pageCount' => 5]);
});
