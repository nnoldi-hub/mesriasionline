<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'started_at',
        'ends_at',
        'cancelled_at',
        'payment_provider',
        'payment_reference',
        'quotes_used_this_month',
        'quotes_reset_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at'  => 'datetime',
            'ends_at'     => 'datetime',
            'cancelled_at'=> 'datetime',
            'quotes_reset_at' => 'datetime',
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

    public function isActive(): bool
    {
        if ($this->status !== 'active' && $this->status !== 'trial') {
            return false;
        }

        return $this->ends_at === null || $this->ends_at->isFuture();
    }

    public function hasQuotesAvailable(): bool
    {
        $max = $this->plan->max_quotes_per_month;

        if ($max === 0) {
            return true; // nelimitat
        }

        $this->resetQuotesIfNewMonth();

        return $this->quotes_used_this_month < $max;
    }

    public function incrementQuotesUsed(): void
    {
        $this->resetQuotesIfNewMonth();
        $this->increment('quotes_used_this_month');
    }

    private function resetQuotesIfNewMonth(): void
    {
        if ($this->quotes_reset_at === null || $this->quotes_reset_at->month !== now()->month) {
            $this->update([
                'quotes_used_this_month' => 0,
                'quotes_reset_at'        => now(),
            ]);
        }
    }
}
