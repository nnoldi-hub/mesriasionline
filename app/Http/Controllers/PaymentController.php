<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Models\Plan;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService)
    {
    }

    /**
     * Show confirmation page before redirecting to Stripe.
     */
    public function checkout(string $slug)
    {
        $plan = Plan::where('slug', $slug)->where('is_active', true)->firstOrFail();

        if ($plan->isFree()) {
            return redirect()->route('plans.index');
        }

        $user = Auth::user();
        $currentPlan = $user->activePlan();

        return view('plans.checkout', compact('plan', 'currentPlan'));
    }

    /**
     * Redirect to Stripe Checkout.
     */
    public function redirectToStripe(Request $request, string $slug)
    {
        $plan = Plan::where('slug', $slug)->where('is_active', true)->firstOrFail();

        if ($plan->isFree()) {
            return redirect()->route('plans.index');
        }

        try {
            $session = $this->paymentService->createCheckoutSession(Auth::user(), $plan);
            return redirect($session->url, 303);
        } catch (\Exception $e) {
            Log::error('Stripe checkout error: ' . $e->getMessage());
            return redirect()->route('plans.index')
                ->with('error', 'Nu am putut inițializa plata. Te rugăm să încerci din nou.');
        }
    }

    /**
     * Handle Stripe success redirect.
     */
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('plans.index');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session     = StripeSession::retrieve($sessionId);
            $transaction = PaymentTransaction::where('stripe_session_id', $sessionId)->first();
            $plan        = $transaction ? $transaction->plan : null;
        } catch (\Exception $e) {
            Log::error('Stripe success page error: ' . $e->getMessage());
            $session = null;
            $plan    = null;
        }

        return view('plans.success', compact('plan'));
    }

    /**
     * Handle Stripe cancel redirect.
     */
    public function cancel()
    {
        return view('plans.cancel');
    }
}
