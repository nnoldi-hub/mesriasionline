<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Quote;
use App\Models\Review;
use App\Models\Category;
use App\Models\PlatformDailyStat;
use App\Models\ConversionFunnel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AnalyticsController extends Controller
{
    protected function trackingService()
    {
        try {
            return app(\App\Services\ConversionTrackingService::class);
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function reportService()
    {
        try {
            return app(\App\Services\ReportExportService::class);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Main analytics dashboard
     */
    public function index(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays((int)$period);
        $endDate = now();

        $emptyChartData = ['labels' => [], 'datasets' => []];
        $emptyFunnel = ['total_sessions' => 0, 'stages' => [], 'conversion_rate' => 0, 'total_value' => 0];
        $emptyStats = [
            'total_visits' => 0, 'unique_visitors' => 0, 'page_views' => 0,
            'new_registrations' => 0, 'new_craftsmen' => 0, 'new_clients' => 0,
            'profile_views' => 0, 'messages_sent' => 0, 'quote_requests' => 0,
            'quotes_sent' => 0, 'quotes_accepted' => 0, 'appointments_booked' => 0,
            'reviews_submitted' => 0, 'total_revenue' => 0, 'affiliate_commissions' => 0,
            'avg_visit_to_contact_rate' => 0, 'avg_contact_to_quote_rate' => 0,
            'avg_quote_to_booking_rate' => 0,
        ];

        try { $stats = PlatformDailyStat::getAggregatedStats($startDate, $endDate); }
        catch (\Throwable $e) { $stats = $emptyStats; }

        try { $visitsChart = PlatformDailyStat::getVisitsChartData($startDate, $endDate); }
        catch (\Throwable $e) { $visitsChart = $emptyChartData; }

        try { $conversionsChart = PlatformDailyStat::getConversionsChartData($startDate, $endDate); }
        catch (\Throwable $e) { $conversionsChart = $emptyChartData; }

        try { $ts = $this->trackingService(); $funnelStats = $ts ? $ts->getFunnelStats($startDate, $endDate) : $emptyFunnel; }
        catch (\Throwable $e) { $funnelStats = $emptyFunnel; }

        try { $ts = $this->trackingService(); $trafficSources = $ts ? $ts->getTrafficSources($startDate, $endDate) : []; }
        catch (\Throwable $e) { $trafficSources = []; }

        try { $ts = $this->trackingService(); $deviceBreakdown = $ts ? $ts->getDeviceBreakdown($startDate, $endDate) : []; }
        catch (\Throwable $e) { $deviceBreakdown = []; }

        // User counts
        $userStats = [
            'total_craftsmen' => User::where('role', 'specialist')->count(),
            'total_clients' => User::where('role', 'client')->count(),
            'active_craftsmen' => User::where('role', 'specialist')->where('is_active', true)->count(),
            'verified_craftsmen' => User::where('role', 'specialist')->whereNotNull('email_verified_at')->count(),
        ];

        // Top performers
        try {
            $topCraftsmen = User::where('role', 'specialist')
                ->withCount(['reviews'])
                ->withAvg('reviews', 'rating')
                ->orderByDesc('reviews_count')
                ->limit(10)
                ->get();
        } catch (\Throwable $e) {
            $topCraftsmen = collect();
        }

        // Top categories
        try {
            $topCategories = Category::withCount('users')->orderByDesc('users_count')->limit(10)->get();
        } catch (\Throwable $e) {
            $topCategories = collect();
        }

        // Recent activity
        $recentRegistrations = User::latest()->limit(10)->get();

        try {
            $recentReviews = Review::with(['user', 'specialist'])->latest()->limit(10)->get();
        } catch (\Throwable $e) {
            $recentReviews = collect();
        }

        return view('admin.analytics.index', compact(
            'period',
            'stats',
            'visitsChart',
            'conversionsChart',
            'funnelStats',
            'trafficSources',
            'deviceBreakdown',
            'userStats',
            'topCraftsmen',
            'topCategories',
            'recentRegistrations',
            'recentReviews'
        ));
    }

    /**
     * Conversion funnel analysis
     */
    public function funnel(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays((int)$period);
        $endDate = now();

        $emptyFunnel = ['total_sessions' => 0, 'stages' => [], 'conversion_rate' => 0, 'total_value' => 0];
        try { $ts = $this->trackingService(); $funnelStats = $ts ? $ts->getFunnelStats($startDate, $endDate) : $emptyFunnel; }
        catch (\Throwable $e) { $funnelStats = $emptyFunnel; }

        // Get daily funnel data for chart
        try {
            $dailyFunnels = ConversionFunnel::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as date')
                ->selectRaw('COUNT(*) as total')
                ->selectRaw('SUM(CASE WHEN final_status = "converted" THEN 1 ELSE 0 END) as converted')
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } catch (\Throwable $e) { $dailyFunnels = collect(); }

        // Drop-off analysis
        $dropOffAnalysis = $this->calculateDropOffRates($funnelStats);

        // Top converting sources
        try {
            $convertingSources = ConversionFunnel::where('final_status', 'converted')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('source, COUNT(*) as conversions')
                ->whereNotNull('source')
                ->groupBy('source')
                ->orderByDesc('conversions')
                ->limit(10)
                ->get();
        } catch (\Throwable $e) { $convertingSources = collect(); }

        return view('admin.analytics.funnel', compact(
            'period',
            'funnelStats',
            'dailyFunnels',
            'dropOffAnalysis',
            'convertingSources'
        ));
    }

    /**
     * Traffic analysis
     */
    public function traffic(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays((int)$period);
        $endDate = now();

        try { $ts = $this->trackingService(); $trafficSources = $ts ? $ts->getTrafficSources($startDate, $endDate) : []; }
        catch (\Throwable $e) { $trafficSources = []; }
        try { $ts = $this->trackingService(); $deviceBreakdown = $ts ? $ts->getDeviceBreakdown($startDate, $endDate) : []; }
        catch (\Throwable $e) { $deviceBreakdown = []; }
        try { $ts = $this->trackingService(); $topPages = $ts ? $ts->getTopConvertingPages($startDate, $endDate) : []; }
        catch (\Throwable $e) { $topPages = []; }

        // Daily traffic data
        try {
            $dailyTraffic = PlatformDailyStat::whereBetween('date', [$startDate, $endDate])
                ->orderBy('date')
                ->get(['date', 'total_visits', 'unique_visitors', 'page_views']);
        } catch (\Throwable $e) { $dailyTraffic = collect(); }

        return view('admin.analytics.traffic', compact(
            'period',
            'trafficSources',
            'deviceBreakdown',
            'topPages',
            'dailyTraffic'
        ));
    }

    /**
     * User analytics
     */
    public function users(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays((int)$period);
        $endDate = now();

        // Registration trends
        $registrationTrends = User::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw('role')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('date', 'role')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        // User activity
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();

        // New vs returning
        $newUsers = User::whereBetween('created_at', [$startDate, $endDate])->count();

        // Verification rates
        $verificationRate = User::whereNotNull('email_verified_at')->count() / max(User::count(), 1) * 100;

        // Top locations
        try {
            $topLocations = User::whereNotNull('city')
                ->selectRaw('city, COUNT(*) as count')
                ->groupBy('city')
                ->orderByDesc('count')
                ->limit(10)
                ->get();
        } catch (\Throwable $e) { $topLocations = collect(); }

        return view('admin.analytics.users', compact(
            'period',
            'registrationTrends',
            'activeUsers',
            'inactiveUsers',
            'newUsers',
            'verificationRate',
            'topLocations'
        ));
    }

    /**
     * Export platform report as PDF
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $rs = $this->reportService();
        if (!$rs) { abort(503, 'Export service unavailable'); }
        $pdf = $rs->generatePlatformReport($startDate, $endDate);

        return $pdf->download('raport-platforma-' . $startDate->format('Y-m-d') . '-' . $endDate->format('Y-m-d') . '.pdf');
    }

    /**
     * Export platform report as Excel
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $filename = 'raport-platforma-' . $startDate->format('Y-m-d') . '-' . $endDate->format('Y-m-d') . '.xlsx';

        try {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PlatformReportExport($startDate, $endDate), $filename);
        } catch (\Throwable $e) {
            abort(503, 'Export service unavailable');
        }
    }

    /**
     * Calculate drop-off rates between funnel stages
     */
    protected function calculateDropOffRates(array $funnelStats): array
    {
        $stages = $funnelStats['stages'];
        $dropOffs = [];

        for ($i = 0; $i < count($stages) - 1; $i++) {
            $current = $stages[$i];
            $next = $stages[$i + 1];

            $dropOffRate = $current['count'] > 0 
                ? round((($current['count'] - $next['count']) / $current['count']) * 100, 1)
                : 0;

            $dropOffs[] = [
                'from' => $current['name'],
                'to' => $next['name'],
                'drop_off_rate' => $dropOffRate,
                'lost_users' => $current['count'] - $next['count'],
            ];
        }

        return $dropOffs;
    }
}
