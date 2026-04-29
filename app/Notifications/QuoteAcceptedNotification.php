<?php

namespace App\Notifications;

use App\Models\Quote;
use App\Services\EmailTemplateService;
use App\Services\NotificationPreferenceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class QuoteAcceptedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Quote $quote;

    /**
     * Create a new notification instance.
     */
    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return app(NotificationPreferenceService::class)
            ->getChannels($notifiable, 'quote_accepted');
    }

    /**
     * Get the WebPush representation of the notification.
     */
    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        $client = $this->quote->quoteRequest->client;
        
        return (new WebPushMessage)
            ->title('🎉 Oferta Ta a Fost Acceptată!')
            ->icon('/images/logo.png')
            ->body($client->name . ' a acceptat oferta ta de ' . $this->quote->price_display)
            ->action('Vezi', 'open')
            ->options(['TTL' => 86400, 'urgency' => 'high'])
            ->data([
                'url' => url('/craftsman/quotes/' . $this->quote->quote_request_id),
                'notificationId' => $notification->id ?? null,
            ]);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $client = $this->quote->quoteRequest->client;
        $quoteRequest = $this->quote->quoteRequest;
        
        return (new MailMessage)
            ->subject('🎉 Oferta ta a fost acceptată!')
            ->greeting('Felicitări, ' . $notifiable->name . '!')
            ->line('Clientul **' . $client->name . '** a acceptat oferta ta.')
            ->line('**Serviciu:** ' . $quoteRequest->title)
            ->line('**Preț acceptat:** ' . $this->quote->price_display)
            ->when($quoteRequest->location, fn ($mail) => $mail->line('**Locație:** ' . $quoteRequest->location))
            ->when($quoteRequest->preferred_date, fn ($mail) => $mail->line('**Data preferată:** ' . $quoteRequest->preferred_date->format('d.m.Y')))
            ->line('---')
            ->line('**Contact client:**')
            ->when($client->phone, fn ($mail) => $mail->line('📱 Telefon: ' . $client->phone))
            ->line('📧 Email: ' . $client->email)
            ->action('Vezi detalii', url('/craftsman/quotes/' . $quoteRequest->id))
            ->line('Contactează clientul cât mai curând pentru a stabili detaliile lucrării!')
            ->salutation('Succes! Echipa Meseriași');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'quote_accepted',
            'quote_id' => $this->quote->id,
            'quote_request_id' => $this->quote->quote_request_id,
            'client_id' => $this->quote->quoteRequest->client_id,
            'client_name' => $this->quote->quoteRequest->client->name,
            'title' => $this->quote->quoteRequest->title,
            'price' => $this->quote->price_display,
            'url' => '/craftsman/quotes/' . $this->quote->quote_request_id,
        ];
    }
}
