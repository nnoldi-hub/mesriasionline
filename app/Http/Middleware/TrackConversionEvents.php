<?php

namespace App\Http\Middleware;

use App\Models\ConversionEvent;
use App\Services\ConversionTrackingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackConversionEvents
{
    protected ConversionTrackingService $trackingService;

    public function __construct(ConversionTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Store UTM parameters in session
        $this->storeUtmParameters($request);

        // Track page view for non-API, non-admin routes
        if ($this->shouldTrackPageView($request)) {
            $this->trackingService->trackEvent(
                ConversionEvent::TYPE_PAGE_VIEW,
                auth()->id(),
                null,
                [
                    'url' => $request->fullUrl(),
                    'route' => $request->route()?->getName(),
                ]
            );
        }

        return $next($request);
    }

    /**
     * Store UTM parameters in session for attribution
     */
    protected function storeUtmParameters(Request $request): void
    {
        $utmParams = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
        
        foreach ($utmParams as $param) {
            if ($request->has($param)) {
                session([$param => $request->get($param)]);
            }
        }
    }

    /**
     * Determine if we should track this page view
     */
    protected function shouldTrackPageView(Request $request): bool
    {
        // Don't track AJAX requests
        if ($request->ajax()) {
            return false;
        }

        // Don't track API routes
        if ($request->is('api/*')) {
            return false;
        }

        // Don't track admin routes (they have their own tracking)
        if ($request->is('admin/*')) {
            return false;
        }

        // Don't track certain paths
        $excludedPaths = [
            'livewire/*',
            '_debugbar/*',
            'sanctum/*',
            'broadcasting/*',
        ];

        foreach ($excludedPaths as $path) {
            if ($request->is($path)) {
                return false;
            }
        }

        // Only track GET requests
        if (!$request->isMethod('GET')) {
            return false;
        }

        return true;
    }
}
