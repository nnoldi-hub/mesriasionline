<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SuspiciousActivityDetector;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $detector = new SuspiciousActivityDetector($request);
        
        // Check if IP is blocked
        if ($detector->isBlocked()) {
            return redirect()->back()->withErrors([
                'email' => 'Accesul tău a fost blocat temporar din cauza activității suspecte.',
            ]);
        }

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'g-recaptcha-response' => 'required|captcha',
        ], [
            'g-recaptcha-response.required' => 'Te rugăm să completezi reCAPTCHA.',
            'g-recaptcha-response.captcha' => 'Verificarea reCAPTCHA a eșuat. Te rugăm să încerci din nou.',
        ]);

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            // Clear failed login attempts
            $detector->clearFailedAttempts($credentials['email']);
            
            // Check for unusual location or user agent changes
            $detector->checkUnusualLocation($user);
            $detector->checkUserAgentChange($user);

            if (in_array($user->role, ['admin', 'superadmin'])) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->role === 'specialist') {
                return redirect()->intended(route('craftsman.dashboard'));
            } elseif ($user->role === 'client') {
                return redirect()->intended(route('client.dashboard'));
            }

            return redirect()->route('home');
        }

        // Log failed login attempt
        if ($detector->checkFailedLogin($credentials['email'])) {
            return redirect()->back()->withErrors([
                'email' => 'Prea multe încercări eșuate. Accesul tău a fost blocat temporar.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'Datele de autentificare sunt incorecte.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
