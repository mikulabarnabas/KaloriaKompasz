<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'show'])->name('home');


require __DIR__.'/auth.php';
require __DIR__.'/app.php';