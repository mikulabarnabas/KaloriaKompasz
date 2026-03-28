<?php

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a profil oldal elérhető a bejelentkezett felhasználónak', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('profile.show'))
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page->component('profile'));
});

test('a profil adatok sikeresen menthetőek és frissülnek', function () {
    $user = User::factory()->create();
    
    $profileData = [
        'gender' => 'male',
        'date_of_birth' => '1990-01-01',
        'height_cm' => 180,
        'weight_kg' => 80,
        'activity_level' => 'moderate',
        'weight_goal' => 'maintain',
    ];

    $this->actingAs($user)
        ->post(route('profile.save'), $profileData)
        ->assertRedirect();

    $this->assertDatabaseHas('user_profiles', [
        'user_id' => $user->id,
        'weight_kg' => 80,
        'gender' => 'male',
        'height_cm' => 180
    ]);
});

test('az observer automatikusan kiszámolja a kalóriákat mentéskor', function () {
    $user = User::factory()->create();
    
    $this->actingAs($user)->post(route('profile.save'), [
        'gender' => 'male',
        'date_of_birth' => '1990-01-01',
        'height_cm' => 180,
        'weight_kg' => 80,
        'activity_level' => 'moderate',
        'weight_goal' => 'maintain',
    ]);

    $profile = UserProfile::where('user_id', $user->id)->first();

    expect($profile)->not->toBeNull();
    expect($profile->calories_per_day)->toBeGreaterThan(2000);
    expect($profile->protein_per_day)->toBeGreaterThan(0);
});

test('fogyás cél esetén a kalória keret alacsonyabb', function () {
    $user = User::factory()->create();
    
    $commonData = [
        'gender' => 'female',
        'date_of_birth' => '1995-05-05',
        'height_cm' => 170,
        'weight_kg' => 70,
        'activity_level' => 'light',
    ];

    $this->actingAs($user)->post(route('profile.save'), array_merge($commonData, [
        'weight_goal' => 'maintain'
    ]));
    $maintainCalories = UserProfile::where('user_id', $user->id)->first()->calories_per_day;

    $this->actingAs($user)->post(route('profile.save'), array_merge($commonData, [
        'weight_goal' => 'lose',
        'target_weight_kg' => 65,
        'goal_period_weeks' => 10,
    ]));
    
    $loseCalories = UserProfile::where('user_id', $user->id)->first()->refresh()->calories_per_day;

    expect($loseCalories)->toBeLessThan($maintainCalories);
});

test('érvénytelen adatok esetén a validáció elbukik', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson(route('profile.save'), [
            'weight_kg' => 'nagyon-sok',
            'gender' => 'invalid-gender',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['weight_kg', 'gender']);
});