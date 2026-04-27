<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Affiliate extends Model
{
    protected $fillable = [
        'user_id',
        'program_id',
        'referral_code',
        'payment_method',
        'payment_details',
        'status',
        'total_earnings',
        'pending_earnings',
        'paid_earnings',
        'total_referrals',
        'successful_referrals',
        'approved_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_earnings' => 'decimal:2',
            'pending_earnings' => 'decimal:2',
            'paid_earnings' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Boot method.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($affiliate) {
            if (empty($affiliate->referral_code)) {
                $affiliate->referral_code = self::generateUniqueCode();
            }
        });
    }

    /**
     * Generate a unique referral code.
     */
    public static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Get the user who is the affiliate.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the affiliate program.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(AffiliateProgram::class, 'program_id');
    }

    /**
     * Get all referrals.
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }

    /**
     * Get all commissions.
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class);
    }

    /**
     * Get all payouts.
     */
    public function payouts(): HasMany
    {
        return $this->hasMany(AffiliatePayout::class);
    }

    /**
     * Scope for active affiliates.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if affiliate is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get referral URL.
     */
    public function getReferralUrlAttribute(): string
    {
        return url('/?ref=' . $this->referral_code);
    }

    /**
     * Get conversion rate.
     */
    public function getConversionRateAttribute(): float
    {
        if ($this->total_referrals === 0) {
            return 0;
        }

        return round(($this->successful_referrals / $this->total_referrals) * 100, 2);
    }

    /**
     * Check if can request payout.
     */
    public function canRequestPayout(): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        $minPayout = $this->program?->min_payout ?? 100;
        return $this->pending_earnings >= $minPayout;
    }

    /**
     * Add earnings from a commission.
     */
    public function addEarnings(float $amount): void
    {
        $this->increment('total_earnings', $amount);
        $this->increment('pending_earnings', $amount);
    }

    /**
     * Record a successful referral.
     */
    public function recordSuccessfulReferral(): void
    {
        $this->increment('successful_referrals');
    }

    /**
     * Record a click/visit.
     */
    public function recordClick(): void
    {
        $this->increment('total_referrals');
    }
}
