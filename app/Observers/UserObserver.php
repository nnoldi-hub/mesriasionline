<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Webhook;
use App\Services\WebhookService;
use Illuminate\Support\Facades\Cache;

class UserObserver
{
    protected WebhookService $webhookService;

    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        Cache::forget('stat_total_craftsmen');

        $payload = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'user_type' => $user->user_type,
            'created_at' => $user->created_at,
        ];

        $this->webhookService->dispatch(
            Webhook::EVENT_USER_REGISTERED,
            $payload,
            $user->id
        );
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->wasChanged('is_active')) {
            Cache::forget('stat_total_craftsmen');
        }

        // Trigger webhook when email is verified
        if ($user->wasChanged('email_verified_at') && $user->email_verified_at) {
            $payload = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'verified_at' => $user->email_verified_at,
            ];

            $this->webhookService->dispatch(
                Webhook::EVENT_USER_VERIFIED,
                $payload,
                $user->id
            );
        }
    }
}
