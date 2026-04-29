<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasPushSubscriptions;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'phone',
        'description',
        'sub_brand',
        'coverage_area',
        'transport_fee',
        'max_distance',
        'slug',
        'category_id',
        'location_id',
        'specialization',
        'experience_years',
        'certifications',
        'has_insurance',
        'company_name',
        'cui',
        'is_company',
        'company_type',
        'reg_com',
        'service_radius_km',
        'working_hours',
        'available_weekends',
        'emergency_services',
        'facebook_url',
        'instagram_url',
        'tiktok_url',
        'linkedin_url',
        'youtube_url',
        'whatsapp_number',
        'website_url',
        // Advanced filter fields
        'latitude',
        'longitude',
        'is_featured',
        'is_verified',
        'has_gallery',
        'min_price',
        'max_price',
        'response_time_hours',
        'completed_projects',
        'last_active_at',
        // Enhanced profile fields
        'video_url',
        'video_thumbnail',
        'weekly_schedule',
        'break_times',
        'slot_duration_minutes',
        'buffer_between_slots',
        'vacation_periods',
        'coverage_zones',
        'extra_fee_per_km',
        'short_bio',
        'languages_spoken',
        'payment_methods',
        'offers_free_estimate',
        'offers_warranty',
        'warranty_months',
        // Calendar integration fields
        'google_calendar_token',
        'google_calendar_id',
        'outlook_calendar_token',
        'outlook_calendar_id',
        // Onboarding wizard
        'onboarding_step',
        'onboarding_completed_at',
        // Stripe
        'stripe_customer_id',
        // Notification preferences
        'notification_preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'coverage_area' => 'array',
            'certifications' => 'array',
            'working_hours' => 'array',
            'has_insurance' => 'boolean',
            'available_weekends' => 'boolean',
            'emergency_services' => 'boolean',
            // Advanced filter casts
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'is_featured' => 'boolean',
            'is_verified' => 'boolean',
            'has_gallery' => 'boolean',
            'min_price' => 'decimal:2',
            'max_price' => 'decimal:2',
            'last_active_at' => 'datetime',
            // Enhanced profile casts
            'weekly_schedule' => 'array',
            'break_times' => 'array',
            'vacation_periods' => 'array',
            'coverage_zones' => 'array',
            'languages_spoken' => 'array',
            'payment_methods' => 'array',
            'offers_free_estimate' => 'boolean',
            'offers_warranty' => 'boolean',
            'is_company' => 'boolean',
            'extra_fee_per_km' => 'decimal:2',
            'onboarding_completed_at' => 'datetime',
            'notification_preferences' => 'array',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function certificationDocuments()
    {
        return $this->hasMany(Certification::class);
    }

    public function availabilitySlots()
    {
        return $this->hasMany(AvailabilitySlot::class);
    }

    public function bookingSettings()
    {
        return $this->hasOne(BookingSetting::class);
    }

    public function profileViews()
    {
        return $this->hasMany(ProfileView::class, 'craftsman_id');
    }

    public function dailyStats()
    {
        return $this->hasMany(DailyStat::class);
    }

    public function addresses()
    {
        return $this->hasMany(ClientAddress::class);
    }

    public function defaultAddress()
    {
        return $this->hasOne(ClientAddress::class)->where('is_default', true);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'specialist_id');
    }

    /**
     * Client appointments (programările făcute de client)
     */
    public function clientAppointments()
    {
        return $this->hasMany(Appointment::class, 'client_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'specialist_id');
    }

    /**
     * Reviews written by this user as a client
     */
    public function clientReviews()
    {
        return $this->hasMany(Review::class, 'client_id');
    }

    /**
     * Quote requests made by this user as a client
     */
    public function quoteRequests()
    {
        return $this->hasMany(QuoteRequest::class, 'client_id');
    }

    public function gallery()
    {
        return $this->hasMany(Gallery::class);
    }

    public function featuredGallery()
    {
        return $this->hasMany(Gallery::class)->where('is_featured', true);
    }

    /**
     * Webhooks owned by this user
     */
    public function webhooks()
    {
        return $this->hasMany(Webhook::class);
    }

    // ─── Subscriptions ───────────────────────────────────────────────

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->with('plan')
            ->whereIn('status', ['active', 'trial'])
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->latest('started_at')
            ->first();
    }

    public function activePlan(): ?Plan
    {
        return $this->activeSubscription()?->plan;
    }

    public function isOnPlan(string $slug): bool
    {
        return $this->activePlan()?->slug === $slug;
    }

    public function isPro(): bool
    {
        return $this->isOnPlan('pro');
    }

    public function isStarter(): bool
    {
        return $this->isOnPlan('starter');
    }

    /**
     * Check if the user has any social media links.
     */
    public function hasSocialLinks(): bool
    {
        return $this->facebook_url || 
               $this->instagram_url || 
               $this->tiktok_url || 
               $this->linkedin_url || 
               $this->youtube_url || 
               $this->whatsapp_number ||
               $this->website_url;
    }

    /**
     * Check if the user has gallery photos.
     */
    public function hasGalleryPhotos(): bool
    {
        return $this->gallery()->count() > 0;
    }

    /**
     * Get the average rating.
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get total reviews count.
     */
    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }

    // ===== SCOPES FOR ADVANCED FILTERING =====

    /**
     * Scope for featured craftsmen.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for verified craftsmen.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for craftsmen with gallery photos.
     */
    public function scopeWithGalleryPhotos($query)
    {
        return $query->where('has_gallery', true);
    }

    /**
     * Scope for craftsmen with minimum rating.
     */
    public function scopeMinRating($query, $rating)
    {
        return $query->whereHas('reviews', function ($q) use ($rating) {
            $q->havingRaw('AVG(rating) >= ?', [$rating]);
        }, '>=', 1)->orWhere(function ($q) use ($rating) {
            // Also show craftsmen without reviews if rating filter is low
            if ($rating <= 3) {
                $q->doesntHave('reviews');
            }
        });
    }

    /**
     * Scope for craftsmen with minimum number of reviews.
     */
    public function scopeMinReviews($query, $count)
    {
        return $query->has('reviews', '>=', $count);
    }

    /**
     * Scope for craftsmen near a location (using Haversine formula).
     * @param float $lat Latitude
     * @param float $lng Longitude
     * @param int $radius Radius in kilometers
     */
    public function scopeNearLocation($query, $lat, $lng, $radius = 50)
    {
        $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))";
        
        return $query->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->selectRaw("{$haversine} AS distance", [$lat, $lng, $lat])
            ->whereRaw("{$haversine} <= ?", [$lat, $lng, $lat, $radius]);
    }

    /**
     * Scope for craftsmen with price range.
     */
    public function scopePriceRange($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice) {
            $query->where(function ($q) use ($minPrice) {
                $q->where('min_price', '>=', $minPrice)
                  ->orWhereNull('min_price');
            });
        }
        
        if ($maxPrice) {
            $query->where(function ($q) use ($maxPrice) {
                $q->where('max_price', '<=', $maxPrice)
                  ->orWhereNull('max_price');
            });
        }
        
        return $query;
    }

    /**
     * Scope for available on weekends.
     */
    public function scopeAvailableWeekends($query)
    {
        return $query->where('available_weekends', true);
    }

    /**
     * Scope for emergency services.
     */
    public function scopeEmergencyAvailable($query)
    {
        return $query->where('emergency_services', true);
    }

    /**
     * Scope for minimum experience years.
     */
    public function scopeMinExperience($query, $years)
    {
        return $query->where('experience_years', '>=', $years);
    }

    /**
     * Scope for active craftsmen (active in last 30 days).
     */
    public function scopeRecentlyActive($query, $days = 30)
    {
        return $query->where('last_active_at', '>=', now()->subDays($days));
    }

    /**
     * Order by distance from a location.
     */
    public function scopeOrderByDistance($query, $lat, $lng)
    {
        $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))";
        
        return $query->orderByRaw($haversine, [$lat, $lng, $lat]);
    }

    /**
     * Calculate distance to a point.
     */
    public function distanceTo($lat, $lng)
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        $earthRadius = 6371; // km

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 1);
    }
}
