<?php

use App\Models\User;
use App\Models\Foods;
use App\Models\FoodDiary;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Cloudinary\Api\Upload\UploadApi;
use Mockery\MockInterface;
use Cloudinary\Api\ApiResponse;

uses(RefreshDatabase::class);

test('the food diary main page is accessible', function () {
    $felhasznalo = User::factory()->create();

    $this->actingAs($felhasznalo)
        ->get(route('food.show'))
        ->assertStatus(200)
        ->assertInertia(fn($oldal) => $oldal->component('food_diary'));
});

test('a new food entry can be recorded in the diary', function () {
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
});




test('a new food can be created with an image', function () {
    $fakeUrl = 'https://res.cloudinary.com/demo/image/upload/sample.jpg';
    $fakeResponse = new ApiResponse([
        'secure_url' => $fakeUrl,
        'public_id' => 'fake_id'
    ], []);

    $this->mock(UploadApi::class, function (MockInterface $mock) use ($fakeResponse) {
        $mock->shouldReceive('upload')
             ->once()
             ->andReturn($fakeResponse); 
    });

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
        'amount' => $kamuEtel->amount,
        'image' => $kep
    ];

    $valasz = $this->actingAs($felhasznalo)->postJson('/fdiary/create', $adatok);

    $valasz->assertOk()->assertJson(['success' => true]);

    $this->assertDatabaseHas('foods', [
        'name' => $kamuEtel->name,
        'image' => $fakeUrl,
    ]);
});

test('fetching the diary by date returns the summary', function () {
    $felhasznalo = User::factory()->create();
    $etel = Foods::factory()->create();
    $datumStr = '2026-03-20';

    $naplo = FoodDiary::create([
        'user_id' => $felhasznalo->id,
        'date' => $datumStr
    ]);

    $naplo->foods()->attach($etel->id, [
        'meal_type' => 'breakfast',
        'amount' => 100,
        'unit' => 'g',
    ]);

    $this->actingAs($felhasznalo)
        ->getJson("/fdiary/diary/{$datumStr}")
        ->assertOk()
        ->assertJsonPath('totals.calories', fn($value) => $value > 0);
});

test('an entry can be deleted from the diary', function () {
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

test('foods can be searched by name', function () {
    $felhasznalo = User::factory()->create();
    $nev = 'Zabkása';
    Foods::factory()->create(['name' => $nev]);

    $this->actingAs($felhasznalo)
        ->getJson("/fdiary/getFoods/" . substr($nev, 0, 3) . "/1")
        ->assertOk()
        ->assertJsonCount(1, 'result');
});
