<?php

namespace App\Http\Controllers\Craftsman;

use App\Http\Controllers\Controller;
use App\Models\DailyStat;
use App\Models\ProfileView;
use App\Models\QuoteRequest;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnalyticsController extends Controller
{
    /**
     * Display the analytics dashboard.
     */
    public function index(Request $request)
    {
        $craftsman = auth()->user();
        $period = $request->get('period', '30'); // days
        
        $startDate = now()->subDays((int) $period)->startOfDay();
        $endDate = now()->endOfDay();
        
        // Get aggregated stats
        $stats = DailyStat::getStatsForPeriod($craftsman->id, $startDate->toDateString(), $endDate->toDateString());
        
        // Get comparison with previous period
        $prevStartDate = $startDate->copy()->subDays((int) $period);
        $prevEndDate = $startDate->copy()->subDay();
        $prevStats = DailyStat::getStatsForPeriod($craftsman->id, $prevStartDate->toDateString(), $prevEndDate->toDateString());
        
        // Calculate growth percentages
        $growth = $this->calculateGrowth($stats, $prevStats);
        
        // Get chart data
        $chartData = $this->prepareChartData($craftsman->id, $startDate, $endDate);
        
        // Get traffic sources
        $trafficSources = ProfileView::forCraftsman($craftsman->id)
            ->betweenDates($startDate, $endDate)
            ->selectRaw('source, COUNT(*) as count')
            ->groupBy('source')
            ->orderByDesc('count')
            ->get();
        
        // Top performing services
        $topServices = $craftsman->services()
            ->withCount(['appointments' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->orderByDesc('appointments_count')
            ->take(5)
            ->get();
        
        // Recent reviews
        $recentReviews = $craftsman->reviews()
            ->where('is_approved', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->take(5)
            ->get();
        
        return view('craftsman.analytics.index', compact(
            'stats',
            'prevStats',
            'growth',
            'chartData',
            'trafficSources',
            'topServices',
            'recentReviews',
            'period'
        ));
    }

    /**
     * Calculate growth percentages.
     */
    private function calculateGrowth(array $current, array $previous): array
    {
        $growth = [];
        $metrics = ['profile_views', 'quote_requests', 'bookings', 'messages_received'];
        
        foreach ($metrics as $metric) {
            $currentVal = $current[$metric] ?? 0;
            $previousVal = $previous[$metric] ?? 0;
            
            if ($previousVal > 0) {
                $growth[$metric] = round((($currentVal - $previousVal) / $previousVal) * 100, 1);
            } else {
                $growth[$metric] = $currentVal > 0 ? 100 : 0;
            }
        }
        
        return $growth;
    }

    /**
     * Prepare chart data.
     */
    private function prepareChartData(int $userId, Carbon $startDate, Carbon $endDate): array
    {
        $stats = DailyStat::forUser($userId)
            ->betweenDates($startDate->toDateString(), $endDate->toDateString())
            ->orderBy('date')
            ->get()
            ->keyBy(fn($item) => $item->date->format('Y-m-d'));
        
        $labels = [];
        $profileViews = [];
        $quoteRequests = [];
        $bookings = [];
        
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateKey = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('d M');
            
            $dayStat = $stats->get($dateKey);
            $profileViews[] = $dayStat?->profile_views ?? 0;
            $quoteRequests[] = $dayStat?->quote_requests ?? 0;
            $bookings[] = $dayStat?->bookings ?? 0;
            
            $currentDate->addDay();
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Vizualizări profil',
                    'data' => $profileViews,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
                [
                    'label' => 'Cereri ofertă',
                    'data' => $quoteRequests,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
                [
                    'label' => 'Programări',
                    'data' => $bookings,
                    'borderColor' => '#8b5cf6',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                ],
            ],
        ];
    }

    /**
     * Export analytics report.
     */
    public function export(Request $request)
    {
        $craftsman = auth()->user();
        $period = $request->get('period', '30');
        $format = $request->get('format', 'csv');
        
        $startDate = now()->subDays((int) $period)->startOfDay();
        $endDate = now()->endOfDay();
        
        $stats = DailyStat::forUser($craftsman->id)
            ->betweenDates($startDate->toDateString(), $endDate->toDateString())
            ->orderBy('date')
            ->get();
        
        if ($format === 'csv') {
            return $this->exportCsv($stats, $craftsman->name);
        }
        
        return back()->with('error', 'Format nesuportat.');
    }

    /**
     * Export as CSV.
     */
    private function exportCsv($stats, string $craftsmanName)
    {
        $filename = 'analytics-' . Str::slug($craftsmanName) . '-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($stats) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Data',
                'Vizualizări profil',
                'Vizualizări servicii',
                'Click-uri contact',
                'Cereri ofertă',
                'Programări',
                'Mesaje primite',
                'Recenzii primite',
                'Rating mediu',
            ]);
            
            // Data rows
            foreach ($stats as $stat) {
                fputcsv($file, [
                    $stat->date->format('d.m.Y'),
                    $stat->profile_views,
                    $stat->service_views,
                    $stat->contact_clicks,
                    $stat->quote_requests,
                    $stat->bookings,
                    $stat->messages_received,
                    $stat->reviews_received,
                    $stat->avg_rating ?? '-',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
