<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'specialist_id',
        'client_name',
        'client_email',
        'client_phone',
        'service_id',
        'appointment_date',
        'appointment_time',
        'status',
        'is_home_service',
        'client_address',
        'client_city',
        'client_zone',
        'distance_km',
        'transport_fee',
        'estimated_travel_time',
        'payment_status',
        'payment_method',
        'total_amount',
        'review_token',
        'notes',
        'request_type',
        'work_description',
        'work_photos',
        'preferred_start_date',
        'urgency',
        'quoted_price',
        'quote_details',
        'estimated_duration_hours',
        'quote_valid_until',
        'actual_start_date',
        'actual_end_date',
        'actual_duration_hours',
        'completion_notes',
        'completion_photos',
        'warranty_months',
        'warranty_expires_at',
        'requires_followup',
        'followup_date',
        // Calendar & reminder fields
        'google_calendar_event_id',
        'outlook_calendar_event_id',
        'sms_reminder_sent_at',
        'email_reminder_sent_at',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'is_home_service' => 'boolean',
        'work_photos' => 'array',
        'completion_photos' => 'array',
        'preferred_start_date' => 'date',
        'quote_valid_until' => 'date',
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
        'warranty_expires_at' => 'date',
        'requires_followup' => 'boolean',
        'followup_date' => 'date',
        'sms_reminder_sent_at' => 'datetime',
        'email_reminder_sent_at' => 'datetime',
    ];

    public function specialist()
    {
        return $this->belongsTo(User::class, 'specialist_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
