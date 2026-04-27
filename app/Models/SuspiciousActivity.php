<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuspiciousActivity extends Model
{
    // Activity types
    const TYPE_FAILED_LOGIN = 'failed_login';
    const TYPE_RAPID_SUBMISSION = 'rapid_submission';
    const TYPE_UNUSUAL_LOCATION = 'unusual_location';
    const TYPE_USER_AGENT_CHANGE = 'user_agent_change';
    const TYPE_MULTIPLE_ACCOUNTS = 'multiple_accounts';
    const TYPE_BOT_BEHAVIOR = 'bot_behavior';
    const TYPE_BRUTE_FORCE = 'brute_force';
    const TYPE_SQL_INJECTION_ATTEMPT = 'sql_injection';
    const TYPE_XSS_ATTEMPT = 'xss_attempt';

    // Severity levels
    const SEVERITY_LOW = 'low';
    const SEVERITY_MEDIUM = 'medium';
    const SEVERITY_HIGH = 'high';
    const SEVERITY_CRITICAL = 'critical';

    protected $fillable = [
        'user_id',
        'type',
        'severity',
        'ip_address',
        'user_agent',
        'details',
        'risk_score',
        'is_blocked',
        'blocked_until',
    ];

    protected $casts = [
        'is_blocked' => 'boolean',
        'blocked_until' => 'datetime',
        'details' => 'array',
    ];

    /**
     * Get the user associated with this activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the IP is currently blocked.
     */
    public function isCurrentlyBlocked(): bool
    {
        if (!$this->is_blocked) {
            return false;
        }

        if ($this->blocked_until && $this->blocked_until->isPast()) {
            // Auto-unblock if time has passed
            $this->update(['is_blocked' => false, 'blocked_until' => null]);
            return false;
        }

        return true;
    }

    /**
     * Get activities by IP address within timeframe.
     */
    public static function getByIp(string $ip, int $minutes = 60): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('ip_address', $ip)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get activities by user within timeframe.
     */
    public static function getByUser(int $userId, int $minutes = 60): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('user_id', $userId)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Calculate risk score based on severity and frequency.
     */
    public static function calculateRiskScore(string $type, int $count): int
    {
        $baseScores = [
            self::TYPE_FAILED_LOGIN => 10,
            self::TYPE_RAPID_SUBMISSION => 15,
            self::TYPE_UNUSUAL_LOCATION => 20,
            self::TYPE_USER_AGENT_CHANGE => 25,
            self::TYPE_MULTIPLE_ACCOUNTS => 30,
            self::TYPE_BOT_BEHAVIOR => 40,
            self::TYPE_BRUTE_FORCE => 50,
            self::TYPE_SQL_INJECTION_ATTEMPT => 90,
            self::TYPE_XSS_ATTEMPT => 90,
        ];

        $baseScore = $baseScores[$type] ?? 10;
        $frequencyMultiplier = min($count, 10); // Cap at 10x
        
        return min($baseScore * $frequencyMultiplier, 100);
    }

    /**
     * Determine severity from risk score.
     */
    public static function getSeverityFromScore(int $score): string
    {
        if ($score >= 80) return self::SEVERITY_CRITICAL;
        if ($score >= 50) return self::SEVERITY_HIGH;
        if ($score >= 30) return self::SEVERITY_MEDIUM;
        return self::SEVERITY_LOW;
    }

    /**
     * Check if IP should be blocked based on activities.
     */
    public static function shouldBlock(string $ip): bool
    {
        $recentActivities = static::getByIp($ip, 60);
        
        // Check for critical activities
        $criticalCount = $recentActivities->where('severity', self::SEVERITY_CRITICAL)->count();
        if ($criticalCount > 0) {
            return true;
        }

        // Check for high severity activities
        $highCount = $recentActivities->where('severity', self::SEVERITY_HIGH)->count();
        if ($highCount >= 3) {
            return true;
        }

        // Check for medium severity activities
        $mediumCount = $recentActivities->where('severity', self::SEVERITY_MEDIUM)->count();
        if ($mediumCount >= 5) {
            return true;
        }

        // Check for low severity but high frequency
        $lowCount = $recentActivities->where('severity', self::SEVERITY_LOW)->count();
        if ($lowCount >= 10) {
            return true;
        }

        // Check total risk score
        $totalRisk = $recentActivities->sum('risk_score');
        if ($totalRisk >= 150) {
            return true;
        }

        return false;
    }

    /**
     * Block an IP address.
     */
    public static function blockIp(string $ip, int $minutes = 60, string $reason = null): void
    {
        static::create([
            'ip_address' => $ip,
            'type' => 'auto_blocked',
            'severity' => self::SEVERITY_CRITICAL,
            'risk_score' => 100,
            'is_blocked' => true,
            'blocked_until' => now()->addMinutes($minutes),
            'details' => [
                'reason' => $reason ?? 'Automatic block due to suspicious activity',
                'auto_blocked' => true,
            ],
        ]);
    }
}
