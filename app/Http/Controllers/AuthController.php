<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Requests\StorePostRequest;

class AuthController extends Controller
{
    public function showRegister() {
        return Inertia::render('register');
    }

    public function showLogin() {
        return Inertia::render('login');
    }

    public function store(StorePostRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('home');
    }
}
