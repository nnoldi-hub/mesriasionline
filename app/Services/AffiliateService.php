?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\AffiliateCommission;
use App\Models\AffiliatePayout;
use App\Models\AffiliateProgram;
use App\Models\Referral;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class AffiliateService
{
    protected string $cookieName = 'omulpotrivit_ref';
    protected int $defaultCookieDays = 30;

    /**
     * Track a referral click.
     */
    public function trackClick(Request $request, string $referralCode): ?Referral
    {
        $affiliate = Affiliate::where('referral_code', $referralCode)
            ->active()
            ->first();

        if (!$affiliate) {
            return null;
        }

        // Get cookie duration from program or use default
        $cookieDays = $affiliate->program?->cookie_days ?? $this->defaultCookieDays;

        // Create referral record
        $referral = Referral::create([
            'affiliate_id' => $affiliate->id,
            'referral_code' => $referralCode,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'landing_page' => $request->fullUrl(),
            'referrer_url' => $request->header('referer'),
            'status' => 'clicked',
            'clicked_at' => now(),
            'expires_at' => now()->addDays($cookieDays),
        ]);

        // Record click for affiliate stats
        $affiliate->recordClick();

        // Set cookie
        Cookie::queue($this->cookieName, $referralCode, $cookieDays * 24 * 60);

        return $referral;
    }

    /**
     * Get referral code from cookie or request.
     */
    public function getReferralCode(Request $request): ?string
    {
        // Check query parameter first
        $code = $request->query('ref');
        
        if (!$code) {
            // Check cookie
            $code = $request->cookie($this->cookieName);
        }

        return $code;
    }

    /**
     * Attribute a registration to an affiliate.
     */
    public function attributeRegistration(User $user, ?string $referralCode = null): ?Referral
    {
        if (!$referralCode) {
            $referralCode = request()->cookie($this->cookieName);
        }

        if (!$referralCode) {
            return null;
        }

        $affiliate = Affiliate::where('referral_code', $referralCode)
            ->active()
            ->first();

        if (!$affiliate) {
            return null;
        }

        // Don't allow self-referral
        if ($affiliate->user_id === $user->id) {
            return null;
        }

        // Find existing referral or create new one
        $referral = Referral::where('referral_code', $referralCode)
            ->where('ip_address', request()->ip())
            ->whereIn('status', ['clicked', 'registered'])
            ->where('expires_at', '>', now())
            ->first();

        if (!$referral) {
            $referral = Referral::create([
                'affiliate_id' => $affiliate->id,
                'referral_code' => $referralCode,
                'ip_address' => request()->ip(),
                'status' => 'registered',
                'clicked_at' => now(),
                'registered_at' => now(),
                'expires_at' => now()->addDays($affiliate->program?->cookie_days ?? $this->defaultCookieDays),
            ]);
        }

        // Mark referral as registered
        $referral->markAsRegistered($user);

        // Update user with referral info
        $user->update([
            'referred_by_code' => $referralCode,
            'referred_by_affiliate_id' => $affiliate->id,
        ]);

        // Create commission for registration (if program awards for registrations)
        $this->createCommission($affiliate, $referral, 'registration', 0, $user);

        Log::info('Affiliate registration attributed', [
            'user_id' => $user->id,
            'affiliate_id' => $affiliate->id,
            'referral_code' => $referralCode,
        ]);

        return $referral;
    }

    /**
     * Convert a referral (user completed a valuable action).
     */
    public function convertReferral(User $user, string $transactionType, float $transactionAmount = 0, ?string $transactionId = null): ?AffiliateCommission
    {
        if (!$user->referred_by_affiliate_id) {
            return null;
        }

        $affiliate = Affiliate::find($user->referred_by_affiliate_id);
        if (!$affiliate || !$affiliate->isActive()) {
            return null;
        }

        // Find the referral
        $referral = Referral::where('affiliate_id', $affiliate->id)
            ->where('referred_user_id', $user->id)
            ->first();

        // Mark referral as converted if not already
        if ($referral && $referral->status !== 'converted') {
            $referral->markAsConverted();
        }

        // Mark user as converted
        if (!$user->referral_converted_at) {
            $user->update(['referral_converted_at' => now()]);
        }

        // Create commission
        return $this->createCommission($affiliate, $referral, $transactionType, $transactionAmount, $user, $transactionId);
    }

    /**
     * Create a commission for the affiliate.
     */
    protected function createCommission(
        Affiliate $affiliate,
        ?Referral $referral,
        string $transactionType,
        float $transactionAmount,
        User $referredUser,
        ?string $transactionId = null
    ): AffiliateCommission {
        $program = $affiliate->program ?? AffiliateProgram::getDefault();
        
        $commissionAmount = 0;
        $commissionRate = 0;

        if ($program) {
            $commissionAmount = $program->calculateCommission($transactionAmount);
            $commissionRate = $program->commission_type === 'percentage' 
                ? $program->commission_value 
                : 0;
        }

        // For registrations without transaction, use fixed bonus if defined
        if ($transactionType === 'registration' && $transactionAmount === 0) {
            $commissionAmount = $program->rules['registration_bonus'] ?? 5.00;
        }

        $commission = AffiliateCommission::create([
            'affiliate_id' => $affiliate->id,
            'referral_id' => $referral?->id,
            'referred_user_id' => $referredUser->id,
            'transaction_type' => $transactionType,
            'transaction_id' => $transactionId,
            'transaction_amount' => $transactionAmount,
            'commission_rate' => $commissionRate,
            'commission_amount' => $commissionAmount,
            'status' => 'pending',
        ]);

        // Add to affiliate's pending earnings
        $affiliate->addEarnings($commissionAmount);

        Log::info('Affiliate commission created', [
            'affiliate_id' => $affiliate->id,
            'commission_id' => $commission->id,
            'amount' => $commissionAmount,
            'type' => $transactionType,
        ]);

        return $commission;
    }

    /**
     * Request a payout.
     */
    public function requestPayout(Affiliate $affiliate): ?AffiliatePayout
    {
        if (!$affiliate->canRequestPayout()) {
            return null;
        }

        $payout = AffiliatePayout::create([
            'affiliate_id' => $affiliate->id,
            'amount' => $affiliate->pending_earnings,
            'payment_method' => $affiliate->payment_method,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        Log::info('Affiliate payout requested', [
            'affiliate_id' => $affiliate->id,
            'payout_id' => $payout->id,
            'amount' => $payout->amount,
        ]);

        return $payout;
    }

    /**
     * Become an affiliate.
     */
    public function createAffiliate(User $user, ?AffiliateProgram $program = null): Affiliate
    {
        $program = $program ?? AffiliateProgram::getDefault();

        return Affiliate::create([
            'user_id' => $user->id,
            'program_id' => $program?->id,
            'status' => 'pending', // Requires admin approval
        ]);
    }

    /**
     * Approve an affiliate.
     */
    public function approveAffiliate(Affiliate $affiliate): void
    {
        $affiliate->update([
            'status' => 'active',
            'approved_at' => now(),
        ]);

        // TODO: Send approval notification email
    }

    /**
     * Get affiliate statistics.
     */
    public function getStatistics(Affiliate $affiliate, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $from = $from ?? now()->subDays(30);
        $to = $to ?? now();

        $referrals = $affiliate->referrals()
            ->whereBetween('created_at', [$from, $to]);

        $commissions = $affiliate->commissions()
            ->whereBetween('created_at', [$from, $to]);

        return [
            'total_clicks' => $referrals->count(),
            'registrations' => $referrals->where('status', 'registered')->count(),
            'conversions' => $referrals->where('status', 'converted')->count(),
            'earnings' => [
                'total' => $affiliate->total_earnings,
                'pending' => $affiliate->pending_earnings,
                'paid' => $affiliate->paid_earnings,
                'period' => $commissions->sum('commission_amount'),
            ],
            'conversion_rate' => $affiliate->conversion_rate,
            'top_landing_pages' => $referrals
                ->selectRaw('landing_page, count(*) as count')
                ->groupBy('landing_page')
                ->orderByDesc('count')
                ->limit(5)
                ->pluck('count', 'landing_page')
                ->toArray(),
        ];
    }

    /**
     * Get cookie name.
     */
    public function getCookieName(): string
    {
        return $this->cookieName;
    }
}
