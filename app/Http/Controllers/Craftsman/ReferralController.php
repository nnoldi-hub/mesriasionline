<?php

namespace App\Http\Controllers\Craftsman;

use App\Http\Controllers\Controller;
use App\Models\CraftsmanLead;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    /**
     * Pagina "Recomandă un coleg" — link personal + formular manual + istoric.
     */
    public function index()
    {
        $craftsman = auth()->user();

        $referralLink = url('/inscriere-meserias?ref=' . $craftsman->slug);

        $referrals = $craftsman->referredLeads()->latest()->get();

        $stats = [
            'total'      => $referrals->count(),
            'inregistrat' => $referrals->where('status', 'inregistrat')->count(),
        ];

        return view('craftsman.referrals.index', compact('referralLink', 'referrals', 'stats'));
    }

    /**
     * Adăugare manuală a unui coleg recomandat.
     */
    public function store(Request $request)
    {
        $craftsman = auth()->user();

        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'city'  => 'required|string|max:100',
            'trade' => 'required|in:electrician,instalator,tamplar,zugrav,mecanic',
        ]);

        CraftsmanLead::create([
            'name'                => $validated['name'],
            'phone'               => $validated['phone'],
            'city'                => $validated['city'],
            'trade'               => $validated['trade'],
            'experience_range'    => '0-2',
            'status'              => 'nou',
            'utm_source'          => 'craftsman_referral',
            'referred_by_user_id' => $craftsman->id,
        ]);

        return back()->with('success', 'Mulțumim! Colegul tău a fost adăugat și îl vom contacta în curând.');
    }
}
