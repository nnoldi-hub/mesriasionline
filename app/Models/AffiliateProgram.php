<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliateProgram extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'commission_type',
        'commission_value',
        'min_payout',
        'cookie_days',
        'is_active',
        'rules',
    ];

    protected function casts(): array
    {
        return [
            'commission_value' => 'decimal:2',
            'min_payout' => 'decimal:2',
            'is_active' => 'boolean',
            'rules' => 'array',
        ];
    }

    /**
     * Get all affiliates in this program.
     */
    public function affiliates(): HasMany
    {
        return $this->hasMany(Affiliate::class, 'program_id');
    }

    /**
     * Scope for active programs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Calculate commission for a given amount.
     */
    public function calculateCommission(float $amount): float
    {
        if ($this->commission_type === 'percentage') {
            return round($amount * ($this->commission_value / 100), 2);
        }

        return $this->commission_value;
    }

    /**
     * Get the default program.
     */
    public static function getDefault(): ?self
    {
        return self::active()->where('slug', 'default')->first() 
            ?? self::active()->first();
    }
}
