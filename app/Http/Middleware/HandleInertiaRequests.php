<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class HandleInertiaRequests extends Middleware
{

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'csrf_token' => csrf_token(),
            'locale' => fn () => App::getLocale(),
            'translations' => fn() => collect(File::files(lang_path(App::getLocale())))
                ->mapWithKeys(fn($file) => [
                    pathinfo($file, PATHINFO_FILENAME) => require $file->getPathname(),
                ]),
            "auth" => [
                "user" => fn() => $request->user()
                    ? $request->user()->only("id", "name", "email")
                    : null,
            ],
        ]);
    }
}
