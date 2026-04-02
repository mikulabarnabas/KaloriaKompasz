<?php

use App\Models\User;
use App\Models\Food;
use App\Models\Exercises;
use App\Models\FoodDiary;
use App\Models\Foods;
use App\Models\WorkoutDiary;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the statistics page loads with default targets if no profile exists', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('stat.show'))
        ->assertOk()
        ->assertInertia(
            fn($page) => $page
                ->component('statistics')
                ->where('hasProfile', false)
                ->where('targets.calories', 2000)
        );
});

test('the statistics page uses the specific targets defined in the profile', function () {
    $user = User::factory()->create();
    UserProfile::factory()->for($user)->create([
        'calories_per_day' => 2500,
        'protein_per_day' => 180
    ]);

    $this->actingAs($user)
        ->get(route('stat.show'))
        ->assertOk()
        ->assertInertia(
            fn($page) => $page
                ->where('hasProfile', true)
                ->where('targets.calories', fn($value) => $value > 0)
                ->where('targets.protein', fn($value) => $value > 0)
        );
});

test('the getData API returns accurate aggregated data', function () {
    $user = User::factory()->create();
    $date = '2026-03-28';

    $foodDiary = FoodDiary::create(['user_id' => $user->id, 'date' => $date]);

    $food = Foods::factory()->create([
        'calorie' => 1000
    ]);

    $foodDiary->foods()->attach($food->id, [
        'meal_type' => 'breakfast',
        'amount' => 100,
        'unit' => 'g',
    ]);

    $workoutDiary = WorkoutDiary::create(['user_id' => $user->id, 'date' => $date]);
    $exercise = Exercises::factory()->create();
    $workoutDiary->exercises()->attach($exercise->id, [
        'burned_calories' => 500,
        'amount' => 60,
        'unit' => 'minutes'
    ]);

    $response = $this->actingAs($user)
        ->getJson("/stats/getData/{$date}")
        ->assertOk();

    $response->assertJsonPath('todayStats.calories', 1000);
    $response->assertJsonPath('todayStats.burned', 500);
});

test('the weekly statistics correctly calculate the last 7 days', function () {
    $user = User::factory()->create();
    $today = '2026-03-28';

    $foodDiary = FoodDiary::create(['user_id' => $user->id, 'date' => $today]);
    $food = Foods::factory()->create();
    $foodDiary->foods()->attach($food->id, [
        'meal_type' => 'breakfast',
        'amount' => 100,
        'unit' => 'g',
    ]);

    $workoutDiary = WorkoutDiary::create(['user_id' => $user->id, 'date' => $today]);
    $exercise = Exercises::factory()->create();
    $workoutDiary->exercises()->attach($exercise->id, [
        'burned_calories' => 500,
        'amount' => 60,
        'unit' => 'minutes'
    ]);

    $response = $this->actingAs($user)
        ->getJson("/stats/weekly/{$today}")
        ->assertOk();

    $results = $response->json();

    expect($results)->toHaveCount(7);
    expect(end($results)['date'])->toBe($today);
    expect(end($results)['calories'])->toBeGreaterThan(0);
    expect(end($results)['burned'])->toBeGreaterThan(0);

});