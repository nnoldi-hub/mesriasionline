?php

namespace App\Services;

use App\Models\PaymentTransaction;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Customer;
use Stripe\Event as StripeEvent;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        Stripe::setApiVersion('2024-06-20');
    }

    /**
     * Create a Stripe Checkout Session for the given plan.
     */
    public function createCheckoutSession(User $user, Plan $plan): StripeSession
    {
        $customerId = $this->resolveStripeCustomer($user);

        $session = StripeSession::create([
            'mode'                  => 'payment',
            'customer'              => $customerId,
            'payment_method_types'  => ['card'],
            'line_items'            => [[
                'price_data' => [
                    'currency'     => 'ron',
                    'unit_amount'  => (int) ($plan->price_monthly * 100), // Stripe folosește bani (cenți)
                    'product_data' => [
                        'name'        => "Omul Potrivit — Plan {$plan->name}",
                        'description' => $plan->description,
                    ],
                ],
                'quantity' => 1,
            ]],
            'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('payment.cancel'),
            'metadata'    => [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'plan_slug' => $plan->slug,
            ],
            'locale' => 'ro',
        ]);

        // Log pending transaction
        PaymentTransaction::create([
            'user_id'           => $user->id,
            'plan_id'           => $plan->id,
            'stripe_session_id' => $session->id,
            'stripe_customer_id'=> $customerId,
            'amount'            => $plan->price_monthly,
            'currency'          => 'RON',
            'status'            => 'pending',
        ]);

        return $session;
    }

    /**
     * Handle a successful Stripe Checkout Session.
     */
    public function handleCheckoutCompleted(StripeEvent $event): void
    {
        $session = $event->data->object;

        $transaction = PaymentTransaction::where('stripe_session_id', $session->id)->first();

        if (!$transaction) {
            Log::warning('PaymentService: transaction not found for session ' . $session->id);
            return;
        }

        if ($transaction->status === 'completed') {
            return; // idempotent
        }

        $user = User::findOrFail($transaction->user_id);
        $plan = Plan::findOrFail($transaction->plan_id);

        // Cancel any existing active subscriptions
        Subscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'trial'])
            ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        // Create new subscription (30 days)
        $subscription = Subscription::create([
            'user_id'               => $user->id,
            'plan_id'               => $plan->id,
            'status'                => 'active',
            'started_at'            => now(),
            'ends_at'               => now()->addDays(30),
            'payment_provider'      => 'stripe',
            'payment_reference'     => $session->payment_intent ?? $session->id,
            'quotes_used_this_month'=> 0,
            'quotes_reset_at'       => now()->startOfMonth(),
        ]);

        // Update transaction
        $transaction->update([
            'status'                 => 'completed',
            'subscription_id'        => $subscription->id,
            'stripe_payment_intent'  => $session->payment_intent,
            'stripe_metadata'        => (array) $session->metadata,
        ]);

        // Save Stripe customer ID on user for future payments
        if ($session->customer && !$user->stripe_customer_id) {
            $user->update(['stripe_customer_id' => $session->customer]);
        }

        Log::info("PaymentService: subscription activated for user {$user->id}, plan {$plan->slug}");
    }

    /**
     * Validate and construct a Stripe Webhook event.
     *
     * @throws SignatureVerificationException
     */
    public function constructWebhookEvent(string $payload, string $signature): StripeEvent
    {
        return Webhook::constructEvent(
            $payload,
            $signature,
            config('services.stripe.webhook_secret')
        );
    }

    /**
     * Get or create a Stripe Customer for the user.
     */
    private function resolveStripeCustomer(User $user): string
    {
        if ($user->stripe_customer_id) {
            return $user->stripe_customer_id;
        }

        $customer = Customer::create([
            'email'    => $user->email,
            'name'     => $user->name,
            'metadata' => ['user_id' => $user->id],
        ]);

        $user->update(['stripe_customer_id' => $customer->id]);

        return $customer->id;
    }
}
