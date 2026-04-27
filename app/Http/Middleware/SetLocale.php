<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported locales.
     */
    protected array $supportedLocales = ['ro', 'en', 'hu'];

    /**
     * Default locale.
     */
    protected string $defaultLocale = 'ro';

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->determineLocale($request);
        
        App::setLocale($locale);
        Session::put('locale', $locale);

        // Share locale with views
        view()->share('currentLocale', $locale);
        view()->share('supportedLocales', $this->getSupportedLocalesWithNames());

        return $next($request);
    }

    /**
     * Determine the locale for the request.
     */
    protected function determineLocale(Request $request): string
    {
        // 1. Check URL prefix (highest priority)
        $urlLocale = $request->segment(1);
        if (in_array($urlLocale, $this->supportedLocales)) {
            return $urlLocale;
        }

        // 2. Check query parameter
        if ($request->has('lang') && in_array($request->query('lang'), $this->supportedLocales)) {
            return $request->query('lang');
        }

        // 3. Check session
        if (Session::has('locale') && in_array(Session::get('locale'), $this->supportedLocales)) {
            return Session::get('locale');
        }

        // 4. Check user preference (if authenticated)
        if ($request->user() && $request->user()->locale && in_array($request->user()->locale, $this->supportedLocales)) {
            return $request->user()->locale;
        }

        // 5. Check browser preference
        $browserLocale = $request->getPreferredLanguage($this->supportedLocales);
        if ($browserLocale && in_array($browserLocale, $this->supportedLocales)) {
            return $browserLocale;
        }

        // 6. Default locale
        return $this->defaultLocale;
    }

    /**
     * Get supported locales with their display names.
     */
    protected function getSupportedLocalesWithNames(): array
    {
        return [
            'ro' => [
                'code' => 'ro',
                'name' => 'Română',
                'native' => 'Română',
                'flag' => '🇷🇴',
            ],
            'en' => [
                'code' => 'en',
                'name' => 'English',
                'native' => 'English',
                'flag' => '🇬🇧',
            ],
            'hu' => [
                'code' => 'hu',
                'name' => 'Hungarian',
                'native' => 'Magyar',
                'flag' => '🇭🇺',
            ],
        ];
    }
}
