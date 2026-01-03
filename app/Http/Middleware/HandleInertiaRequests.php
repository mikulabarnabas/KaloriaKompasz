<?php

namespace App\Http\Middleware;

use Closure;
use Inertia\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleInertiaRequests extends Middleware
{

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            "testShared" => "ok",
            "auth" => [
                "user" => fn() => $request->user()
                    ? $request->user()->only("id", "name", "email")
                    : null,
            ],
        ]);
    }
}
