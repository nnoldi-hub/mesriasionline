<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AffiliateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientRegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register-client');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'g-recaptcha-response' => 'required|captcha',
        ], [
            'g-recaptcha-response.required' => 'Te rugăm să completezi reCAPTCHA.',
            'g-recaptcha-response.captcha' => 'Verificarea reCAPTCHA a eșuat. Te rugăm să încerci din nou.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'phone' => $validated['phone'] ?? null,
            'role' => 'client',
            'is_active' => true,
        ]);

        // Attribute registration to affiliate if referral cookie exists
        app(AffiliateService::class)->attributeRegistration($user);

        // Auto-login after registration
        auth()->login($user);

        return redirect()->route('home')
            ->with('success', 'Contul tău a fost creat cu succes! Acum poți căuta și contacta meșteri.');
    }
}
