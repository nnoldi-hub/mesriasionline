<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanLimits
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        $subscription = $user->activeSubscription();

        // If user has an active subscription, reset monthly counter if in a new month
        if ($subscription) {
            $subscription->resetQuotesIfNewMonth();
            $subscription->refresh();
        }

        // Check if the user can send a quote
        if (!$user->isPro()) {
            $quotesUsed   = $subscription?->quotes_used_this_month ?? 0;
            $plan         = $user->activePlan();
            $maxQuotes    = $plan?->max_quotes_per_month ?? 3; // Free default

            if ($maxQuotes > 0 && $quotesUsed >= $maxQuotes) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Ai atins limita de oferte pentru luna aceasta. Fă upgrade la un plan superior.',
                        'upgrade_url' => route('plans.index'),
                    ], 403);
                }

                return redirect()->route('plans.index')
                    ->with('warning', "Ai atins limita de {$maxQuotes} oferte/lună. Fă upgrade pentru a trimite mai multe.");
            }
        }

        return $next($request);
    }
}
