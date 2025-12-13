<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function show()
    {
        return Inertia::render('Home', [
            'title' => 'Home'
        ]);
    }
}