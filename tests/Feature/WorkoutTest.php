<?php

use App\Models\User;
use App\Models\Exercises;
use App\Models\WorkoutDiary;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('accessing diary page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('workout.show'))
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page->component('workout_diary'));
});



test('add exercise to diary', function () {
    $user = User::factory()->create();
    $exercise = Exercises::factory()->create([
        'calories_per_unit' => 600,
        'unit' => 'hours'
    ]);
    
    $date = now()->toDateString();

    $this->actingAs($user)->postJson('/wdiary/entry', [
        'exercise_id' => $exercise->id,
        'amount' => 30,
        'unit' => 'minutes',
        'date' => $date
    ])->assertOk();

    $diary = WorkoutDiary::where('user_id', $user->id)->whereDate('date', $date)->first();
    $entry = $diary->exercises()->first();

    expect($entry->pivot->amount)->toBe(30);
    expect((float)$entry->pivot->burned_calories)->toBe(300.0);
});

test('deleting entry from diary', function () {
    $user = User::factory()->create();
    $exercise = Exercises::factory()->create();
    $date = now()->toDateString();
    
    $diary = WorkoutDiary::create(['user_id' => $user->id, 'date' => $date]);
    $diary->exercises()->attach($exercise->id, ['amount' => 10, 'unit' => 'minutes']);
    
    $pivotId = $diary->exercises()->first()->pivot->id;

    $this->actingAs($user)
        ->deleteJson("/wdiary/entry/{$date}/{$pivotId}")
        ->assertStatus(204);

    expect($diary->exercises()->count())->toBe(0);
});

test('searching exercises', function () {
    $user = User::factory()->create();
    Exercises::factory()->create(['name' => 'Futás']);
    Exercises::factory()->create(['name' => 'Úszás']);

    $this->actingAs($user)
        ->getJson('/wdiary/getExercises/Fut/1')
        ->assertOk()
        ->assertJsonCount(1, 'result')
        ->assertJsonPath('result.0.name', 'Futás');
});

test('sync steps Android', function () {
    $user = User::factory()->create();
    Exercises::factory()->create(['id' => 1, 'name' => 'Steps']); 
    
    $date = now()->toDateString();

    $this->actingAs($user)->postJson('/wdiary/sync-steps', [
        'steps' => 10000,
        'date' => $date
    ])->assertOk();

    $this->assertDatabaseHas('exercises_to_workout_diary', [
        'amount' => 10000,
        'unit' => 'steps',
        'burned_calories' => 400
    ]);
});