<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = [
        'quote_request_id',
        'craftsman_id',
        'price',
        'price_max',
        'description',
        'materials_included',
        'estimated_duration_hours',
        'estimated_duration_days',
        'available_from',
        'valid_until',
        'breakdown',
        'status',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'price_max' => 'decimal:2',
            'available_from' => 'date',
            'valid_until' => 'date',
            'breakdown' => 'array',
        ];
    }

    /**
     * Get the quote request this quote belongs to.
     */
    public function quoteRequest()
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    /**
     * Get the craftsman who made this quote.
     */
    public function craftsman()
    {
        return $this->belongsTo(User::class, 'craftsman_id');
    }

    /**
     * Check if quote is still pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if quote was accepted.
     */
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    /**
     * Check if quote has expired.
     */
    public function isExpired(): bool
    {
        if ($this->status === 'expired') {
            return true;
        }
        
        if ($this->valid_until && $this->valid_until->isPast()) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if quote is still valid for acceptance.
     */
    public function canBeAccepted(): bool
    {
        return $this->isPending() && !$this->isExpired();
    }

    /**
     * Get formatted price display.
     */
    public function getPriceDisplayAttribute(): string
    {
        if ($this->price_max) {
            return number_format($this->price, 0, ',', '.') . ' - ' . number_format($this->price_max, 0, ',', '.') . ' lei';
        }
        return number_format($this->price, 0, ',', '.') . ' lei';
    }

    /**
     * Get estimated duration display.
     */
    public function getDurationDisplayAttribute(): ?string
    {
        $parts = [];
        
        if ($this->estimated_duration_days) {
            $parts[] = $this->estimated_duration_days . ' ' . ($this->estimated_duration_days == 1 ? 'zi' : 'zile');
        }
        
        if ($this->estimated_duration_hours) {
            $parts[] = $this->estimated_duration_hours . ' ' . ($this->estimated_duration_hours == 1 ? 'oră' : 'ore');
        }
        
        return empty($parts) ? null : implode(' și ', $parts);
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'În așteptare',
            'accepted' => 'Acceptată',
            'rejected' => 'Respinsă',
            'expired' => 'Expirată',
            'withdrawn' => 'Retrasă',
            default => 'Necunoscut',
        };
    }

    /**
     * Get status color class.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'accepted' => 'success',
            'rejected' => 'danger',
            'expired' => 'secondary',
            'withdrawn' => 'dark',
            default => 'secondary',
        };
    }

    /**
     * Accept this quote.
     */
    public function accept(): bool
    {
        if (!$this->canBeAccepted()) {
            return false;
        }

        // Reject all other quotes for this request
        Quote::where('quote_request_id', $this->quote_request_id)
            ->where('id', '!=', $this->id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        // Accept this quote
        $this->update(['status' => 'accepted']);

        // Update quote request status
        $this->quoteRequest->update(['status' => 'accepted']);

        return true;
    }

    /**
     * Reject this quote.
     */
    public function reject(?string $reason = null): bool
    {
        if (!$this->isPending()) {
            return false;
        }

        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        return true;
    }

    /**
     * Withdraw this quote.
     */
    public function withdraw(): bool
    {
        if (!$this->isPending()) {
            return false;
        }

        $this->update(['status' => 'withdrawn']);

        return true;
    }

    /**
     * Scope for pending quotes.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for valid quotes (not expired).
     */
    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('valid_until')
              ->orWhere('valid_until', '>', now());
        })->where('status', '!=', 'expired');
    }
}
