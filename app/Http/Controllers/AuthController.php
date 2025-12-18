<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginUserRequest;

class AuthController extends Controller
{
    public function showRegister()
    {
        return Inertia::render('register');
    }

    public function showLogin()
    {
        return Inertia::render('login');
    }

    public function registerUser(RegisterUserRequest $request)
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('home');
    }

    /**
     * Handle an authentication attempt.
     */
    public function loginUser(LoginUserRequest $request)
    {
        $credentials = $request->validated();

        if (
            Auth::attempt([
                'email' => $credentials['email'],
                'password' => $credentials['password']

            ], $credentials['rememberme'])
        ) {
            $request->session()->regenerate();
            return redirect()->route('home');
        }


        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
}
