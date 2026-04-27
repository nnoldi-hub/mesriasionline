<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'subscription_id',
        'stripe_session_id',
        'stripe_payment_intent',
        'stripe_customer_id',
        'amount',
        'currency',
        'status',
        'failure_message',
        'stripe_metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount'           => 'decimal:2',
            'stripe_metadata'  => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
