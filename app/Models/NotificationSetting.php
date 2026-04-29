<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class NotificationSetting extends Model
{
    protected $fillable = [
        'notification_type',
        'label',
        'description',
        'is_enabled',
        'email_enabled',
        'database_enabled',
        'push_enabled',
    ];

    protected $casts = [
        'is_enabled'       => 'boolean',
        'email_enabled'    => 'boolean',
        'database_enabled' => 'boolean',
        'push_enabled'     => 'boolean',
    ];

    const CACHE_KEY = 'notification_settings_all';
    const CACHE_TTL = 300; // 5 minutes

    /**
     * Returns all settings indexed by notification_type, cached.
     */
    public static function allCached(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return self::all()->keyBy('notification_type')->toArray();
        });
    }

    /**
     * Clears the settings cache (call after any update).
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Gets a single setting by type (cached).
     */
    public static function forType(string $type): ?self
    {
        $all = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return self::all()->keyBy('notification_type');
        });

        return $all->get($type);
    }
}
