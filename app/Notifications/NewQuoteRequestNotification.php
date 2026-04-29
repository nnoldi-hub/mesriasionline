<?php

namespace App\Notifications;

use App\Models\QuoteRequest;
use App\Services\EmailTemplateService;
use App\Services\NotificationPreferenceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NewQuoteRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected QuoteRequest $quoteRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(QuoteRequest $quoteRequest)
    {
        $this->quoteRequest = $quoteRequest;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return app(NotificationPreferenceService::class)
            ->getChannels($notifiable, 'new_quote_request');
    }

    /**
     * Get the WebPush representation of the notification.
     */
    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('📋 Cerere Nouă de Ofertă')
            ->icon('/images/logo.png')
            ->body($this->quoteRequest->title . ' - ' . $this->quoteRequest->urgency_label)
            ->action('Vezi', 'open')
            ->options(['TTL' => 86400, 'urgency' => 'high'])
            ->data([
                'url' => url('/craftsman/quotes/' . $this->quoteRequest->id),
                'notificationId' => $notification->id ?? null,
            ]);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $client = $this->quoteRequest->client;
        
        return (new MailMessage)
            ->subject('Cerere nouă de ofertă: ' . $this->quoteRequest->title)
            ->greeting('Bună, ' . $notifiable->name . '!')
            ->line('Ai primit o cerere nouă de ofertă de la **' . $client->name . '**.')
            ->line('**Titlu:** ' . $this->quoteRequest->title)
            ->line('**Descriere:** ' . \Illuminate\Support\Str::limit($this->quoteRequest->description, 200))
            ->when($this->quoteRequest->location, fn ($mail) => $mail->line('**Locație:** ' . $this->quoteRequest->location))
            ->when($this->quoteRequest->preferred_date, fn ($mail) => $mail->line('**Data preferată:** ' . $this->quoteRequest->preferred_date->format('d.m.Y')))
            ->line('**Urgență:** ' . $this->quoteRequest->urgency_label)
            ->action('Trimite ofertă', url('/craftsman/quotes/' . $this->quoteRequest->id))
            ->line('Răspunde rapid pentru a câștiga încrederea clienților!')
            ->salutation('Cu stimă, Echipa Meseriași');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_quote_request',
            'quote_request_id' => $this->quoteRequest->id,
            'title' => $this->quoteRequest->title,
            'client_id' => $this->quoteRequest->client_id,
            'client_name' => $this->quoteRequest->client->name,
            'urgency' => $this->quoteRequest->urgency,
            'url' => '/craftsman/quotes/' . $this->quoteRequest->id,
        ];
    }
}
