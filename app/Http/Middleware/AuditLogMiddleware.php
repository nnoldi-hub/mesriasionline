<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    /**
     * Actions that should be logged
     */
    protected array $loggedMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * Routes to skip logging
     */
    protected array $skipRoutes = [
        'sanctum/*',
        '_ignition/*',
        'livewire/*',
        'broadcasting/*',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log for authenticated users making mutations
        if (auth()->check() && 
            in_array($request->method(), $this->loggedMethods) &&
            !$this->shouldSkip($request) &&
            $response->isSuccessful()
        ) {
            $this->logAction($request);
        }

        return $response;
    }

    /**
     * Check if route should be skipped
     */
    protected function shouldSkip(Request $request): bool
    {
        $path = $request->path();

        foreach ($this->skipRoutes as $pattern) {
            if (fnmatch($pattern, $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Log the action
     */
    protected function logAction(Request $request): void
    {
        $action = $this->determineAction($request);
        
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);
    }

    /**
     * Determine action from request
     */
    protected function determineAction(Request $request): string
    {
        $path = $request->path();
        $method = $request->method();

        // Check for specific patterns
        if (str_contains($path, 'login')) {
            return AuditLog::ACTION_LOGIN;
        }

        if (str_contains($path, 'logout')) {
            return AuditLog::ACTION_LOGOUT;
        }

        if (str_contains($path, 'export')) {
            return AuditLog::ACTION_EXPORT;
        }

        if (str_contains($path, 'import')) {
            return AuditLog::ACTION_IMPORT;
        }

        // Default based on HTTP method
        return match($method) {
            'POST' => AuditLog::ACTION_CREATE,
            'PUT', 'PATCH' => AuditLog::ACTION_UPDATE,
            'DELETE' => AuditLog::ACTION_DELETE,
            default => 'unknown',
        };
    }
}
