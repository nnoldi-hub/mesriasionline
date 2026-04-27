<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = Plan::active()->get();
        $activeSubscription = Auth::user()?->activeSubscription();
        $currentPlan = $activeSubscription?->plan ?? Plan::free();

        return view('plans.index', compact('plans', 'activeSubscription', 'currentPlan'));
    }

    public function subscribe(Request $request, string $slug)
    {
        $plan = Plan::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $user = Auth::user();

        // Cancel any existing active subscription
        Subscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'trial'])
            ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        // Free plan — just cancel existing, no new subscription needed
        if ($plan->isFree()) {
            return redirect()->route('plans.index')
                ->with('success', 'Ai revenit la planul Free.');
        }

        // Create new 30-day trial/subscription (payment integration can replace this later)
        Subscription::create([
            'user_id'              => $user->id,
            'plan_id'              => $plan->id,
            'status'               => 'trial',
            'started_at'           => now(),
            'ends_at'              => now()->addDays(30),
            'payment_provider'     => 'manual',
            'payment_reference'    => null,
            'quotes_used_this_month' => 0,
            'quotes_reset_at'      => now()->startOfMonth(),
        ]);

        return redirect()->route('plans.index')
            ->with('success', "Felicitări! Ai activat planul {$plan->name} pentru 30 de zile.");
    }

    public function cancel(Request $request)
    {
        $user = Auth::user();

        Subscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'trial'])
            ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        return redirect()->route('plans.index')
            ->with('success', 'Abonamentul a fost anulat. Vei trece la planul Free.');
    }
}
