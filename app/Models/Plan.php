<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_monthly',
        'max_quotes_per_month',
        'featured_listing',
        'priority_visibility',
        'badge_visible',
        'features',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price_monthly'      => 'decimal:2',
            'featured_listing'   => 'boolean',
            'priority_visibility'=> 'boolean',
            'badge_visible'      => 'boolean',
            'is_active'          => 'boolean',
            'features'           => 'array',
        ];
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function isFree(): bool
    {
        return $this->slug === 'free';
    }

    public function isPro(): bool
    {
        return $this->slug === 'pro';
    }

    public static function free(): ?self
    {
        return static::where('slug', 'free')->first();
    }

    public static function active()
    {
        return static::where('is_active', true)->orderBy('sort_order');
    }
}
