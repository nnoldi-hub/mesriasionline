<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PlatformDailyStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'total_visits',
        'unique_visitors',
        'page_views',
        'new_registrations',
        'new_craftsmen',
        'new_clients',
        'active_users',
        'profile_views',
        'messages_sent',
        'quote_requests',
        'quotes_sent',
        'quotes_accepted',
        'appointments_booked',
        'reviews_submitted',
        'total_revenue',
        'affiliate_commissions',
        'visit_to_contact_rate',
        'contact_to_quote_rate',
        'quote_to_booking_rate',
        'traffic_sources',
        'device_breakdown',
        'top_categories',
        'top_locations',
    ];

    protected $casts = [
        'date' => 'date',
        'total_visits' => 'integer',
        'unique_visitors' => 'integer',
        'page_views' => 'integer',
        'new_registrations' => 'integer',
        'new_craftsmen' => 'integer',
        'new_clients' => 'integer',
        'active_users' => 'integer',
        'profile_views' => 'integer',
        'messages_sent' => 'integer',
        'quote_requests' => 'integer',
        'quotes_sent' => 'integer',
        'quotes_accepted' => 'integer',
        'appointments_booked' => 'integer',
        'reviews_submitted' => 'integer',
        'total_revenue' => 'decimal:2',
        'affiliate_commissions' => 'decimal:2',
        'visit_to_contact_rate' => 'decimal:2',
        'contact_to_quote_rate' => 'decimal:2',
        'quote_to_booking_rate' => 'decimal:2',
        'traffic_sources' => 'array',
        'device_breakdown' => 'array',
        'top_categories' => 'array',
        'top_locations' => 'array',
    ];

    /**
     * Get or create today's stats
     */
    public static function today(): self
    {
        return static::firstOrCreate(
            ['date' => Carbon::today()],
            [
                'traffic_sources' => [],
                'device_breakdown' => [],
                'top_categories' => [],
                'top_locations' => [],
            ]
        );
    }

    /**
     * Increment a specific counter
     */
    public function incrementCounter(string $field, int $amount = 1): bool
    {
        if (in_array($field, $this->fillable) && is_numeric($this->{$field})) {
            $this->increment($field, $amount);
            return true;
        }
        return false;
    }

    /**
     * Scope for date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope for last N days
     */
    public function scopeLastDays($query, int $days)
    {
        return $query->where('date', '>=', Carbon::today()->subDays($days));
    }

    /**
     * Get aggregated stats for a period
     */
    public static function getAggregatedStats($startDate, $endDate): array
    {
        $stats = static::whereBetween('date', [$startDate, $endDate])->get();

        return [
            'total_visits' => $stats->sum('total_visits'),
            'unique_visitors' => $stats->sum('unique_visitors'),
            'page_views' => $stats->sum('page_views'),
            'new_registrations' => $stats->sum('new_registrations'),
            'new_craftsmen' => $stats->sum('new_craftsmen'),
            'new_clients' => $stats->sum('new_clients'),
            'profile_views' => $stats->sum('profile_views'),
            'messages_sent' => $stats->sum('messages_sent'),
            'quote_requests' => $stats->sum('quote_requests'),
            'quotes_sent' => $stats->sum('quotes_sent'),
            'quotes_accepted' => $stats->sum('quotes_accepted'),
            'appointments_booked' => $stats->sum('appointments_booked'),
            'reviews_submitted' => $stats->sum('reviews_submitted'),
            'total_revenue' => $stats->sum('total_revenue'),
            'affiliate_commissions' => $stats->sum('affiliate_commissions'),
            'avg_visit_to_contact_rate' => $stats->avg('visit_to_contact_rate'),
            'avg_contact_to_quote_rate' => $stats->avg('contact_to_quote_rate'),
            'avg_quote_to_booking_rate' => $stats->avg('quote_to_booking_rate'),
        ];
    }

    /**
     * Get chart data for visits
     */
    public static function getVisitsChartData($startDate, $endDate): array
    {
        $stats = static::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get(['date', 'total_visits', 'unique_visitors', 'page_views']);

        return [
            'labels' => $stats->pluck('date')->map(fn($d) => $d->format('d M'))->toArray(),
            'datasets' => [
                [
                    'label' => 'Vizite Totale',
                    'data' => $stats->pluck('total_visits')->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
                [
                    'label' => 'Vizitatori Unici',
                    'data' => $stats->pluck('unique_visitors')->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
            ],
        ];
    }

    /**
     * Get chart data for conversions
     */
    public static function getConversionsChartData($startDate, $endDate): array
    {
        $stats = static::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get(['date', 'quote_requests', 'quotes_accepted', 'appointments_booked']);

        return [
            'labels' => $stats->pluck('date')->map(fn($d) => $d->format('d M'))->toArray(),
            'datasets' => [
                [
                    'label' => 'Cereri Ofertă',
                    'data' => $stats->pluck('quote_requests')->toArray(),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                ],
                [
                    'label' => 'Oferte Acceptate',
                    'data' => $stats->pluck('quotes_accepted')->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
                [
                    'label' => 'Programări',
                    'data' => $stats->pluck('appointments_booked')->toArray(),
                    'borderColor' => '#8b5cf6',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                ],
            ],
        ];
    }

    /**
     * Recalculate conversion rates
     */
    public function recalculateRates(): void
    {
        if ($this->profile_views > 0) {
            $contactActions = $this->messages_sent + $this->quote_requests;
            $this->visit_to_contact_rate = ($contactActions / $this->profile_views) * 100;
        }

        if ($this->quote_requests > 0) {
            $this->contact_to_quote_rate = ($this->quotes_sent / $this->quote_requests) * 100;
        }

        if ($this->quotes_sent > 0) {
            $this->quote_to_booking_rate = ($this->quotes_accepted / $this->quotes_sent) * 100;
        }

        $this->save();
    }
}
