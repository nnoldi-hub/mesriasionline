<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliatePayout extends Model
{
    protected $fillable = [
        'affiliate_id',
        'amount',
        'payment_method',
        'payment_reference',
        'status',
        'requested_at',
        'processed_at',
        'completed_at',
        'notes',
        'processed_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'requested_at' => 'datetime',
            'processed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the affiliate.
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    /**
     * Get the admin who processed this payout.
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scope for pending payouts.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Mark as processing.
     */
    public function markAsProcessing(User $admin): void
    {
        $this->update([
            'status' => 'processing',
            'processed_at' => now(),
            'processed_by' => $admin->id,
        ]);
    }

    /**
     * Mark as completed.
     */
    public function markAsCompleted(string $reference = null): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'payment_reference' => $reference,
        ]);

        // Update affiliate earnings
        $affiliate = $this->affiliate;
        $affiliate->decrement('pending_earnings', $this->amount);
        $affiliate->increment('paid_earnings', $this->amount);

        // Mark related commissions as paid
        AffiliateCommission::where('affiliate_id', $this->affiliate_id)
            ->where('status', 'approved')
            ->where('commission_amount', '<=', $this->amount)
            ->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
    }

    /**
     * Mark as failed.
     */
    public function markAsFailed(string $reason = null): void
    {
        $this->update([
            'status' => 'failed',
            'notes' => $reason,
        ]);
    }
}
