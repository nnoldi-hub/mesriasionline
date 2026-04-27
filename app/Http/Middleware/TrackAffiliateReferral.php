<?php

namespace App\Http\Middleware;

use App\Services\AffiliateService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackAffiliateReferral
{
    public function __construct(
        protected AffiliateService $affiliateService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for referral code in URL
        $referralCode = $request->query('ref');

        if ($referralCode) {
            // Track the click
            $this->affiliateService->trackClick($request, $referralCode);
        }

        return $next($request);
    }
}
