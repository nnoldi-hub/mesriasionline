<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Webhook extends Model
{
    // Available webhook events
    const EVENT_APPOINTMENT_CREATED = 'appointment.created';
    const EVENT_APPOINTMENT_CONFIRMED = 'appointment.confirmed';
    const EVENT_APPOINTMENT_COMPLETED = 'appointment.completed';
    const EVENT_APPOINTMENT_CANCELLED = 'appointment.cancelled';
    
    const EVENT_QUOTE_REQUEST_CREATED = 'quote_request.created';
    const EVENT_QUOTE_CREATED = 'quote.created';
    const EVENT_QUOTE_ACCEPTED = 'quote.accepted';
    const EVENT_QUOTE_REJECTED = 'quote.rejected';
    
    const EVENT_REVIEW_CREATED = 'review.created';
    const EVENT_REVIEW_APPROVED = 'review.approved';
    
    const EVENT_MESSAGE_RECEIVED = 'message.received';
    
    const EVENT_USER_REGISTERED = 'user.registered';
    const EVENT_USER_VERIFIED = 'user.verified';

    protected $fillable = [
        'user_id',
        'name',
        'url',
        'events',
        'secret',
        'is_active',
        'last_triggered_at',
        'success_count',
        'failure_count',
    ];

    protected $casts = [
        'events' => 'array',
        'is_active' => 'boolean',
        'last_triggered_at' => 'datetime',
    ];

    /**
     * Get the user that owns the webhook.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the delivery logs for the webhook.
     */
    public function deliveries(): HasMany
    {
        return $this->hasMany(WebhookDelivery::class);
    }

    /**
     * Get all available webhook events.
     */
    public static function getAvailableEvents(): array
    {
        return [
            self::EVENT_APPOINTMENT_CREATED => 'Programare creată',
            self::EVENT_APPOINTMENT_CONFIRMED => 'Programare confirmată',
            self::EVENT_APPOINTMENT_COMPLETED => 'Programare finalizată',
            self::EVENT_APPOINTMENT_CANCELLED => 'Programare anulată',
            self::EVENT_QUOTE_REQUEST_CREATED => 'Cerere ofertă creată',
            self::EVENT_QUOTE_CREATED => 'Ofertă trimisă',
            self::EVENT_QUOTE_ACCEPTED => 'Ofertă acceptată',
            self::EVENT_QUOTE_REJECTED => 'Ofertă respinsă',
            self::EVENT_REVIEW_CREATED => 'Recenzie creată',
            self::EVENT_REVIEW_APPROVED => 'Recenzie aprobată',
            self::EVENT_MESSAGE_RECEIVED => 'Mesaj primit',
            self::EVENT_USER_REGISTERED => 'Utilizator înregistrat',
            self::EVENT_USER_VERIFIED => 'Utilizator verificat',
        ];
    }

    /**
     * Check if webhook listens to specific event.
     */
    public function listensTo(string $event): bool
    {
        return in_array($event, $this->events ?? []);
    }

    /**
     * Record successful delivery.
     */
    public function recordSuccess(): void
    {
        $this->increment('success_count');
        $this->update(['last_triggered_at' => now()]);
    }

    /**
     * Record failed delivery.
     */
    public function recordFailure(): void
    {
        $this->increment('failure_count');
        $this->update(['last_triggered_at' => now()]);
    }

    /**
     * Scope to get active webhooks.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get webhooks listening to specific event.
     */
    public function scopeListeningTo($query, string $event)
    {
        return $query->whereJsonContains('events', $event);
    }
}
