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
    protected bool $isReminder;

    public function __construct(QuoteRequest $quoteRequest, bool $isReminder = false)
    {
        $this->quoteRequest = $quoteRequest;
        $this->isReminder = $isReminder;
    }

    public function via(object $notifiable): array
    {
        return app(NotificationPreferenceService::class)
            ->getChannels($notifiable, 'review_requested');
    }

    public function toMail(object $notifiable): MailMessage
    {
        $craftsman = $this->quoteRequest->craftsman;

        if ($this->isReminder) {
            return (new MailMessage)
                ->subject('Ne pasă de părerea dvs., ' . $notifiable->name)
                ->greeting('Bună, ' . $notifiable->name . '!')
                ->line('Ne-am dat seama că nu ați apucat încă să lăsați o recenzie pentru lucrarea „' . $this->quoteRequest->title . '" cu **' . $craftsman->name . '**.')
                ->line('Ne pasă cu adevărat de experiența clienților noștri, iar părerea dvs. — bună sau cu sugestii de îmbunătățire — ne ajută pe noi și pe alți clienți care caută un meseriaș de încredere.')
                ->action('Lasă o recenzie (durează 1 minut)', url('/recenzie/' . $this->quoteRequest->review_token))
                ->line('Vă mulțumim pentru timpul acordat!')
                ->salutation('Cu stimă, Echipa Meseriași');
        }

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
