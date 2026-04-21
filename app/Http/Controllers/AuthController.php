<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;
use Google_Client;


use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{
    public function showRegister()
    {
        return Inertia::render('register');
    }

    public function showLogin(Request $request)
    {
        App::setLocale('en');
        return Inertia::render('login', [
            'locale' => App::getLocale(),
        ]);
    }

    public function registerUser(RegisterUserRequest $request)
    {
        $data = $request->validated();

        $user = User::create($data);

        return response()->json(['success' => true]);
    }

    public function loginUser(LoginUserRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return response()->json(['success' => true]);
        }

        throw ValidationException::withMessages([
            'password' => [trans('validation.current_password')],
        ]);
    }

    public function googleRedirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function googleCallback(Request $request)
    {

        try {
            if ($request->has('token')) {

                $client = new Google_Client(['client_id' => '10740457262-47dacbavcs5blgon888e89us8tcp5504.apps.googleusercontent.com']);
                $payload = $client->verifyIdToken($request->token);

                if (!$payload) {
                    Log::error('Google Token validáció sikertelen: érvénytelen payload');
                    return response()->json(['error' => 'Invalid token'], 401);
                }


                $userData = [
                    'email' => $payload['email'],
                    'name' => $payload['name'] ?? ($payload['given_name'] . ' ' . $payload['family_name']),
                    'google_id' => $payload['sub'],
                ];
            } else {
                $googleUser = Socialite::driver('google')->stateless()->user();

                $userData = [
                    'email' => $googleUser->email,
                    'name' => $googleUser->name,
                    'google_id' => $googleUser->id,
                    'google_token' => $googleUser->token,
                ];
            }

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'google_id' => $userData['google_id'],
                    'google_token' => $userData['google_token'] ?? null,
                ]
            );

            Auth::login($user, true);

            if ($request->expectsJson() || $request->has('token')) {
                return response()->json([
                    'success' => true,
                    'user' => $user
                ]);
            }

            return redirect('/');

        } catch (\Exception $e) {
            Log::error('Hiba a Google bejelentkezés során', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|letters|mixedCase|numbers|symbols|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function logoutUser(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
