<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    protected $fillable = [
        'affiliate_id',
        'referred_user_id',
        'referral_code',
        'ip_address',
        'user_agent',
        'landing_page',
        'referrer_url',
        'status',
        'clicked_at',
        'registered_at',
        'converted_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'clicked_at' => 'datetime',
            'registered_at' => 'datetime',
            'converted_at' => 'datetime',
            'expires_at' => 'datetime',
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
     * Get the referred user.
     */
    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    /**
     * Scope for converted referrals.
     */
    public function scopeConverted($query)
    {
        return $query->where('status', 'converted');
    }

    /**
     * Scope for pending referrals.
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['clicked', 'registered']);
    }

    /**
     * Check if referral has expired.
     */
    public function hasExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Mark as registered.
     */
    public function markAsRegistered(User $user): void
    {
        $this->update([
            'referred_user_id' => $user->id,
            'status' => 'registered',
            'registered_at' => now(),
        ]);
    }

    /**
     * Mark as converted.
     */
    public function markAsConverted(): void
    {
        $this->update([
            'status' => 'converted',
            'converted_at' => now(),
        ]);

        // Record successful referral for affiliate
        $this->affiliate->recordSuccessfulReferral();
    }
}
