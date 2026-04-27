<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class CustomRateLimiter
{
    /**
     * Rate limit configurations per route pattern.
     */
    protected array $rateLimits = [
        // Authentication
        'login' => ['attempts' => 5, 'decay' => 60],
        'register' => ['attempts' => 3, 'decay' => 60],
        'password.email' => ['attempts' => 3, 'decay' => 120],
        
        // API endpoints
        'api.*' => ['attempts' => 60, 'decay' => 60],
        
        // Contact & Messages
        'messages.store' => ['attempts' => 10, 'decay' => 60],
        'contact.send' => ['attempts' => 5, 'decay' => 300],
        
        // Reviews
        'reviews.store' => ['attempts' => 5, 'decay' => 300],
        
        // Quote requests
        'quotes.store' => ['attempts' => 10, 'decay' => 300],
        
        // Search
        'search' => ['attempts' => 30, 'decay' => 60],
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $limiter = null): Response
    {
        $key = $this->resolveRequestSignature($request);
        $config = $this->getConfig($request, $limiter);

        if (RateLimiter::tooManyAttempts($key, $config['attempts'])) {
            return $this->buildResponse($request, $key, $config);
        }

        RateLimiter::hit($key, $config['decay']);

        $response = $next($request);

        return $this->addHeaders(
            $response,
            $config['attempts'],
            RateLimiter::remaining($key, $config['attempts'])
        );
    }

    /**
     * Resolve the request signature for rate limiting.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        $routeName = $request->route()?->getName() ?? $request->path();
        
        if ($request->user()) {
            return sha1($routeName . '|' . $request->user()->id);
        }

        return sha1($routeName . '|' . $request->ip());
    }

    /**
     * Get rate limit config for the current request.
     */
    protected function getConfig(Request $request, ?string $limiter): array
    {
        // If a specific limiter is provided
        if ($limiter && isset($this->rateLimits[$limiter])) {
            return $this->rateLimits[$limiter];
        }

        $routeName = $request->route()?->getName();

        // Check exact match
        if ($routeName && isset($this->rateLimits[$routeName])) {
            return $this->rateLimits[$routeName];
        }

        // Check pattern match
        foreach ($this->rateLimits as $pattern => $config) {
            if (str_contains($pattern, '*')) {
                $regex = '/^' . str_replace('*', '.*', preg_quote($pattern, '/')) . '$/';
                if ($routeName && preg_match($regex, $routeName)) {
                    return $config;
                }
            }
        }

        // Default rate limit
        return ['attempts' => 60, 'decay' => 60];
    }

    /**
     * Build the rate limit exceeded response.
     */
    protected function buildResponse(Request $request, string $key, array $config): Response
    {
        $retryAfter = RateLimiter::availableIn($key);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Prea multe încercări. Încearcă din nou în ' . ceil($retryAfter / 60) . ' minute.',
                'retry_after' => $retryAfter,
            ], 429);
        }

        return response()->view('errors.429', [
            'retryAfter' => $retryAfter,
        ], 429);
    }

    /**
     * Add rate limit headers to the response.
     */
    protected function addHeaders(Response $response, int $maxAttempts, int $remaining): Response
    {
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', $remaining);
        
        return $response;
    }
}
