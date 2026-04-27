<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'issuing_organization',
        'issue_date',
        'expiry_date',
        'credential_id',
        'credential_url',
        'document_path',
        'is_verified',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'expiry_date' => 'date',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Get the craftsman who owns this certification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if certification is expired.
     */
    public function isExpired(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }
        return $this->expiry_date->isPast();
    }

    /**
     * Check if certification is expiring soon (within 30 days).
     */
    public function isExpiringSoon(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }
        return $this->expiry_date->isBetween(now(), now()->addDays(30));
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->isExpired()) {
            return 'Expirată';
        }
        if ($this->isExpiringSoon()) {
            return 'Expiră curând';
        }
        if ($this->is_verified) {
            return 'Verificată';
        }
        return 'În așteptare';
    }

    /**
     * Get status color.
     */
    public function getStatusColorAttribute(): string
    {
        if ($this->isExpired()) {
            return 'red';
        }
        if ($this->isExpiringSoon()) {
            return 'yellow';
        }
        if ($this->is_verified) {
            return 'green';
        }
        return 'gray';
    }

    /**
     * Scope for verified certifications.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for valid (not expired) certifications.
     */
    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expiry_date')
              ->orWhere('expiry_date', '>', now());
        });
    }
}
