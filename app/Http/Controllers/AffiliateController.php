<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\AffiliateProgram;
use App\Services\AffiliateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AffiliateController extends Controller
{
    public function __construct(
        protected AffiliateService $affiliateService
    ) {}

    /**
     * Show affiliate dashboard or registration page.
     */
    public function index()
    {
        $user = Auth::user();
        $affiliate = Affiliate::where('user_id', $user->id)->first();

        if (!$affiliate) {
            // Show registration page
            $program = AffiliateProgram::getDefault();
            return view('affiliate.register', compact('program'));
        }

        // Show dashboard
        $statistics = $this->affiliateService->getStatistics($affiliate);
        $recentReferrals = $affiliate->referrals()
            ->with('referredUser')
            ->latest()
            ->limit(10)
            ->get();
        $recentCommissions = $affiliate->commissions()
            ->with('referredUser')
            ->latest()
            ->limit(10)
            ->get();
        $pendingPayouts = $affiliate->payouts()
            ->whereIn('status', ['pending', 'processing'])
            ->latest()
            ->get();

        return view('affiliate.dashboard', compact(
            'affiliate',
            'statistics',
            'recentReferrals',
            'recentCommissions',
            'pendingPayouts'
        ));
    }

    /**
     * Register as an affiliate.
     */
    public function register(Request $request)
    {
        $user = Auth::user();

        // Check if already an affiliate
        if (Affiliate::where('user_id', $user->id)->exists()) {
            return redirect()->route('affiliate.dashboard')
                ->with('info', 'Ești deja înregistrat ca afiliat.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:iban,paypal,revolut',
            'payment_details' => 'required|string|max:255',
            'terms_accepted' => 'required|accepted',
        ]);

        $affiliate = $this->affiliateService->createAffiliate($user);
        $affiliate->update([
            'payment_method' => $validated['payment_method'],
            'payment_details' => $validated['payment_details'],
        ]);

        return redirect()->route('affiliate.dashboard')
            ->with('success', 'Cererea ta de afiliere a fost trimisă! Vei fi notificat după aprobare.');
    }

    /**
     * Show referral links and marketing materials.
     */
    public function links()
    {
        $user = Auth::user();
        $affiliate = Affiliate::where('user_id', $user->id)->active()->firstOrFail();

        $links = [
            [
                'name' => 'Link principal',
                'url' => url('/?ref=' . $affiliate->referral_code),
                'description' => 'Link către pagina principală',
            ],
            [
                'name' => 'Link înregistrare',
                'url' => url('/register?ref=' . $affiliate->referral_code),
                'description' => 'Link direct către pagina de înregistrare',
            ],
            [
                'name' => 'Link meseriași',
                'url' => url('/meseriasi?ref=' . $affiliate->referral_code),
                'description' => 'Link către lista de meseriași',
            ],
        ];

        return view('affiliate.links', compact('affiliate', 'links'));
    }

    /**
     * Show earnings and commissions.
     */
    public function earnings(Request $request)
    {
        $user = Auth::user();
        $affiliate = Affiliate::where('user_id', $user->id)->active()->firstOrFail();

        $commissions = $affiliate->commissions()
            ->with('referredUser')
            ->latest()
            ->paginate(20);

        $summary = [
            'total' => $affiliate->total_earnings,
            'pending' => $affiliate->pending_earnings,
            'paid' => $affiliate->paid_earnings,
            'this_month' => $affiliate->commissions()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('commission_amount'),
        ];

        return view('affiliate.earnings', compact('affiliate', 'commissions', 'summary'));
    }

    /**
     * Show payouts history.
     */
    public function payouts()
    {
        $user = Auth::user();
        $affiliate = Affiliate::where('user_id', $user->id)->active()->firstOrFail();

        $payouts = $affiliate->payouts()->latest()->paginate(20);

        return view('affiliate.payouts', compact('affiliate', 'payouts'));
    }

    /**
     * Request a payout.
     */
    public function requestPayout(Request $request)
    {
        $user = Auth::user();
        $affiliate = Affiliate::where('user_id', $user->id)->active()->firstOrFail();

        if (!$affiliate->canRequestPayout()) {
            $minPayout = $affiliate->program?->min_payout ?? 100;
            return back()->with('error', "Suma minimă pentru retragere este de {$minPayout} lei. Ai acum {$affiliate->pending_earnings} lei în așteptare.");
        }

        $payout = $this->affiliateService->requestPayout($affiliate);

        if ($payout) {
            return back()->with('success', 'Cererea de plată a fost trimisă! Vei primi banii în 5-7 zile lucrătoare.');
        }

        return back()->with('error', 'A apărut o eroare. Te rugăm să încerci din nou.');
    }

    /**
     * Update payment settings.
     */
    public function updatePaymentSettings(Request $request)
    {
        $user = Auth::user();
        $affiliate = Affiliate::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'payment_method' => 'required|in:iban,paypal,revolut',
            'payment_details' => 'required|string|max:255',
        ]);

        $affiliate->update($validated);

        return back()->with('success', 'Setările de plată au fost actualizate.');
    }

    /**
     * Show referrals list.
     */
    public function referrals()
    {
        $user = Auth::user();
        $affiliate = Affiliate::where('user_id', $user->id)->active()->firstOrFail();

        $referrals = $affiliate->referrals()
            ->with('referredUser')
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => $affiliate->total_referrals,
            'registered' => $affiliate->referrals()->where('status', 'registered')->count(),
            'converted' => $affiliate->successful_referrals,
            'conversion_rate' => $affiliate->conversion_rate,
        ];

        return view('affiliate.referrals', compact('affiliate', 'referrals', 'stats'));
    }
}
