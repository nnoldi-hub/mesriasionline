<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    protected $fillable = [
        'client_id',
        'craftsman_id',
        'service_id',
        'title',
        'description',
        'location',
        'client_lat',
        'client_lng',
        'preferred_date',
        'preferred_time',
        'images',
        'budget_min',
        'budget_max',
        'urgency',
        'status',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'images' => 'array',
            'budget_min' => 'decimal:2',
            'budget_max' => 'decimal:2',
            'client_lat' => 'decimal:8',
            'client_lng' => 'decimal:8',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Get the client who made the request.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the craftsman who received the request.
     */
    public function craftsman()
    {
        return $this->belongsTo(User::class, 'craftsman_id');
    }

    /**
     * Get the service this request is for.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get all quotes for this request.
     */
    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    /**
     * Get the accepted quote.
     */
    public function acceptedQuote()
    {
        return $this->hasOne(Quote::class)->where('status', 'accepted');
    }

    /**
     * Check if request is still pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if request has expired.
     */
    public function isExpired(): bool
    {
        if ($this->status === 'expired') {
            return true;
        }
        
        if ($this->expires_at && $this->expires_at->isPast()) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if request can receive quotes.
     */
    public function canReceiveQuotes(): bool
    {
        return $this->isPending() && !$this->isExpired();
    }

    /**
     * Get urgency label.
     */
    public function getUrgencyLabelAttribute(): string
    {
        return match($this->urgency) {
            'low' => 'Scăzută',
            'normal' => 'Normală',
            'high' => 'Ridicată',
            'urgent' => 'Urgentă',
            default => 'Normală',
        };
    }

    /**
     * Get urgency color class.
     */
    public function getUrgencyColorAttribute(): string
    {
        return match($this->urgency) {
            'low' => 'secondary',
            'normal' => 'info',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'info',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'În așteptare',
            'quoted' => 'Ofertă primită',
            'accepted' => 'Acceptată',
            'rejected' => 'Respinsă',
            'expired' => 'Expirată',
            'completed' => 'Finalizată',
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
            'quoted' => 'info',
            'accepted' => 'success',
            'rejected' => 'danger',
            'expired' => 'secondary',
            'completed' => 'primary',
            default => 'secondary',
        };
    }

    /**
     * Get preferred time label.
     */
    public function getPreferredTimeLabelAttribute(): ?string
    {
        if (!$this->preferred_time) {
            return null;
        }
        
        return match($this->preferred_time) {
            'morning' => 'Dimineața (8:00 - 12:00)',
            'afternoon' => 'După-amiaza (12:00 - 18:00)',
            'evening' => 'Seara (18:00 - 21:00)',
            default => $this->preferred_time,
        };
    }

    /**
     * Scope for pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for client's requests.
     */
    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Scope for craftsman's requests.
     */
    public function scopeForCraftsman($query, $craftsmanId)
    {
        return $query->where('craftsman_id', $craftsmanId);
    }

    /**
     * Scope for not expired requests.
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        })->where('status', '!=', 'expired');
    }
}
