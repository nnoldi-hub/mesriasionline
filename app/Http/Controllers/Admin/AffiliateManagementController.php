<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\AffiliateCommission;
use App\Models\AffiliatePayout;
use App\Models\AffiliateProgram;
use App\Services\AffiliateService;
use Illuminate\Http\Request;

class AffiliateManagementController extends Controller
{
    public function __construct(
        protected AffiliateService $affiliateService
    ) {}

    /**
     * Affiliate management dashboard.
     */
    public function index()
    {
        $stats = [
            'total_affiliates' => Affiliate::count(),
            'active_affiliates' => Affiliate::active()->count(),
            'pending_affiliates' => Affiliate::where('status', 'pending')->count(),
            'total_commissions' => AffiliateCommission::sum('commission_amount'),
            'pending_commissions' => AffiliateCommission::pending()->sum('commission_amount'),
            'pending_payouts' => AffiliatePayout::pending()->sum('amount'),
            'this_month_earnings' => AffiliateCommission::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('commission_amount'),
        ];

        $pendingAffiliates = Affiliate::with('user')
            ->where('status', 'pending')
            ->latest()
            ->limit(10)
            ->get();

        $pendingPayouts = AffiliatePayout::with('affiliate.user')
            ->pending()
            ->latest()
            ->limit(10)
            ->get();

        $topAffiliates = Affiliate::with('user')
            ->active()
            ->orderByDesc('total_earnings')
            ->limit(10)
            ->get();

        return view('admin.affiliates.index', compact(
            'stats',
            'pendingAffiliates',
            'pendingPayouts',
            'topAffiliates'
        ));
    }

    /**
     * List all affiliates.
     */
    public function affiliates(Request $request)
    {
        $query = Affiliate::with(['user', 'program']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('referral_code', 'like', "%{$search}%");
        }

        $affiliates = $query->latest()->paginate(20);

        return view('admin.affiliates.affiliates', compact('affiliates'));
    }

    /**
     * Show affiliate details.
     */
    public function showAffiliate(Affiliate $affiliate)
    {
        $affiliate->load(['user', 'program', 'referrals.referredUser', 'commissions', 'payouts']);
        
        $statistics = $this->affiliateService->getStatistics($affiliate);

        return view('admin.affiliates.show', compact('affiliate', 'statistics'));
    }

    /**
     * Approve an affiliate.
     */
    public function approveAffiliate(Affiliate $affiliate)
    {
        $this->affiliateService->approveAffiliate($affiliate);

        return back()->with('success', "Afiliatul {$affiliate->user->name} a fost aprobat.");
    }

    /**
     * Reject an affiliate.
     */
    public function rejectAffiliate(Request $request, Affiliate $affiliate)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $affiliate->update([
            'status' => 'rejected',
            'notes' => $validated['reason'] ?? 'Cerere respinsă de administrator.',
        ]);

        return back()->with('success', "Afiliatul {$affiliate->user->name} a fost respins.");
    }

    /**
     * Suspend an affiliate.
     */
    public function suspendAffiliate(Request $request, Affiliate $affiliate)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $affiliate->update([
            'status' => 'suspended',
            'notes' => $validated['reason'],
        ]);

        return back()->with('success', "Afiliatul {$affiliate->user->name} a fost suspendat.");
    }

    /**
     * List pending payouts.
     */
    public function payouts(Request $request)
    {
        $query = AffiliatePayout::with('affiliate.user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payouts = $query->latest('requested_at')->paginate(20);

        return view('admin.affiliates.payouts', compact('payouts'));
    }

    /**
     * Process a payout.
     */
    public function processPayout(AffiliatePayout $payout)
    {
        $payout->markAsProcessing(auth()->user());

        return back()->with('success', 'Plata este acum în procesare.');
    }

    /**
     * Complete a payout.
     */
    public function completePayout(Request $request, AffiliatePayout $payout)
    {
        $validated = $request->validate([
            'payment_reference' => 'nullable|string|max:255',
        ]);

        $payout->markAsCompleted($validated['payment_reference'] ?? null);

        return back()->with('success', 'Plata a fost finalizată cu succes.');
    }

    /**
     * Fail a payout.
     */
    public function failPayout(Request $request, AffiliatePayout $payout)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $payout->markAsFailed($validated['reason']);

        return back()->with('success', 'Plata a fost marcată ca eșuată.');
    }

    /**
     * List commissions.
     */
    public function commissions(Request $request)
    {
        $query = AffiliateCommission::with(['affiliate.user', 'referredUser']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('transaction_type', $request->type);
        }

        $commissions = $query->latest()->paginate(20);

        return view('admin.affiliates.commissions', compact('commissions'));
    }

    /**
     * Approve a commission.
     */
    public function approveCommission(AffiliateCommission $commission)
    {
        $commission->approve();

        return back()->with('success', 'Comisionul a fost aprobat.');
    }

    /**
     * Reject a commission.
     */
    public function rejectCommission(Request $request, AffiliateCommission $commission)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $commission->reject($validated['reason']);

        return back()->with('success', 'Comisionul a fost respins.');
    }

    /**
     * Manage affiliate programs.
     */
    public function programs()
    {
        $programs = AffiliateProgram::withCount('affiliates')->get();

        return view('admin.affiliates.programs', compact('programs'));
    }

    /**
     * Create a new program.
     */
    public function storeProgram(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:affiliate_programs',
            'description' => 'nullable|string',
            'commission_type' => 'required|in:percentage,fixed',
            'commission_value' => 'required|numeric|min:0',
            'min_payout' => 'required|numeric|min:0',
            'cookie_days' => 'required|integer|min:1|max:365',
            'is_active' => 'boolean',
        ]);

        AffiliateProgram::create($validated);

        return back()->with('success', 'Programul de afiliere a fost creat.');
    }

    /**
     * Update a program.
     */
    public function updateProgram(Request $request, AffiliateProgram $program)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'commission_type' => 'required|in:percentage,fixed',
            'commission_value' => 'required|numeric|min:0',
            'min_payout' => 'required|numeric|min:0',
            'cookie_days' => 'required|integer|min:1|max:365',
            'is_active' => 'boolean',
        ]);

        $program->update($validated);

        return back()->with('success', 'Programul de afiliere a fost actualizat.');
    }
}
