<?php

namespace App\Services;

use App\Models\User;
use App\Models\Appointment;
use App\Models\Quote;
use App\Models\Review;
use App\Models\ConversionFunnel;
use App\Models\PlatformDailyStat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class ReportExportService
{
    /**
     * Generate craftsman performance PDF report
     */
    public function generateCraftsmanReport(User $craftsman, $startDate, $endDate): \Barryvdh\DomPDF\PDF
    {
        $data = $this->getCraftsmanReportData($craftsman, $startDate, $endDate);
        
        return Pdf::loadView('reports.pdf.craftsman-report', $data)
            ->setPaper('a4', 'portrait');
    }

    /**
     * Generate admin platform report PDF
     */
    public function generatePlatformReport($startDate, $endDate): \Barryvdh\DomPDF\PDF
    {
        $data = $this->getPlatformReportData($startDate, $endDate);
        
        return Pdf::loadView('reports.pdf.platform-report', $data)
            ->setPaper('a4', 'portrait');
    }

    /**
     * Generate client activity report PDF
     */
    public function generateClientReport(User $client, $startDate, $endDate): \Barryvdh\DomPDF\PDF
    {
        $data = $this->getClientReportData($client, $startDate, $endDate);
        
        return Pdf::loadView('reports.pdf.client-report', $data)
            ->setPaper('a4', 'portrait');
    }

    /**
     * Get craftsman report data
     */
    public function getCraftsmanReportData(User $craftsman, $startDate, $endDate): array
    {
        $appointments = Appointment::where('meserias_id', $craftsman->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $quotes = Quote::where('meserias_id', $craftsman->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $reviews = Review::where('meserias_id', $craftsman->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Get conversion tracking service stats
        $trackingService = app(ConversionTrackingService::class);
        $conversionStats = $trackingService->getCraftsmanStats($craftsman->id, $startDate, $endDate);

        return [
            'craftsman' => $craftsman,
            'period' => [
                'start' => Carbon::parse($startDate)->format('d.m.Y'),
                'end' => Carbon::parse($endDate)->format('d.m.Y'),
            ],
            'generated_at' => now()->format('d.m.Y H:i'),
            
            // Appointments
            'appointments' => [
                'total' => $appointments->count(),
                'completed' => $appointments->where('status', 'completed')->count(),
                'cancelled' => $appointments->where('status', 'cancelled')->count(),
                'pending' => $appointments->where('status', 'pending')->count(),
                'list' => $appointments->take(20),
            ],
            
            // Quotes
            'quotes' => [
                'total' => $quotes->count(),
                'accepted' => $quotes->where('status', 'accepted')->count(),
                'pending' => $quotes->where('status', 'pending')->count(),
                'rejected' => $quotes->where('status', 'rejected')->count(),
                'total_value' => $quotes->where('status', 'accepted')->sum('amount'),
                'list' => $quotes->take(20),
            ],
            
            // Reviews
            'reviews' => [
                'total' => $reviews->count(),
                'average_rating' => round($reviews->avg('rating'), 1),
                'rating_distribution' => [
                    5 => $reviews->where('rating', 5)->count(),
                    4 => $reviews->where('rating', 4)->count(),
                    3 => $reviews->where('rating', 3)->count(),
                    2 => $reviews->where('rating', 2)->count(),
                    1 => $reviews->where('rating', 1)->count(),
                ],
                'list' => $reviews->take(10),
            ],
            
            // Conversion stats
            'conversions' => $conversionStats,
        ];
    }

    /**
     * Get platform report data
     */
    public function getPlatformReportData($startDate, $endDate): array
    {
        $dailyStats = PlatformDailyStat::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        $aggregated = PlatformDailyStat::getAggregatedStats($startDate, $endDate);

        // User stats
        $newCraftsmen = User::where('user_type', 'meserias')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $newClients = User::where('user_type', 'client')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Top performers
        $topCraftsmen = User::where('user_type', 'meserias')
            ->withCount(['reviewsReceived' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withAvg(['reviewsReceived' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }], 'rating')
            ->orderByDesc('reviews_received_count')
            ->limit(10)
            ->get();

        // Funnel stats
        $trackingService = app(ConversionTrackingService::class);
        $funnelStats = $trackingService->getFunnelStats($startDate, $endDate);
        $trafficSources = $trackingService->getTrafficSources($startDate, $endDate);
        $deviceBreakdown = $trackingService->getDeviceBreakdown($startDate, $endDate);

        return [
            'period' => [
                'start' => Carbon::parse($startDate)->format('d.m.Y'),
                'end' => Carbon::parse($endDate)->format('d.m.Y'),
            ],
            'generated_at' => now()->format('d.m.Y H:i'),
            
            // Traffic
            'traffic' => [
                'total_visits' => $aggregated['total_visits'],
                'unique_visitors' => $aggregated['unique_visitors'],
                'page_views' => $aggregated['page_views'],
                'daily_data' => $dailyStats->map(fn($s) => [
                    'date' => $s->date->format('d.m'),
                    'visits' => $s->total_visits,
                    'unique' => $s->unique_visitors,
                ]),
            ],
            
            // Users
            'users' => [
                'new_registrations' => $aggregated['new_registrations'],
                'new_craftsmen' => $newCraftsmen,
                'new_clients' => $newClients,
                'total_craftsmen' => User::where('user_type', 'meserias')->count(),
                'total_clients' => User::where('user_type', 'client')->count(),
            ],
            
            // Engagement
            'engagement' => [
                'profile_views' => $aggregated['profile_views'],
                'messages_sent' => $aggregated['messages_sent'],
                'quote_requests' => $aggregated['quote_requests'],
                'quotes_sent' => $aggregated['quotes_sent'],
                'quotes_accepted' => $aggregated['quotes_accepted'],
                'appointments_booked' => $aggregated['appointments_booked'],
                'reviews_submitted' => $aggregated['reviews_submitted'],
            ],
            
            // Conversion funnel
            'funnel' => $funnelStats,
            
            // Traffic sources
            'traffic_sources' => $trafficSources,
            
            // Device breakdown
            'devices' => $deviceBreakdown,
            
            // Top performers
            'top_craftsmen' => $topCraftsmen,
        ];
    }

    /**
     * Get client report data
     */
    public function getClientReportData(User $client, $startDate, $endDate): array
    {
        $appointments = Appointment::where('user_id', $client->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('meserias')
            ->get();

        $quoteRequests = $client->quoteRequests()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('quotes')
            ->get();

        $reviews = Review::where('user_id', $client->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('meserias')
            ->get();

        return [
            'client' => $client,
            'period' => [
                'start' => Carbon::parse($startDate)->format('d.m.Y'),
                'end' => Carbon::parse($endDate)->format('d.m.Y'),
            ],
            'generated_at' => now()->format('d.m.Y H:i'),
            
            // Appointments
            'appointments' => [
                'total' => $appointments->count(),
                'completed' => $appointments->where('status', 'completed')->count(),
                'upcoming' => $appointments->where('status', 'confirmed')->count(),
                'list' => $appointments,
            ],
            
            // Quotes
            'quote_requests' => [
                'total' => $quoteRequests->count(),
                'quotes_received' => $quoteRequests->flatMap->quotes->count(),
                'quotes_accepted' => $quoteRequests->flatMap->quotes->where('status', 'accepted')->count(),
                'list' => $quoteRequests->take(20),
            ],
            
            // Reviews
            'reviews' => [
                'total' => $reviews->count(),
                'list' => $reviews,
            ],
        ];
    }

    /**
     * Get affiliate report data
     */
    public function getAffiliateReportData(User $user, $startDate, $endDate): array
    {
        $affiliate = $user->affiliate;
        
        if (!$affiliate) {
            return [
                'error' => 'Nu sunteți înregistrat ca afiliat',
            ];
        }

        $referrals = $affiliate->referrals()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('referredUser')
            ->get();

        $commissions = $affiliate->commissions()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $payouts = $affiliate->payouts()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        return [
            'affiliate' => $affiliate,
            'period' => [
                'start' => Carbon::parse($startDate)->format('d.m.Y'),
                'end' => Carbon::parse($endDate)->format('d.m.Y'),
            ],
            'generated_at' => now()->format('d.m.Y H:i'),
            
            // Referrals
            'referrals' => [
                'total_clicks' => $referrals->sum('clicks'),
                'registrations' => $referrals->where('status', 'converted')->count(),
                'conversion_rate' => $referrals->count() > 0 
                    ? round(($referrals->where('status', 'converted')->count() / $referrals->count()) * 100, 2)
                    : 0,
                'list' => $referrals,
            ],
            
            // Earnings
            'earnings' => [
                'total' => $commissions->sum('amount'),
                'pending' => $commissions->where('status', 'pending')->sum('amount'),
                'approved' => $commissions->where('status', 'approved')->sum('amount'),
                'paid' => $commissions->where('status', 'paid')->sum('amount'),
                'list' => $commissions->take(20),
            ],
            
            // Payouts
            'payouts' => [
                'total_requested' => $payouts->sum('amount'),
                'completed' => $payouts->where('status', 'completed')->sum('amount'),
                'pending' => $payouts->where('status', 'pending')->sum('amount'),
                'list' => $payouts,
            ],
        ];
    }
}
