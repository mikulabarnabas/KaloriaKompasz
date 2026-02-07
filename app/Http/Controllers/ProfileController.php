<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use App\Http\Requests\ProfileRequest;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $profile = $request->user()->profile;

        return Inertia::render('profile', [
            'profile' => $profile
        ]);
    }

    public function save(ProfileRequest $request)
    {
        $data = $request->validated();
        $userId = $request->user()->id;

        $profile = UserProfile::updateOrCreate(
            ['user_id' => $userId],
            $data
        );

        $profile->refresh();

        return back()->with('success', 'Profile updated.');
    }
}
