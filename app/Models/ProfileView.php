<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileView extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'craftsman_id',
        'viewer_id',
        'ip_address',
        'user_agent',
        'referrer',
        'source',
        'viewed_at',
    ];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
        ];
    }

    /**
     * Get the craftsman whose profile was viewed.
     */
    public function craftsman()
    {
        return $this->belongsTo(User::class, 'craftsman_id');
    }

    /**
     * Get the viewer (if logged in).
     */
    public function viewer()
    {
        return $this->belongsTo(User::class, 'viewer_id');
    }

    /**
     * Record a profile view.
     */
    public static function record(User $craftsman, ?User $viewer = null, ?array $requestInfo = null): self
    {
        return self::create([
            'craftsman_id' => $craftsman->id,
            'viewer_id' => $viewer?->id,
            'ip_address' => $requestInfo['ip'] ?? request()->ip(),
            'user_agent' => $requestInfo['user_agent'] ?? request()->userAgent(),
            'referrer' => $requestInfo['referrer'] ?? request()->header('referer'),
            'source' => self::detectSource($requestInfo['referrer'] ?? request()->header('referer')),
            'viewed_at' => now(),
        ]);
    }

    /**
     * Detect traffic source from referrer.
     */
    public static function detectSource(?string $referrer): string
    {
        if (!$referrer) {
            return 'direct';
        }

        $referrer = strtolower($referrer);

        if (str_contains($referrer, 'google')) {
            return 'google';
        }
        if (str_contains($referrer, 'facebook') || str_contains($referrer, 'fb.com')) {
            return 'facebook';
        }
        if (str_contains($referrer, 'instagram')) {
            return 'instagram';
        }
        if (str_contains($referrer, 'tiktok')) {
            return 'tiktok';
        }
        if (str_contains($referrer, 'linkedin')) {
            return 'linkedin';
        }
        if (str_contains($referrer, 'youtube')) {
            return 'youtube';
        }
        if (str_contains($referrer, 'twitter') || str_contains($referrer, 'x.com')) {
            return 'twitter';
        }

        // Check if internal referrer
        $appUrl = parse_url(config('app.url'), PHP_URL_HOST);
        if (str_contains($referrer, $appUrl)) {
            return 'internal';
        }

        return 'other';
    }

    /**
     * Scope for a specific craftsman.
     */
    public function scopeForCraftsman($query, $craftsmanId)
    {
        return $query->where('craftsman_id', $craftsmanId);
    }

    /**
     * Scope for a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('viewed_at', [$startDate, $endDate]);
    }
}
