<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WorkoutController;
use App\Http\Controllers\FoodController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;

Route::get('/', [HomeController::class, 'show'])->name('home');

Route::get('/register', [AuthController::class, 'showRegister'])->middleware([HandlePrecognitiveRequests::class])->name('login');
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
    Route::post('/fdiary/entry', [FoodController::class, 'addEntry']);
    Route::delete('/fdiary/entry', [FoodController::class, 'deleteEntry']);
    Route::post('/fdiary/create', [FoodController::class, 'storeFood'])->middleware([HandlePrecognitiveRequests::class]);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/wdiary', [WorkoutController::class, 'show']);
    Route::post('/wdiary/create', [WorkoutController::class, 'store'])->middleware([HandlePrecognitiveRequests::class]);
    Route::get('/wdiary/diary', [WorkoutController::class, 'getDiaryByDate']);
    Route::post('/wdiary/entry', [WorkoutController::class, 'addEntry']);
    Route::delete('/wdiary/entry', [WorkoutController::class, 'deleteEntry']);
});

