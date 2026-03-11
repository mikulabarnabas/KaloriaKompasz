<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WorkoutController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\StatisticsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;

Route::get('/', [HomeController::class, 'show'])->name('home');

Route::get('/register', [AuthController::class, 'showRegister'])->middleware([HandlePrecognitiveRequests::class])->name('register');
Route::get('/login', [AuthController::class, 'showLogin'])->middleware([HandlePrecognitiveRequests::class])->name('login');

Route::post('/register', [AuthController::class, 'registerUser'])->middleware([HandlePrecognitiveRequests::class]);
Route::post('/login', [AuthController::class, 'loginUser'])->middleware([HandlePrecognitiveRequests::class]);

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/logout', [AuthController::class, 'logoutUser']);
    Route::post('/profile-save', [ProfileController::class, 'save'])->middleware([HandlePrecognitiveRequests::class]);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/fdiary', [FoodController::class, 'show']);
    Route::get('/fdiary/diary/{date}', [FoodController::class, 'getDiaryByDate'])->where('date', '\d{4}-\d{2}-\d{2}');
    Route::get('/fdiary/getFoods/{searchTerm}/{page}', [FoodController::class, 'getFoods']);
    Route::get('/fdiary/getPageCount/{searchTerm}', [FoodController::class, 'getPageCount']);
    Route::post('/fdiary/entry', [FoodController::class, 'addEntry']);
    Route::delete('/fdiary/entry/{date}/{id}', [FoodController::class, 'deleteEntry']);
    Route::post('/fdiary/create', [FoodController::class, 'storeFood'])->middleware([HandlePrecognitiveRequests::class]);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/wdiary', [WorkoutController::class, 'show']);
    Route::post('/wdiary/create', [WorkoutController::class, 'storeExercise'])->middleware([HandlePrecognitiveRequests::class]);
    Route::get('/wdiary/diary/{date}', [WorkoutController::class, 'getDiaryByDate']);
    Route::post('/wdiary/entry', [WorkoutController::class, 'addEntry']);
    Route::delete('/wdiary/entry/{date}/{id}', [WorkoutController::class, 'deleteEntry']);
    Route::get('/wdiary/getExercises/{search}/{page}', [WorkoutController::class, 'getExercises']);
    Route::get('/wdiary/getPageCount/{search}', [WorkoutController::class, 'getPageCount']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/stats', [StatisticsController::class, 'index']);
});


Route::patch('/lang/{lang}', function ($lang) {
    session(['locale' => $lang]);
});

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

Route::get('/auth/google/redirect', function () {
    return Socialite::driver('google')->redirect();
})->name('auth.google.redirect');

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->stateless()->user();

    $user = User::updateOrCreate(
        ['email' => $googleUser->email],
        [
            'name' => $googleUser->name,
            'google_id' => $googleUser->id, 
            'google_token' => $googleUser->token,
            'google_refresh_token' => $googleUser->refreshToken,
        ]
    );

    Auth::login($user);

    return redirect('/');
})->name('auth.google.callback');

use Inertia\Inertia;

Route::get('/forgot-password', function () {
    return Inertia::render('forgot-password');
})->middleware('guest')->name('password.request');

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::ResetLinkSent
        ? back()->with(['status' => __($status)])
        : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function (string $token) {
    return Inertia::render('reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        }
    );

    return $status === Password::PasswordReset
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.update');
