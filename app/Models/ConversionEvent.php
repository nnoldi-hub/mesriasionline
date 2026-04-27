<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversionEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'craftsman_id',
        'event_type',
        'event_data',
        'source',
        'medium',
        'campaign',
        'referrer',
        'landing_page',
        'ip_address',
        'user_agent',
        'device_type',
        'converted_at',
        'conversion_value',
    ];

    protected $casts = [
        'event_data' => 'array',
        'converted_at' => 'datetime',
        'conversion_value' => 'decimal:2',
    ];

    // Event types
    const TYPE_PAGE_VIEW = 'page_view';
    const TYPE_PROFILE_VIEW = 'profile_view';
    const TYPE_CONTACT_CLICK = 'contact_click';
    const TYPE_PHONE_REVEAL = 'phone_reveal';
    const TYPE_MESSAGE_SENT = 'message_sent';
    const TYPE_QUOTE_REQUEST = 'quote_request';
    const TYPE_QUOTE_RECEIVED = 'quote_received';
    const TYPE_QUOTE_ACCEPTED = 'quote_accepted';
    const TYPE_APPOINTMENT_BOOKED = 'appointment_booked';
    const TYPE_REVIEW_SUBMITTED = 'review_submitted';
    const TYPE_REGISTRATION = 'registration';

    /**
     * Get the user who triggered the event.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the craftsman associated with the event.
     */
    public function craftsman()
    {
        return $this->belongsTo(User::class, 'craftsman_id');
    }

    /**
     * Scope for specific event type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('event_type', $type);
    }

    /**
     * Scope for converted events.
     */
    public function scopeConverted($query)
    {
        return $query->whereNotNull('converted_at');
    }

    /**
     * Scope for date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for specific source.
     */
    public function scopeFromSource($query, string $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Get funnel stage for this event type.
     */
    public function getFunnelStageAttribute(): int
    {
        $stages = [
            self::TYPE_PAGE_VIEW => 1,
            self::TYPE_PROFILE_VIEW => 2,
            self::TYPE_CONTACT_CLICK => 3,
            self::TYPE_PHONE_REVEAL => 3,
            self::TYPE_MESSAGE_SENT => 4,
            self::TYPE_QUOTE_REQUEST => 4,
            self::TYPE_QUOTE_RECEIVED => 5,
            self::TYPE_QUOTE_ACCEPTED => 6,
            self::TYPE_APPOINTMENT_BOOKED => 7,
            self::TYPE_REVIEW_SUBMITTED => 8,
        ];

        return $stages[$this->event_type] ?? 0;
    }

    /**
     * Check if this is a final conversion event.
     */
    public function getIsFinalConversionAttribute(): bool
    {
        return in_array($this->event_type, [
            self::TYPE_QUOTE_ACCEPTED,
            self::TYPE_APPOINTMENT_BOOKED,
        ]);
    }
}
