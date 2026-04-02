<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the registration page is accessible', function () {
    $this->get(route('login'))
        ->assertStatus(200)
        ->assertInertia(fn($page) => $page->component('login'));
});

test('the login page is accessible and the language is English', function () {
    $this->get(route('login'))
        ->assertStatus(200)
        ->assertInertia(
            fn($page) => $page
                ->component('login')
                ->has('locale')
                ->where('locale', 'en')
        );
});

test('a new user can register successfully', function () {
    $jelszo = 'Secret123?';
    $email = 'regisztracio@teszt.hu';

    $user = User::factory()->make([
        'email' => $email,
        'password' => Hash::make($jelszo),
    ]);

    $userData = [
        'name' => $user->name,
        'email' => $user->email,
        'password' => $jelszo,
        'password_confirmation' => $jelszo,
        'acceptTerms' => true
    ];

    $this->postJson(action([App\Http\Controllers\AuthController::class, 'registerUser']), $userData)
        ->assertOk()
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('users', [
        'email' => $user->email,
    ]);
});

test('a user can log in successfully', function () {
    $jelszo = 'Secret123?';
    $email = 'bejelentkezes@teszt.hu';

    $user = User::factory()->create([
        'email' => $email,
        'password' => Hash::make($jelszo),
    ]);

    $this->postJson(action([App\Http\Controllers\AuthController::class, 'loginUser']), [
        'email' => $email,
        'password' => $jelszo,
    ])
        ->assertOk()
        ->assertJson(['success' => true]);

    $this->assertAuthenticatedAs($user);
});

test('cannot log in with an incorrect password', function () {
    $user = User::factory()->create();

    $this->postJson(action([App\Http\Controllers\AuthController::class, 'loginUser']), [
        'email' => $user->email,
        'password' => 'rossz-jelszo',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['password']);

    $this->assertGuest();
});

test('an authenticated user can log out', function () {
    $user = User::factory()->create();

    auth()->login($user);

    $this->assertAuthenticated();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('home'));

    $this->assertGuest();
});

test('google callback creates the user and logs them in', function () {
    $googleUser = Mockery::mock('Laravel\Socialite\Two\User');
    $kamuAdatok = User::factory()->make();

    $googleUser->id = '12345';
    $googleUser->name = $kamuAdatok->name;
    $googleUser->email = $kamuAdatok->email;
    $googleUser->token = 'fake-token';
    $googleUser->refreshToken = 'fake-refresh-token';

    $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $provider->shouldReceive('stateless')->andReturnSelf();
    $provider->shouldReceive('user')->andReturn($googleUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $this->get('/auth/google/callback')
        ->assertRedirect('/');

    $this->assertDatabaseHas('users', [
        'email' => $kamuAdatok->email,
        'name' => $kamuAdatok->name,
        'google_id' => '12345'
    ]);

    $this->assertAuthenticated();
});
