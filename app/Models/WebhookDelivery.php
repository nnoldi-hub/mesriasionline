<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookDelivery extends Model
{
    protected $fillable = [
        'webhook_id',
        'event_type',
        'payload',
        'url',
        'response_status',
        'response_body',
        'success',
        'error_message',
        'attempts',
        'delivered_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'success' => 'boolean',
        'delivered_at' => 'datetime',
    ];

    /**
     * Get the webhook that owns the delivery.
     */
    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class);
    }

    /**
     * Scope to get successful deliveries.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    /**
     * Scope to get failed deliveries.
     */
    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    /**
     * Scope to get recent deliveries.
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
