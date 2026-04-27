<?php

namespace App\Services;

use App\Models\ConversionEvent;
use App\Models\ConversionFunnel;
use App\Models\PlatformDailyStat;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ConversionTrackingService
{
    /**
     * Track a conversion event
     */
    public function trackEvent(
        string $eventType,
        ?int $userId = null,
        ?int $craftsmanId = null,
        array $eventData = [],
        ?float $conversionValue = null
    ): ConversionEvent {
        $sessionId = $this->getSessionId();
        
        $event = ConversionEvent::create([
            'session_id' => $sessionId,
            'user_id' => $userId,
            'craftsman_id' => $craftsmanId,
            'event_type' => $eventType,
            'event_data' => $eventData,
            'source' => $this->getSource(),
            'medium' => $this->getMedium(),
            'campaign' => request()->get('utm_campaign') ?? session('utm_campaign'),
            'referrer' => request()->header('referer'),
            'landing_page' => $this->getLandingPage(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'device_type' => $this->getDeviceType(),
            'converted_at' => $this->isConversionEvent($eventType) ? now() : null,
            'conversion_value' => $conversionValue,
        ]);

        // Update funnel
        $this->updateFunnel($sessionId, $eventType, $userId, $craftsmanId, $conversionValue);

        // Update daily stats
        $this->updateDailyStats($eventType);

        return $event;
    }

    /**
     * Get or create session ID
     */
    protected function getSessionId(): string
    {
        if (!session()->has('conversion_session_id')) {
            session(['conversion_session_id' => Str::uuid()->toString()]);
        }
        return session('conversion_session_id');
    }

    /**
     * Get traffic source
     */
    protected function getSource(): ?string
    {
        // Check UTM first
        $utmSource = request()->get('utm_source') ?? session('utm_source');
        if ($utmSource) {
            // Store in session for later pageviews
            if (request()->has('utm_source')) {
                session(['utm_source' => $utmSource]);
            }
            return $utmSource;
        }

        // Parse referrer
        $referrer = request()->header('referer');
        if (!$referrer) return 'direct';

        $host = parse_url($referrer, PHP_URL_HOST);
        if (!$host) return 'direct';

        // Common search engines
        $searchEngines = ['google', 'bing', 'yahoo', 'duckduckgo', 'yandex', 'baidu'];
        foreach ($searchEngines as $engine) {
            if (stripos($host, $engine) !== false) {
                return $engine;
            }
        }

        // Social networks
        $socialNetworks = ['facebook', 'twitter', 'linkedin', 'instagram', 'pinterest', 'tiktok', 'youtube'];
        foreach ($socialNetworks as $network) {
            if (stripos($host, $network) !== false) {
                return $network;
            }
        }

        // If referrer is own site
        if (stripos($host, request()->getHost()) !== false) {
            return 'internal';
        }

        return 'referral';
    }

    /**
     * Get traffic medium
     */
    protected function getMedium(): ?string
    {
        // Check UTM first
        $utmMedium = request()->get('utm_medium') ?? session('utm_medium');
        if ($utmMedium) {
            if (request()->has('utm_medium')) {
                session(['utm_medium' => $utmMedium]);
            }
            return $utmMedium;
        }

        $source = $this->getSource();

        // Determine medium based on source
        $searchEngines = ['google', 'bing', 'yahoo', 'duckduckgo', 'yandex', 'baidu'];
        if (in_array($source, $searchEngines)) {
            return 'organic';
        }

        $socialNetworks = ['facebook', 'twitter', 'linkedin', 'instagram', 'pinterest', 'tiktok', 'youtube'];
        if (in_array($source, $socialNetworks)) {
            return 'social';
        }

        if ($source === 'direct') return 'none';
        if ($source === 'internal') return 'internal';
        if ($source === 'referral') return 'referral';

        return null;
    }

    /**
     * Get landing page (first page of session)
     */
    protected function getLandingPage(): ?string
    {
        if (!session()->has('landing_page')) {
            session(['landing_page' => request()->fullUrl()]);
        }
        return session('landing_page');
    }

    /**
     * Detect device type
     */
    protected function getDeviceType(): string
    {
        $userAgent = strtolower(request()->userAgent() ?? '');

        if (preg_match('/mobile|android|iphone|ipod|blackberry|windows phone/i', $userAgent)) {
            return 'mobile';
        }

        if (preg_match('/tablet|ipad|kindle|playbook/i', $userAgent)) {
            return 'tablet';
        }

        return 'desktop';
    }

    /**
     * Check if event is a conversion event
     */
    protected function isConversionEvent(string $eventType): bool
    {
        return in_array($eventType, [
            ConversionEvent::TYPE_QUOTE_ACCEPTED,
            ConversionEvent::TYPE_APPOINTMENT_BOOKED,
            ConversionEvent::TYPE_REGISTRATION,
        ]);
    }

    /**
     * Update or create conversion funnel
     */
    protected function updateFunnel(
        string $sessionId,
        string $eventType,
        ?int $userId,
        ?int $craftsmanId,
        ?float $conversionValue
    ): void {
        $funnel = ConversionFunnel::firstOrCreate(
            ['session_id' => $sessionId],
            [
                'user_id' => $userId,
                'craftsman_id' => $craftsmanId,
                'source' => $this->getSource(),
                'medium' => $this->getMedium(),
                'campaign' => request()->get('utm_campaign') ?? session('utm_campaign'),
            ]
        );

        // Update user/craftsman if not set
        if ($userId && !$funnel->user_id) {
            $funnel->user_id = $userId;
        }
        if ($craftsmanId && !$funnel->craftsman_id) {
            $funnel->craftsman_id = $craftsmanId;
        }

        // Map event type to funnel stage
        $stageMap = [
            ConversionEvent::TYPE_PAGE_VIEW => 'visited_at',
            ConversionEvent::TYPE_PROFILE_VIEW => 'profile_viewed_at',
            ConversionEvent::TYPE_CONTACT_CLICK => 'contact_clicked_at',
            ConversionEvent::TYPE_PHONE_REVEAL => 'contact_clicked_at',
            ConversionEvent::TYPE_MESSAGE_SENT => 'message_sent_at',
            ConversionEvent::TYPE_QUOTE_REQUEST => 'quote_requested_at',
            ConversionEvent::TYPE_QUOTE_RECEIVED => 'quote_received_at',
            ConversionEvent::TYPE_QUOTE_ACCEPTED => 'quote_accepted_at',
            ConversionEvent::TYPE_APPOINTMENT_BOOKED => 'appointment_booked_at',
            ConversionEvent::TYPE_REVIEW_SUBMITTED => 'review_submitted_at',
        ];

        if (isset($stageMap[$eventType])) {
            $field = $stageMap[$eventType];
            if (!$funnel->{$field}) {
                $funnel->{$field} = now();
            }
        }

        // Check if converted
        if ($this->isConversionEvent($eventType)) {
            $funnel->final_status = ConversionFunnel::STATUS_CONVERTED;
            if ($conversionValue) {
                $funnel->total_value = ($funnel->total_value ?? 0) + $conversionValue;
            }
        }

        $funnel->save();
    }

    /**
     * Update platform daily stats
     */
    protected function updateDailyStats(string $eventType): void
    {
        $dailyStat = PlatformDailyStat::today();

        $counterMap = [
            ConversionEvent::TYPE_PAGE_VIEW => 'page_views',
            ConversionEvent::TYPE_PROFILE_VIEW => 'profile_views',
            ConversionEvent::TYPE_MESSAGE_SENT => 'messages_sent',
            ConversionEvent::TYPE_QUOTE_REQUEST => 'quote_requests',
            ConversionEvent::TYPE_QUOTE_RECEIVED => 'quotes_sent',
            ConversionEvent::TYPE_QUOTE_ACCEPTED => 'quotes_accepted',
            ConversionEvent::TYPE_APPOINTMENT_BOOKED => 'appointments_booked',
            ConversionEvent::TYPE_REVIEW_SUBMITTED => 'reviews_submitted',
        ];

        if (isset($counterMap[$eventType])) {
            $dailyStat->incrementCounter($counterMap[$eventType]);
        }

        // Track unique visitors
        if ($eventType === ConversionEvent::TYPE_PAGE_VIEW) {
            $this->trackVisitor($dailyStat);
        }

        // Track new registrations
        if ($eventType === ConversionEvent::TYPE_REGISTRATION) {
            $dailyStat->incrementCounter('new_registrations');
        }
    }

    /**
     * Track unique visitor
     */
    protected function trackVisitor(PlatformDailyStat $dailyStat): void
    {
        $sessionId = $this->getSessionId();
        $visitorKey = 'visitor_' . date('Y-m-d') . '_' . md5($sessionId);

        if (!cache()->has($visitorKey)) {
            cache()->put($visitorKey, true, now()->endOfDay());
            $dailyStat->incrementCounter('unique_visitors');
            
            // Also track device type
            $deviceBreakdown = $dailyStat->device_breakdown ?? [];
            $deviceType = $this->getDeviceType();
            $deviceBreakdown[$deviceType] = ($deviceBreakdown[$deviceType] ?? 0) + 1;
            $dailyStat->update(['device_breakdown' => $deviceBreakdown]);
        }

        $dailyStat->incrementCounter('total_visits');
    }

    /**
     * Get funnel statistics
     */
    public function getFunnelStats($startDate = null, $endDate = null, ?int $craftsmanId = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        $query = ConversionFunnel::whereBetween('created_at', [$startDate, $endDate]);
        
        if ($craftsmanId) {
            $query->where('craftsman_id', $craftsmanId);
        }

        $funnels = $query->get();
        $total = $funnels->count();

        if ($total === 0) {
            return $this->getEmptyFunnelStats();
        }

        return [
            'total_sessions' => $total,
            'stages' => [
                [
                    'name' => 'Vizită',
                    'count' => $funnels->whereNotNull('visited_at')->count(),
                    'percentage' => 100,
                ],
                [
                    'name' => 'Profil Vizualizat',
                    'count' => $count = $funnels->whereNotNull('profile_viewed_at')->count(),
                    'percentage' => round(($count / $total) * 100, 1),
                ],
                [
                    'name' => 'Contact/Click',
                    'count' => $count = $funnels->whereNotNull('contact_clicked_at')->count(),
                    'percentage' => round(($count / $total) * 100, 1),
                ],
                [
                    'name' => 'Mesaj Trimis',
                    'count' => $count = $funnels->whereNotNull('message_sent_at')->count(),
                    'percentage' => round(($count / $total) * 100, 1),
                ],
                [
                    'name' => 'Cerere Ofertă',
                    'count' => $count = $funnels->whereNotNull('quote_requested_at')->count(),
                    'percentage' => round(($count / $total) * 100, 1),
                ],
                [
                    'name' => 'Ofertă Primită',
                    'count' => $count = $funnels->whereNotNull('quote_received_at')->count(),
                    'percentage' => round(($count / $total) * 100, 1),
                ],
                [
                    'name' => 'Ofertă Acceptată',
                    'count' => $count = $funnels->whereNotNull('quote_accepted_at')->count(),
                    'percentage' => round(($count / $total) * 100, 1),
                ],
                [
                    'name' => 'Programare',
                    'count' => $count = $funnels->whereNotNull('appointment_booked_at')->count(),
                    'percentage' => round(($count / $total) * 100, 1),
                ],
            ],
            'conversion_rate' => round(
                ($funnels->where('final_status', ConversionFunnel::STATUS_CONVERTED)->count() / $total) * 100, 
                2
            ),
            'total_value' => $funnels->sum('total_value'),
        ];
    }

    /**
     * Get empty funnel stats structure
     */
    protected function getEmptyFunnelStats(): array
    {
        return [
            'total_sessions' => 0,
            'stages' => [
                ['name' => 'Vizită', 'count' => 0, 'percentage' => 0],
                ['name' => 'Profil Vizualizat', 'count' => 0, 'percentage' => 0],
                ['name' => 'Contact/Click', 'count' => 0, 'percentage' => 0],
                ['name' => 'Mesaj Trimis', 'count' => 0, 'percentage' => 0],
                ['name' => 'Cerere Ofertă', 'count' => 0, 'percentage' => 0],
                ['name' => 'Ofertă Primită', 'count' => 0, 'percentage' => 0],
                ['name' => 'Ofertă Acceptată', 'count' => 0, 'percentage' => 0],
                ['name' => 'Programare', 'count' => 0, 'percentage' => 0],
            ],
            'conversion_rate' => 0,
            'total_value' => 0,
        ];
    }

    /**
     * Get traffic sources breakdown
     */
    public function getTrafficSources($startDate = null, $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        return ConversionEvent::select('source', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('event_type', ConversionEvent::TYPE_PAGE_VIEW)
            ->whereNotNull('source')
            ->groupBy('source')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Get device breakdown
     */
    public function getDeviceBreakdown($startDate = null, $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        return ConversionEvent::select('device_type', DB::raw('count(distinct session_id) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('device_type')
            ->groupBy('device_type')
            ->get()
            ->toArray();
    }

    /**
     * Get top converting pages
     */
    public function getTopConvertingPages($startDate = null, $endDate = null, int $limit = 10): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        return ConversionEvent::select('landing_page', DB::raw('count(*) as conversions'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('converted_at')
            ->whereNotNull('landing_page')
            ->groupBy('landing_page')
            ->orderByDesc('conversions')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get craftsman conversion stats
     */
    public function getCraftsmanStats(int $craftsmanId, $startDate = null, $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        $events = ConversionEvent::where('craftsman_id', $craftsmanId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $profileViews = $events->where('event_type', ConversionEvent::TYPE_PROFILE_VIEW)->count();
        $contactClicks = $events->where('event_type', ConversionEvent::TYPE_CONTACT_CLICK)->count();
        $phoneReveals = $events->where('event_type', ConversionEvent::TYPE_PHONE_REVEAL)->count();
        $messagesSent = $events->where('event_type', ConversionEvent::TYPE_MESSAGE_SENT)->count();
        $quoteRequests = $events->where('event_type', ConversionEvent::TYPE_QUOTE_REQUEST)->count();
        $quotesAccepted = $events->where('event_type', ConversionEvent::TYPE_QUOTE_ACCEPTED)->count();
        $appointments = $events->where('event_type', ConversionEvent::TYPE_APPOINTMENT_BOOKED)->count();

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'metrics' => [
                'profile_views' => $profileViews,
                'contact_clicks' => $contactClicks,
                'phone_reveals' => $phoneReveals,
                'messages_sent' => $messagesSent,
                'quote_requests' => $quoteRequests,
                'quotes_accepted' => $quotesAccepted,
                'appointments' => $appointments,
            ],
            'conversion_rates' => [
                'view_to_contact' => $profileViews > 0 ? round(($contactClicks / $profileViews) * 100, 2) : 0,
                'contact_to_quote' => $contactClicks > 0 ? round(($quoteRequests / $contactClicks) * 100, 2) : 0,
                'quote_to_booking' => $quoteRequests > 0 ? round(($quotesAccepted / $quoteRequests) * 100, 2) : 0,
            ],
            'funnel' => $this->getFunnelStats($startDate, $endDate, $craftsmanId),
        ];
    }
}
