<?php

namespace App\Notifications;

use App\Models\Subscription;
use App\Services\NotificationPreferenceService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiringNotification extends Notification
{
    use Queueable;

    protected Subscription $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function via(object $notifiable): array
    {
        return app(NotificationPreferenceService::class)
            ->getChannels($notifiable, 'subscription_expiring');
    }

    public function toMail(object $notifiable): MailMessage
    {
        $daysLeft = max(0, now()->diffInDays($this->subscription->ends_at, false));

        return (new MailMessage)
            ->subject('Abonamentul tău ' . $this->subscription->plan->name . ' expiră în curând')
            ->greeting('Bună, ' . $notifiable->name . '!')
            ->line('Abonamentul tău **' . $this->subscription->plan->name . '** expiră pe **' . $this->subscription->ends_at->format('d.m.Y') . '** (în ' . $daysLeft . ' zile).')
            ->line('Ca să continui să primești clienți fără întrerupere, reînnoiește-l din contul tău.')
            ->action('Reînnoiește abonamentul', route('plans.index'))
            ->salutation('Cu stimă, Echipa Meseriași');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'              => 'subscription_expiring',
            'subscription_id'   => $this->subscription->id,
            'plan_name'         => $this->subscription->plan->name,
            'ends_at'           => $this->subscription->ends_at->toDateString(),
            'url'               => '/planuri',
        ];
    }
}
