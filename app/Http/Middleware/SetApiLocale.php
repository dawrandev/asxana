<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetApiLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales  = array_keys(config('app.locales'));
        $defaultLocale    = config('app.fallback_locale', 'qq');

        $locale = $request->header('Accept-Language');

        if (!$locale) {
            $locale = $request->getPreferredLanguage($supportedLocales);
        }

        if (!$locale || !in_array($locale, $supportedLocales)) {
            $locale = $defaultLocale;
        }

        App::setLocale($locale);

        return $next($request);
    }
}
