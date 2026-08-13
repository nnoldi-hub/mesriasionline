<?php

namespace App\Notifications;

use App\Models\QuoteRequest;
use App\Services\NotificationPreferenceService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewRequestNotification extends Notification
{
    use Queueable;

    protected QuoteRequest $quoteRequest;

    public function __construct(QuoteRequest $quoteRequest)
    {
        $this->quoteRequest = $quoteRequest;
    }

    public function via(object $notifiable): array
    {
        return app(NotificationPreferenceService::class)
            ->getChannels($notifiable, 'review_requested');
    }

    public function toMail(object $notifiable): MailMessage
    {
        $craftsman = $this->quoteRequest->craftsman;

        return (new MailMessage)
            ->subject('Cum a fost lucrarea cu ' . $craftsman->name . '?')
            ->greeting('Bună, ' . $notifiable->name . '!')
            ->line('Lucrarea ta „' . $this->quoteRequest->title . '" cu **' . $craftsman->name . '** a fost marcată ca finalizată.')
            ->line('Părerea ta contează — ajută alți clienți să aleagă meseriași de încredere.')
            ->action('Lasă o recenzie (durează 1 minut)', url('/recenzie/' . $this->quoteRequest->review_token))
            ->line('Îți mulțumim!')
            ->salutation('Cu stimă, Echipa Meseriași');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'             => 'review_requested',
            'quote_request_id' => $this->quoteRequest->id,
            'craftsman_name'   => $this->quoteRequest->craftsman->name,
            'url'              => '/recenzie/' . $this->quoteRequest->review_token,
        ];
    }
}
