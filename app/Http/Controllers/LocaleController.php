<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    /**
     * Supported locales.
     */
    protected array $supportedLocales = ['ro', 'en', 'hu'];

    /**
     * Switch the application locale.
     */
    public function switch(Request $request, string $locale)
    {
        if (!in_array($locale, $this->supportedLocales)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Limba selectată nu este suportată.',
                ], 400);
            }
            
            return redirect()->back()->with('error', 'Limba selectată nu este suportată.');
        }

        // Store in session
        Session::put('locale', $locale);

        // Update user preference if authenticated
        if ($request->user()) {
            $request->user()->update(['locale' => $locale]);
        }

        // Set cookie for persistence
        $cookie = cookie('locale', $locale, 60 * 24 * 365); // 1 year

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'locale' => $locale,
                'message' => __('messages.language_changed'),
            ])->withCookie($cookie);
        }

        return redirect()->back()->withCookie($cookie);
    }

    /**
     * Get available locales.
     */
    public function available()
    {
        return response()->json([
            'success' => true,
            'current' => App::getLocale(),
            'locales' => [
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
            ],
        ]);
    }
}
