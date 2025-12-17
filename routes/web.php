<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'show']);
Route::get('/login', [LoginController::class, 'show']);
Route::get('/register', [RegisterController::class, 'show']);
Route::post("/register", [RegisterController::class, "store"])
    ->name("register.store");
