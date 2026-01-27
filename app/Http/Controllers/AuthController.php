<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Locale;

class AuthController extends Controller
{
    public function showRegister()
    {
        return Inertia::render('register');
    }

    public function showLogin(Request $request)
    {
        App::setLocale('hu');
        return Inertia::render('login');
    }

    public function registerUser(RegisterUserRequest $request)
    {
        $data = $request->validated();

        $user = User::create($data);

        return redirect()->route('home');
    }

    public function loginUser(LoginUserRequest $request)
    {
        $data = $request->validated();

        if (
            Auth::attempt($data, $data)
        ) {
            $request->session()->regenerate();
            return redirect()->route('home');
        }



        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logoutUser(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
