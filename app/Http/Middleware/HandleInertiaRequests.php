<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class HandleInertiaRequests extends Middleware
{

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'csrf_token' => csrf_token(),
            'locale' => App::getLocale(),
            "auth" => [
                "user" => fn() => $request->user()
                    ? $request->user()->only("id", "name", "email")
                    : null,
            ],
        ]);
    }
}
