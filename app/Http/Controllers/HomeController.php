<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use function Pest\Laravel\session;

class HomeController extends Controller
{
    public function show(Request $request)
    {
        return Inertia::render('home');
    }
}