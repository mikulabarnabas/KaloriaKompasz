<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'registerUser'])->middleware(HandlePrecognitiveRequests::class);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'loginUser'])->middleware(HandlePrecognitiveRequests::class);

    Route::get('/auth/google/redirect', [AuthController::class, 'googleRedirect'])->name('auth.google.redirect');
    Route::match(['get', 'post'], '/auth/google/callback', [AuthController::class, 'googleCallback'])->name('auth.google.callback');

    Route::get('/forgot-password', fn() => Inertia::render('forgot-password'))->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', fn(Request $request, $token) => Inertia::render('reset-password', ['token' => $token, 'email' => $request->query('email')]))->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logoutUser'])->middleware('auth')->name('logout');
