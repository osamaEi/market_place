<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;


class SetLocale
{
    protected $supportedLocales = ['en', 'fr', 'es', 'ar', 'de', 'tr', 'it', 'ja', 'zh', 'ur'];

    public function handle($request, Closure $next)
    {
        $locale = config('app.fallback_locale', 'en');

        if ($request->is('api/*')) {
            if ($request->hasHeader('Accept-Language')) {
                $locale = $this->parseLocale($request->header('Accept-Language'));
            }
        } else {
            if (Session::has('locale')) {
                $locale = Session::get('locale');
            } elseif ($request->hasHeader('Accept-Language')) {
                $locale = $this->parseLocale($request->header('Accept-Language'));
                Session::put('locale', $locale);
            }
        }

        if (!in_array($locale, $this->supportedLocales)) {
            $locale = config('app.fallback_locale', 'en');
        }

        // Add debug logging
        Log::info('Locale Debug', [
            'requested_locale' => $request->header('Accept-Language'),
            'parsed_locale' => $locale,
            'is_api' => $request->is('api/*'),
            'translation_file_exists' => file_exists(resource_path("lang/{$locale}.json")),
        ]);

        App::setLocale($locale); 
         
        return $next($request);
    }

    protected function parseLocale($header)
    {
        $locale = substr($header, 0, 2);
        return in_array($locale, $this->supportedLocales) 
            ? $locale 
            : config('app.fallback_locale', 'en');
    }
}