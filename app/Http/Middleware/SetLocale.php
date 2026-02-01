<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supported = config('locales.supported', ['ja', 'en']);
        $fallback  = config('locales.fallback', config('app.fallback_locale', 'en'));

        $locale = session('locale')
            ?? $request->cookie('locale')
            ?? config('app.locale');

        if (! in_array($locale, $supported, true)) {
            $locale = $fallback;
        }

        // Laravel translation locale
        app()->setLocale($locale);

        // Carbon locale (month names, diffForHumans, translatedFormat)
        Carbon::setLocale($locale);

        return $next($request);
    }
}
