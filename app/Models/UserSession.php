<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'platform',
        'location',
        'is_current',
        'last_activity_at',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'last_activity_at' => 'datetime',
    ];

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Parse user agent and create/update session
     */
    public static function recordSession(int $userId, string $sessionId): self
    {
        $userAgent = request()->userAgent();
        $parsed = self::parseUserAgent($userAgent);

        return static::updateOrCreate(
            [
                'user_id' => $userId,
                'session_id' => $sessionId,
            ],
            [
                'ip_address' => request()->ip(),
                'user_agent' => $userAgent,
                'device_type' => $parsed['device_type'],
                'browser' => $parsed['browser'],
                'platform' => $parsed['platform'],
                'last_activity_at' => now(),
            ]
        );
    }

    /**
     * Mark session as current
     */
    public function markAsCurrent(): void
    {
        // Remove current flag from other sessions
        static::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_current' => false]);

        $this->update(['is_current' => true]);
    }

    /**
     * Parse user agent string
     */
    protected static function parseUserAgent(string $userAgent): array
    {
        $result = [
            'device_type' => 'desktop',
            'browser' => 'Unknown',
            'platform' => 'Unknown',
        ];

        // Device type
        if (preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $userAgent)) {
            $result['device_type'] = preg_match('/iPad|Tablet/i', $userAgent) ? 'tablet' : 'mobile';
        }

        // Browser
        if (preg_match('/Edge|Edg/i', $userAgent)) {
            $result['browser'] = 'Microsoft Edge';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $result['browser'] = 'Google Chrome';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $result['browser'] = 'Mozilla Firefox';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $result['browser'] = 'Apple Safari';
        } elseif (preg_match('/Opera|OPR/i', $userAgent)) {
            $result['browser'] = 'Opera';
        } elseif (preg_match('/MSIE|Trident/i', $userAgent)) {
            $result['browser'] = 'Internet Explorer';
        }

        // Platform
        if (preg_match('/Windows NT 10/i', $userAgent)) {
            $result['platform'] = 'Windows 10/11';
        } elseif (preg_match('/Windows/i', $userAgent)) {
            $result['platform'] = 'Windows';
        } elseif (preg_match('/Mac OS X/i', $userAgent)) {
            $result['platform'] = 'macOS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $result['platform'] = 'Linux';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $result['platform'] = 'Android';
        } elseif (preg_match('/iOS|iPhone|iPad/i', $userAgent)) {
            $result['platform'] = 'iOS';
        }

        return $result;
    }

    /**
     * Terminate session
     */
    public function terminate(): bool
    {
        return $this->delete();
    }

    /**
     * Terminate all other sessions for user
     */
    public static function terminateOtherSessions(int $userId, string $currentSessionId): int
    {
        return static::where('user_id', $userId)
            ->where('session_id', '!=', $currentSessionId)
            ->delete();
    }

    /**
     * Get device icon
     */
    public function getDeviceIconAttribute(): string
    {
        return match($this->device_type) {
            'mobile' => 'smartphone',
            'tablet' => 'tablet',
            default => 'monitor',
        };
    }
}
