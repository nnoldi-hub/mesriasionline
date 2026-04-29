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
use App\Services\ConversionTrackingService;
use App\Services\ReportExportService;
use App\Exports\PlatformReportExport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class AnalyticsController extends Controller
{
    protected ConversionTrackingService $trackingService;
    protected ReportExportService $reportService;

    public function __construct(ConversionTrackingService $trackingService, ReportExportService $reportService)
    {
        $this->trackingService = $trackingService;
        $this->reportService = $reportService;
    }

    /**
     * Main analytics dashboard
     */
    public function index(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays((int)$period);
        $endDate = now();

        // Get aggregated stats
        $stats = PlatformDailyStat::getAggregatedStats($startDate, $endDate);

        // Get chart data
        $visitsChart = PlatformDailyStat::getVisitsChartData($startDate, $endDate);
        $conversionsChart = PlatformDailyStat::getConversionsChartData($startDate, $endDate);

        // Get funnel stats
        $funnelStats = $this->trackingService->getFunnelStats($startDate, $endDate);

        // Get traffic sources
        $trafficSources = $this->trackingService->getTrafficSources($startDate, $endDate);

        // Get device breakdown
        $deviceBreakdown = $this->trackingService->getDeviceBreakdown($startDate, $endDate);

        // User counts
        $userStats = [
            'total_craftsmen' => User::where('role', 'specialist')->count(),
            'total_clients' => User::where('role', 'client')->count(),
            'active_craftsmen' => User::where('role', 'specialist')
                ->where('last_login_at', '>=', now()->subDays(30))
                ->count(),
            'verified_craftsmen' => User::where('role', 'specialist')
                ->whereNotNull('email_verified_at')
                ->count(),
        ];

        // Top performers
        $topCraftsmen = User::where('role', 'specialist')
            ->withCount(['reviews'])
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_count')
            ->limit(10)
            ->get();

        // Top categories
        $topCategories = Category::withCount('users')
            ->orderByDesc('users_count')
            ->limit(10)
            ->get();

        // Recent activity
        $recentRegistrations = User::latest()->limit(10)->get();
        $recentReviews = Review::with(['user', 'specialist'])->latest()->limit(10)->get();

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

        $funnelStats = $this->trackingService->getFunnelStats($startDate, $endDate);

        // Get daily funnel data for chart
        $dailyFunnels = ConversionFunnel::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN final_status = "converted" THEN 1 ELSE 0 END) as converted')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Drop-off analysis
        $dropOffAnalysis = $this->calculateDropOffRates($funnelStats);

        // Top converting sources
        $convertingSources = ConversionFunnel::where('final_status', 'converted')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('source, COUNT(*) as conversions')
            ->whereNotNull('source')
            ->groupBy('source')
            ->orderByDesc('conversions')
            ->limit(10)
            ->get();

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

        $trafficSources = $this->trackingService->getTrafficSources($startDate, $endDate);
        $deviceBreakdown = $this->trackingService->getDeviceBreakdown($startDate, $endDate);
        $topPages = $this->trackingService->getTopConvertingPages($startDate, $endDate);

        // Daily traffic data
        $dailyTraffic = PlatformDailyStat::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get(['date', 'total_visits', 'unique_visitors', 'page_views']);

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
        $activeUsers = User::where('last_login_at', '>=', now()->subDays(7))->count();
        $inactiveUsers = User::where('last_login_at', '<', now()->subDays(30))->count();

        // New vs returning
        $newUsers = User::whereBetween('created_at', [$startDate, $endDate])->count();

        // Verification rates
        $verificationRate = User::whereNotNull('email_verified_at')->count() / max(User::count(), 1) * 100;

        // Top locations
        $topLocations = User::whereNotNull('city')
            ->selectRaw('city, COUNT(*) as count')
            ->groupBy('city')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

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

        $pdf = $this->reportService->generatePlatformReport($startDate, $endDate);

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

        return Excel::download(new PlatformReportExport($startDate, $endDate), $filename);
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
