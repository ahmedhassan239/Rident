<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $lang = $request->header('lang', 'en'); // Default to English if no header is provided

        // Validate the lang parameter to be either 'en' or 'ar'
        if (!in_array($lang, ['en', 'ar'])) {
            return response()->json(['error' => 'Unsupported language.'], 400);
        }

        app()->setLocale($lang);

        return $next($request);
    }
}
