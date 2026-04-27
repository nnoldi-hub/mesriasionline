<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SuspiciousActivityDetector;

class DetectSuspiciousActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $detector = new SuspiciousActivityDetector($request);

        // Check if IP is blocked
        if ($detector->isBlocked()) {
            return response()->view('errors.blocked', [], 403);
        }

        // Check for SQL injection attempts
        if ($detector->checkSqlInjection()) {
            return response()->view('errors.blocked', [], 403);
        }

        // Check for XSS attempts
        if ($detector->checkXss()) {
            return response()->view('errors.blocked', [], 403);
        }

        // Check for bot behavior (only log, don't block)
        $detector->checkBotBehavior();

        // Check for rapid submissions on POST requests
        if ($request->isMethod('post')) {
            $formType = $request->segment(1) ?? 'unknown';
            $detector->checkRapidSubmission($formType);
        }

        return $next($request);
    }
}
