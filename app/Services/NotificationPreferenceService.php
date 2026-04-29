<?php

namespace App\Services;

use App\Models\NotificationSetting;
use App\Models\User;
use NotificationChannels\WebPush\WebPushChannel;

class NotificationPreferenceService
{
    /**
     * Returns the list of channels to use for a given notifiable and notification type.
     * Respects both global admin settings and per-user preferences.
     *
     * @param  User   $notifiable  The user receiving the notification
     * @param  string $type        Notification type key (e.g. 'new_quote_request')
     * @return array<string>
     */
    public function getChannels(User $notifiable, string $type): array
    {
        try {
            $setting = NotificationSetting::forType($type);
        } catch (\Exception $e) {
            // Fallback if notification_settings table doesn't exist yet
            return $this->defaultChannels($notifiable);
        }

        // If no admin setting found, fall back to default behaviour (all channels).
        if (!$setting) {
            return $this->defaultChannels($notifiable);
        }

        // Globally disabled — skip notification entirely.
        if (!$setting->is_enabled) {
            return [];
        }

        // Per-user preferences (null = not set, meaning "use default").
        $userPrefs = $notifiable->notification_preferences[$type] ?? null;

        $channels = [];

        // Email channel
        if ($setting->email_enabled) {
            $emailOn = $userPrefs !== null ? (bool)($userPrefs['email'] ?? true) : true;
            if ($emailOn) {
                $channels[] = 'mail';
            }
        }

        // Database (in-app) channel
        if ($setting->database_enabled) {
            $dbOn = $userPrefs !== null ? (bool)($userPrefs['database'] ?? true) : true;
            if ($dbOn) {
                $channels[] = 'database';
            }
        }

        // Push channel (only if user has active push subscriptions)
        if ($setting->push_enabled && $notifiable->pushSubscriptions()->exists()) {
            $pushOn = $userPrefs !== null ? (bool)($userPrefs['push'] ?? true) : true;
            if ($pushOn) {
                $channels[] = WebPushChannel::class;
            }
        }

        return $channels;
    }

    /**
     * Default channels used when no admin setting row exists yet.
     */
    private function defaultChannels(User $notifiable): array
    {
        $channels = ['mail', 'database'];
        if ($notifiable->pushSubscriptions()->exists()) {
            $channels[] = WebPushChannel::class;
        }
        return $channels;
    }
}
