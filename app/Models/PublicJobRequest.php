<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PublicJobRequest extends Model
{
    /**
     * Numărul maxim de meseriași interesați acceptați înainte ca cererea
     * să se închidă automat (clientul are deja destule oferte).
     */
    public const MAX_INTERESTED = 3;

    protected $fillable = [
        'category_id',
        'location_id',
        'name',
        'phone',
        'email',
        'title',
        'description',
        'city',
        'preferred_date',
        'urgency',
        'budget_max',
        'status',
        'notified_craftsmen',
        'access_token',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'budget_max' => 'decimal:2',
        'notified_craftsmen' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->access_token)) {
                $model->access_token = Str::random(64);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function responses()
    {
        return $this->hasMany(PublicJobRequestResponse::class);
    }

    public function interestedCraftsmen()
    {
        return $this->responses()->where('action', 'interested');
    }

    /**
     * A ajuns cererea la numărul maxim de meseriași interesați?
     */
    public function isFull(): bool
    {
        return $this->interestedCraftsmen()->count() >= self::MAX_INTERESTED;
    }

    public function getUrgencyLabelAttribute(): string
    {
        return match ($this->urgency) {
            'this_week' => 'Săptămâna aceasta',
            'urgent'    => 'Urgent (1-2 zile)',
            default     => 'Flexibil',
        };
    }

    public function getLocationDisplayAttribute(): string
    {
        return $this->location?->city ?? $this->city ?? 'Nespecificat';
    }
}
