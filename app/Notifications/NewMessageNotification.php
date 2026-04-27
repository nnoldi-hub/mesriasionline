<?php

namespace App\Notifications;

use App\Models\Message;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Message $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $templateService = app(EmailTemplateService::class);
        $sender = $this->message->sender;
        $preview = \Illuminate\Support\Str::limit($this->message->body, 100);
        
        $data = [
            'user_name' => $notifiable->name,
            'sender_name' => $sender->name,
            'message_preview' => $preview,
            'action_url' => url('/mesaje/' . $this->message->conversation_id),
        ];

        return $templateService->buildMailMessage(
            'new_message',
            $data,
            'Mesaj nou de la ' . $sender->name,
            function (MailMessage $mail) use ($notifiable, $sender, $preview) {
                return $mail
                    ->subject('Mesaj nou de la ' . $sender->name)
                    ->greeting('Bună, ' . $notifiable->name . '!')
                    ->line('Ai primit un mesaj nou de la **' . $sender->name . '**:')
                    ->line('"' . $preview . '"')
                    ->action('Vezi conversația', url('/mesaje/' . $this->message->conversation_id))
                    ->line('Răspunde cât mai curând pentru a menține comunicarea activă.')
                    ->salutation('Cu stimă, Echipa Meseriași');
            }
        );
    }

    /**
     * Get the WebPush representation of the notification.
     */
    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        $sender = $this->message->sender;
        $preview = \Illuminate\Support\Str::limit($this->message->body, 80);
        
        return (new WebPushMessage)
            ->title('💬 Mesaj de la ' . $sender->name)
            ->icon('/images/logo.png')
            ->body($preview)
            ->action('Răspunde', 'open')
            ->options([
                'TTL' => 86400,
                'urgency' => 'high',
            ])
            ->data([
                'url' => url('/mesaje/' . $this->message->conversation_id),
                'notificationId' => $notification->id ?? null,
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_message',
            'message_id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'preview' => \Illuminate\Support\Str::limit($this->message->body, 50),
            'url' => '/mesaje/' . $this->message->conversation_id,
        ];
    }
}
