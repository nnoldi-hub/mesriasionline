<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyStat extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'profile_views',
        'service_views',
        'contact_clicks',
        'quote_requests',
        'bookings',
        'messages_received',
        'reviews_received',
        'avg_rating',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'avg_rating' => 'decimal:2',
        ];
    }

    /**
     * Get the user for these stats.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Increment a specific stat for today.
     */
    public static function incrementStat(int $userId, string $stat, int $amount = 1): void
    {
        $record = self::firstOrCreate(
            ['user_id' => $userId, 'date' => now()->toDateString()],
            [
                'profile_views' => 0,
                'service_views' => 0,
                'contact_clicks' => 0,
                'quote_requests' => 0,
                'bookings' => 0,
                'messages_received' => 0,
                'reviews_received' => 0,
            ]
        );

        $record->increment($stat, $amount);
    }

    /**
     * Get stats for a date range.
     */
    public static function getStatsForPeriod(int $userId, string $startDate, string $endDate): array
    {
        $stats = self::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        return [
            'profile_views' => $stats->sum('profile_views'),
            'service_views' => $stats->sum('service_views'),
            'contact_clicks' => $stats->sum('contact_clicks'),
            'quote_requests' => $stats->sum('quote_requests'),
            'bookings' => $stats->sum('bookings'),
            'messages_received' => $stats->sum('messages_received'),
            'reviews_received' => $stats->sum('reviews_received'),
            'avg_rating' => $stats->avg('avg_rating'),
            'daily' => $stats->keyBy('date'),
        ];
    }

    /**
     * Get conversion rate (quote requests / profile views).
     */
    public function getConversionRateAttribute(): float
    {
        if ($this->profile_views === 0) {
            return 0;
        }
        return round(($this->quote_requests / $this->profile_views) * 100, 2);
    }

    /**
     * Scope for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope for last N days.
     */
    public function scopeLastDays($query, $days = 30)
    {
        return $query->where('date', '>=', now()->subDays($days)->toDateString());
    }
}
