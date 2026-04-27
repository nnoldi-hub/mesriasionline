<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'g-recaptcha-response' => 'required|captcha',
        ], [
            'g-recaptcha-response.required' => 'Te rugăm să completezi reCAPTCHA.',
            'g-recaptcha-response.captcha' => 'Verificarea reCAPTCHA a eșuat. Te rugăm să încerci din nou.',
        ]);

        // În viitor poți trimite email sau salva în baza de date
        // Pentru moment, doar returnăm cu success message
        
        return back()->with('success', 'Mesajul tău a fost trimis cu succes! Te vom contacta în curând.');
    }

    public function terms()
    {
        return view('pages.terms');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }
}
