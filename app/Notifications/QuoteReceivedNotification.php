<?php

namespace App\Notifications;

use App\Models\Quote;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class QuoteReceivedNotification extends Notification implements ShouldQueue
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
        $channels = ['mail', 'database'];
        
        if ($notifiable->pushSubscriptions()->exists()) {
            $channels[] = WebPushChannel::class;
        }
        
        return $channels;
    }

    /**
     * Get the WebPush representation of the notification.
     */
    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        $craftsman = $this->quote->craftsman;
        
        return (new WebPushMessage)
            ->title('💰 Ofertă Nouă Primită')
            ->icon('/images/logo.png')
            ->body($craftsman->name . ' - ' . $this->quote->price_display)
            ->action('Vezi', 'open')
            ->options(['TTL' => 86400, 'urgency' => 'normal'])
            ->data([
                'url' => url('/cereri-oferta/' . $this->quote->quote_request_id),
                'notificationId' => $notification->id ?? null,
            ]);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $craftsman = $this->quote->craftsman;
        $quoteRequest = $this->quote->quoteRequest;
        
        return (new MailMessage)
            ->subject('Ai primit o ofertă pentru: ' . $quoteRequest->title)
            ->greeting('Bună, ' . $notifiable->name . '!')
            ->line('Ai primit o ofertă de la **' . $craftsman->name . '** pentru cererea ta.')
            ->line('**Serviciu:** ' . $quoteRequest->title)
            ->line('**Preț oferit:** ' . $this->quote->price_display)
            ->when($this->quote->duration_display, fn ($mail) => $mail->line('**Durată estimată:** ' . $this->quote->duration_display))
            ->line('**Descriere:** ' . \Illuminate\Support\Str::limit($this->quote->description, 200))
            ->action('Vezi oferta', url('/cereri-oferta/' . $quoteRequest->id))
            ->line('Compară ofertele și alege meșterul potrivit pentru tine!')
            ->salutation('Cu stimă, Echipa Meseriași');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'quote_received',
            'quote_id' => $this->quote->id,
            'quote_request_id' => $this->quote->quote_request_id,
            'craftsman_id' => $this->quote->craftsman_id,
            'craftsman_name' => $this->quote->craftsman->name,
            'price' => $this->quote->price_display,
            'title' => $this->quote->quoteRequest->title,
            'url' => '/cereri-oferta/' . $this->quote->quote_request_id,
        ];
    }
}
