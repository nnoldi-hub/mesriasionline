<?php

namespace App\Services;

use App\Models\SuspiciousActivity;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class SuspiciousActivityDetector
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Log a suspicious activity.
     */
    public function log(
        string $type,
        ?int $userId = null,
        ?string $details = null,
        ?array $additionalData = []
    ): SuspiciousActivity {
        $ip = $this->request->ip();
        $userAgent = $this->request->userAgent();

        // Count recent similar activities
        $recentCount = SuspiciousActivity::where('type', $type)
            ->where('ip_address', $ip)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        // Calculate risk score
        $riskScore = SuspiciousActivity::calculateRiskScore($type, $recentCount + 1);
        $severity = SuspiciousActivity::getSeverityFromScore($riskScore);

        // Create details array
        $detailsArray = array_merge([
            'message' => $details,
            'url' => $this->request->fullUrl(),
            'method' => $this->request->method(),
            'referer' => $this->request->header('referer'),
        ], $additionalData);

        // Log the activity
        $activity = SuspiciousActivity::create([
            'user_id' => $userId,
            'type' => $type,
            'severity' => $severity,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'details' => $detailsArray,
            'risk_score' => $riskScore,
        ]);

        // Check if should block
        if (SuspiciousActivity::shouldBlock($ip)) {
            $this->blockIp($ip, $severity);
        }

        // Log to system log if high severity
        if (in_array($severity, [SuspiciousActivity::SEVERITY_HIGH, SuspiciousActivity::SEVERITY_CRITICAL])) {
            Log::warning("Suspicious activity detected: {$type}", [
                'ip' => $ip,
                'user_id' => $userId,
                'severity' => $severity,
                'risk_score' => $riskScore,
            ]);
        }

        return $activity;
    }

    /**
     * Check for failed login attempts.
     */
    public function checkFailedLogin(string $email): bool
    {
        $ip = $this->request->ip();
        $cacheKey = "failed_login:{$ip}:{$email}";
        
        $attempts = Cache::get($cacheKey, 0);
        $attempts++;
        
        Cache::put($cacheKey, $attempts, now()->addMinutes(30));

        // Log if suspicious
        if ($attempts >= 3) {
            $this->log(
                SuspiciousActivity::TYPE_FAILED_LOGIN,
                null,
                "Multiple failed login attempts for email: {$email}",
                [
                    'email' => $email,
                    'attempts' => $attempts,
                ]
            );
        }

        // Block after 5 attempts
        if ($attempts >= 5) {
            $this->log(
                SuspiciousActivity::TYPE_BRUTE_FORCE,
                null,
                "Brute force attack detected for email: {$email}",
                [
                    'email' => $email,
                    'attempts' => $attempts,
                ]
            );
            return true; // Should block
        }

        return false;
    }

    /**
     * Check for rapid form submissions.
     */
    public function checkRapidSubmission(string $formType): bool
    {
        $ip = $this->request->ip();
        $cacheKey = "submission:{$formType}:{$ip}";
        
        $lastSubmission = Cache::get($cacheKey);
        
        if ($lastSubmission) {
            $secondsSinceLastSubmission = now()->diffInSeconds($lastSubmission);
            
            // Less than 5 seconds between submissions
            if ($secondsSinceLastSubmission < 5) {
                $this->log(
                    SuspiciousActivity::TYPE_RAPID_SUBMISSION,
                    auth()->id(),
                    "Rapid form submission detected",
                    [
                        'form_type' => $formType,
                        'seconds_since_last' => $secondsSinceLastSubmission,
                    ]
                );
                return true; // Suspicious
            }
        }

        Cache::put($cacheKey, now(), now()->addMinutes(5));
        return false;
    }

    /**
     * Check for unusual location (different country).
     */
    public function checkUnusualLocation(User $user): bool
    {
        $currentIp = $this->request->ip();
        $cacheKey = "user_location:{$user->id}";
        
        $lastKnownIp = Cache::get($cacheKey);
        
        if ($lastKnownIp && $lastKnownIp !== $currentIp) {
            // In a real app, you'd use a GeoIP service to check country
            // For now, we'll just log the IP change
            $this->log(
                SuspiciousActivity::TYPE_UNUSUAL_LOCATION,
                $user->id,
                "Login from different IP address",
                [
                    'previous_ip' => $lastKnownIp,
                    'current_ip' => $currentIp,
                ]
            );
        }

        Cache::put($cacheKey, $currentIp, now()->addDays(30));
        return false;
    }

    /**
     * Check for user agent changes.
     */
    public function checkUserAgentChange(User $user): bool
    {
        $currentUserAgent = $this->request->userAgent();
        $cacheKey = "user_agent:{$user->id}";
        
        $lastUserAgent = Cache::get($cacheKey);
        
        if ($lastUserAgent && $lastUserAgent !== $currentUserAgent) {
            // Significant user agent change might indicate session hijacking
            $this->log(
                SuspiciousActivity::TYPE_USER_AGENT_CHANGE,
                $user->id,
                "User agent changed",
                [
                    'previous_agent' => $lastUserAgent,
                    'current_agent' => $currentUserAgent,
                ]
            );
        }

        Cache::put($cacheKey, $currentUserAgent, now()->addDays(30));
        return false;
    }

    /**
     * Check for bot-like behavior.
     */
    public function checkBotBehavior(): bool
    {
        $userAgent = $this->request->userAgent();
        
        // Common bot patterns
        $botPatterns = [
            '/bot/i',
            '/crawl/i',
            '/spider/i',
            '/scrape/i',
            '/curl/i',
            '/wget/i',
            '/python/i',
        ];

        foreach ($botPatterns as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                $this->log(
                    SuspiciousActivity::TYPE_BOT_BEHAVIOR,
                    auth()->id(),
                    "Bot-like user agent detected",
                    [
                        'user_agent' => $userAgent,
                        'pattern_matched' => $pattern,
                    ]
                );
                return true;
            }
        }

        return false;
    }

    /**
     * Check for SQL injection attempts in request.
     */
    public function checkSqlInjection(): bool
    {
        $inputs = $this->request->all();
        $sqlPatterns = [
            '/(\bSELECT\b|\bUNION\b|\bINSERT\b|\bDROP\b|\bDELETE\b|\bUPDATE\b)/i',
            '/(-{2}|\/\*|\*\/)/i', // SQL comments
            '/(\bOR\b|\bAND\b)\s+[\'\"]?\d+[\'\"]?\s*=\s*[\'\"]?\d+/i', // OR 1=1, AND 1=1
        ];

        foreach ($inputs as $key => $value) {
            if (is_string($value)) {
                foreach ($sqlPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        $this->log(
                            SuspiciousActivity::TYPE_SQL_INJECTION_ATTEMPT,
                            auth()->id(),
                            "Possible SQL injection attempt detected",
                            [
                                'input_key' => $key,
                                'suspicious_value' => substr($value, 0, 200),
                            ]
                        );
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Check for XSS attempts in request.
     */
    public function checkXss(): bool
    {
        $inputs = $this->request->all();
        $xssPatterns = [
            '/<script[\s>]/i',
            '/javascript:/i',
            '/on\w+\s*=/i', // onclick=, onload=, etc.
            '/<iframe/i',
            '/<object/i',
            '/<embed/i',
        ];

        foreach ($inputs as $key => $value) {
            if (is_string($value)) {
                foreach ($xssPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        $this->log(
                            SuspiciousActivity::TYPE_XSS_ATTEMPT,
                            auth()->id(),
                            "Possible XSS attempt detected",
                            [
                                'input_key' => $key,
                                'suspicious_value' => htmlspecialchars(substr($value, 0, 200)),
                            ]
                        );
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Check if IP is currently blocked.
     */
    public function isBlocked(): bool
    {
        $ip = $this->request->ip();
        
        $blockedActivity = SuspiciousActivity::where('ip_address', $ip)
            ->where('is_blocked', true)
            ->where(function ($query) {
                $query->whereNull('blocked_until')
                    ->orWhere('blocked_until', '>', now());
            })
            ->latest()
            ->first();

        return $blockedActivity !== null && $blockedActivity->isCurrentlyBlocked();
    }

    /**
     * Block an IP address.
     */
    protected function blockIp(string $ip, string $severity): void
    {
        // Determine block duration based on severity
        $minutes = match ($severity) {
            SuspiciousActivity::SEVERITY_CRITICAL => 1440, // 24 hours
            SuspiciousActivity::SEVERITY_HIGH => 480, // 8 hours
            SuspiciousActivity::SEVERITY_MEDIUM => 120, // 2 hours
            default => 30, // 30 minutes
        };

        SuspiciousActivity::blockIp($ip, $minutes, "Automatic block due to {$severity} suspicious activity");
        
        Log::warning("IP address blocked", [
            'ip' => $ip,
            'severity' => $severity,
            'duration_minutes' => $minutes,
        ]);
    }

    /**
     * Clear failed login attempts for email (on successful login).
     */
    public function clearFailedAttempts(string $email): void
    {
        $ip = $this->request->ip();
        Cache::forget("failed_login:{$ip}:{$email}");
    }

    /**
     * Get recent activities for current IP.
     */
    public function getRecentActivities(int $minutes = 60): \Illuminate\Database\Eloquent\Collection
    {
        return SuspiciousActivity::getByIp($this->request->ip(), $minutes);
    }
}
