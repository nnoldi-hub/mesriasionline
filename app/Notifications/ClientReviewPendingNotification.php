<?php

namespace App\Notifications;

use App\Models\QuoteRequest;
use App\Services\NotificationPreferenceService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientReviewPendingNotification extends Notification
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
            ->getChannels($notifiable, 'client_review_pending');
    }

    public function toMail(object $notifiable): MailMessage
    {
        $client = $this->quoteRequest->client;
        $reviewUrl = url('/recenzie/' . $this->quoteRequest->review_token);
        $whatsappText = rawurlencode(
            "Bună, {$client->name}! Mă bucur că am putut ajuta cu „{$this->quoteRequest->title}\". " .
            "Dacă ai 1 minut, mi-ar plăcea să știu cum ți s-a părut — poți lăsa o recenzie aici: {$reviewUrl}"
        );

        return (new MailMessage)
            ->subject('Clientul tău nu a lăsat încă o recenzie')
            ->greeting('Bună, ' . $notifiable->name . '!')
            ->line('**' . $client->name . '** nu a lăsat încă o recenzie pentru lucrarea „' . $this->quoteRequest->title . '".')
            ->line('Un mesaj personal de la tine convertește mult mai bine decât un email automat de la platformă — îți recomandăm să-i scrii direct.')
            ->action('Trimite mesaj pe WhatsApp', 'https://wa.me/?text=' . $whatsappText)
            ->line('Sau poți copia link-ul de recenzie direct: ' . $reviewUrl)
            ->salutation('Cu stimă, Echipa Meseriași');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'             => 'client_review_pending',
            'quote_request_id' => $this->quoteRequest->id,
            'client_name'      => $this->quoteRequest->client->name,
            'url'              => '/recenzie/' . $this->quoteRequest->review_token,
        ];
    }
}
