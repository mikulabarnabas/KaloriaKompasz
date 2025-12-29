<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExcersiseController;
use App\Http\Controllers\FoodController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;

Route::get('/', [HomeController::class, 'show'])->name('home');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister']);
Route::get('/fdiary', [FoodController::class, 'show']);
Route::get('/wdiary', [ExcersiseController::class, 'show']);
Route::get('/profile', [ProfileController::class, 'show']);

Route::post('/register', [AuthController::class, 'registerUser'])->middleware([HandlePrecognitiveRequests::class]);
Route::post('/login', [AuthController::class, 'loginUser'])->middleware([HandlePrecognitiveRequests::class]);
Route::post('/logout', [AuthController::class, 'logoutUser']);
Route::post('/profile-save', [ProfileController::class, 'save'])->middleware([HandlePrecognitiveRequests::class]);
Route::post('/fdiary/create', [FoodController::class, 'create'])->middleware([HandlePrecognitiveRequests::class]);
