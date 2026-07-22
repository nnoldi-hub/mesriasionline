<?php

namespace App\Notifications;

use App\Models\Review;
use App\Services\EmailTemplateService;
use App\Services\NotificationPreferenceService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NewReviewNotification extends Notification
{
    use Queueable;

    protected Review $review;

    /**
     * Create a new notification instance.
     */
    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return app(NotificationPreferenceService::class)
            ->getChannels($notifiable, 'new_review');
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $templateService = app(EmailTemplateService::class);
        $stars = str_repeat('⭐', $this->review->rating);
        
        $data = [
            'user_name' => $notifiable->name,
            'rating' => $this->review->rating,
            'rating_stars' => $stars,
            'comment' => $this->review->comment ? \Illuminate\Support\Str::limit($this->review->comment, 200) : '-',
            'action_url' => url('/craftsman/reviews'),
        ];

        return $templateService->buildMailMessage(
            'new_review',
            $data,
            'Ai primit o recenzie nouă ' . $stars,
            function (MailMessage $mail) use ($notifiable, $stars) {
                return $mail
                    ->subject('Ai primit o recenzie nouă ' . $stars)
                    ->greeting('Bună, ' . $notifiable->name . '!')
                    ->line('Un client ți-a lăsat o recenzie nouă.')
                    ->line('**Rating:** ' . $stars . ' (' . $this->review->rating . '/5)')
                    ->when($this->review->comment, fn ($m) => $m->line('**Comentariu:** "' . \Illuminate\Support\Str::limit($this->review->comment, 200) . '"'))
                    ->action('Vezi recenzia', url('/craftsman/reviews'))
                    ->line('Poți răspunde la recenzie pentru a-ți arăta aprecierea sau pentru a clarifica orice neînțelegere.')
                    ->salutation('Cu stimă, Echipa Meseriași');
            }
        );
    }

    /**
     * Get the WebPush representation of the notification.
     */
    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        $stars = str_repeat('⭐', $this->review->rating);
        $preview = $this->review->comment 
            ? \Illuminate\Support\Str::limit($this->review->comment, 80) 
            : 'Fără comentariu';
        
        return (new WebPushMessage)
            ->title('⭐ Recenzie Nouă: ' . $this->review->rating . '/5')
            ->icon('/images/logo.png')
            ->body($preview)
            ->action('Vezi', 'open')
            ->options([
                'TTL' => 86400,
                'urgency' => 'normal',
            ])
            ->data([
                'url' => url('/craftsman/reviews'),
                'notificationId' => $notification->id ?? null,
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_review',
            'review_id' => $this->review->id,
            'rating' => $this->review->rating,
            'comment' => $this->review->comment ? \Illuminate\Support\Str::limit($this->review->comment, 50) : null,
            'url' => '/craftsman/reviews',
        ];
    }
}
