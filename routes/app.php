<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorkoutController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\StatisticsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;

Route::middleware(['auth', 'verified'])->group(function () {

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::post('/save', [ProfileController::class, 'save'])
            ->middleware(HandlePrecognitiveRequests::class)
            ->name('profile.save');
    });

    Route::controller(FoodController::class)->prefix('fdiary')->name('food.')->group(function () {
        Route::get('/', 'show')->name('index');
        Route::get('/diary/{date}', 'getDiaryByDate')->where('date', '\d{4}-\d{2}-\d{2}');
        Route::get('/getFoods/{searchTerm}/{page}', 'getFoods');
        Route::get('/getPageCount/{searchTerm}', 'getPageCount');
        Route::post('/entry', 'addEntry');
        Route::delete('/entry/{date}/{id}', 'deleteEntry');
        Route::post('/create', 'storeFood')->middleware(HandlePrecognitiveRequests::class);
    });

    Route::controller(WorkoutController::class)->prefix('wdiary')->name('workout.')->group(function () {
        Route::get('/', 'show')->name('index');
        Route::get('/diary/{date}', 'getDiaryByDate');
        Route::get('/getExercises/{search}/{page}', 'getExercises');
        Route::get('/getPageCount/{search}', 'getPageCount');
        Route::post('/entry', 'addEntry');
        Route::post('/create', 'storeExercise')->middleware(HandlePrecognitiveRequests::class);
        Route::delete('/entry/{date}/{id}', 'deleteEntry');
        Route::post('/sync-steps', 'syncSteps');
    });

    Route::controller(FoodController::class)->prefix('stats')->name('stat.')->group(function () {
        Route::get('/', [StatisticsController::class, 'index'])->name('stats.index');
        Route::get('/getData/{date}', [StatisticsController::class, 'getData']);
        Route::get('/weekly/{date}', [StatisticsController::class, 'getWeeklyStats']);
    });
});
