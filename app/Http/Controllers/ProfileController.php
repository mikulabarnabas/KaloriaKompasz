<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use App\Http\Requests\ProfileRequest;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function save(ProfileRequest $request)
    {
        $data = $request->validated();

        $user = $request->user();

        $profile = $user->profile
            ?: UserProfile::firstOrCreate(['user_id' => $user->id]);

        $profile->fill($data)->save();

        return back()->with('success', 'Profile updated.');
    }

    public function show()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        return Inertia::render('profile');
    }
}