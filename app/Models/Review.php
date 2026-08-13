<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'appointment_id',
        'quote_request_id',
        'specialist_id',
        'client_name',
        'rating',
        'comment',
        'specialist_response',
        'is_approved',
        'is_featured',
        'photos',
        'service_quality_rating',
        'punctuality_rating',
        'cleanliness_rating',
        'overall_experience',
    ];

    protected $casts = [
        'photos' => 'array',
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function quoteRequest()
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    public function specialist()
    {
        return $this->belongsTo(User::class, 'specialist_id');
    }
}
