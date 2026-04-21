<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // Kiolvassuk a 'locale' sütit, amit a JS beállított
        $locale = $request->cookie('locale');
        Log::info("Locale cookie value: " . ($locale ?? 'not set'));

        // Ellenőrizzük, hogy létezik-e és támogatott-e
        if (!$locale || !in_array($locale, ['hu', 'en'])) {
            $locale = config('app.locale'); // Alapértelmezett (hu)
        }

        // Élesítjük a nyelvet a Laravelben
        App::setLocale($locale);

        return $next($request);
    }
}