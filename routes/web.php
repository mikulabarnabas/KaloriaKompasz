<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;

Route::get('/', [HomeController::class, 'show'])->name('home');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'registerUser'])->middleware([HandlePrecognitiveRequests::class]);
Route::post('/login', [AuthController::class, 'loginUser'])->middleware([HandlePrecognitiveRequests::class]);
Route::post('/logout', [AuthController::class, 'logoutUser']);
