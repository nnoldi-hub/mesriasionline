<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionManagementController extends Controller
{
    public function subscriptions(Request $request)
    {
        $query = Subscription::with(['user', 'plan'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $subscriptions = $query->paginate(30)->withQueryString();
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        // Revenue stats
        $activeCount = Subscription::where('status', 'active')->count();
        $trialCount  = Subscription::where('status', 'trial')->count();
        $mrr = PaymentTransaction::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
        $totalRevenue = PaymentTransaction::where('status', 'completed')->sum('amount');

        return view('admin.subscriptions.index', compact(
            'subscriptions', 'plans', 'activeCount', 'trialCount', 'mrr', 'totalRevenue'
        ));
    }

    public function transactions(Request $request)
    {
        $query = PaymentTransaction::with(['user', 'plan'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"))
                  ->orWhere('stripe_session_id', 'like', "%{$search}%");
            });
        }

        $transactions = $query->paginate(30)->withQueryString();
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        $totalCompleted = PaymentTransaction::where('status', 'completed')->sum('amount');
        $totalFailed    = PaymentTransaction::where('status', 'failed')->count();
        $todayRevenue   = PaymentTransaction::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('amount');

        return view('admin.transactions.index', compact(
            'transactions', 'plans', 'totalCompleted', 'totalFailed', 'todayRevenue'
        ));
    }
}
