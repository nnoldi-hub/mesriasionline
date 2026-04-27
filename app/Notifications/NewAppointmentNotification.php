<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Models\EmailTemplate;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NewAppointmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Appointment $appointment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['mail', 'database'];
        
        // Adaugă WebPush dacă utilizatorul are subscripții active
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
        
        $data = [
            'user_name' => $notifiable->name,
            'appointment_date' => $this->appointment->scheduled_at->format('d.m.Y la H:i'),
            'service_name' => $this->appointment->service?->name ?? 'Serviciu nespecificat',
            'notes' => $this->appointment->notes ?? '-',
            'action_url' => url('/craftsman/appointments'),
        ];

        return $templateService->buildMailMessage(
            'new_appointment',
            $data,
            'Programare nouă: ' . $this->appointment->scheduled_at->format('d.m.Y H:i'),
            function (MailMessage $mail) use ($notifiable) {
                return $mail
                    ->subject('Programare nouă: ' . $this->appointment->scheduled_at->format('d.m.Y H:i'))
                    ->greeting('Bună, ' . $notifiable->name . '!')
                    ->line('Ai primit o nouă programare.')
                    ->line('**Data și ora:** ' . $this->appointment->scheduled_at->format('d.m.Y la H:i'))
                    ->when($this->appointment->service, fn ($m) => $m->line('**Serviciu:** ' . $this->appointment->service->name))
                    ->when($this->appointment->notes, fn ($m) => $m->line('**Note:** ' . $this->appointment->notes))
                    ->action('Vezi programările', url('/craftsman/appointments'))
                    ->line('Nu uita să confirmi programarea!')
                    ->salutation('Cu stimă, Echipa Meseriași');
            }
        );
    }

    /**
     * Get the WebPush representation of the notification.
     */
    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        $serviceName = $this->appointment->service?->name ?? 'Serviciu';
        
        return (new WebPushMessage)
            ->title('📅 Programare Nouă')
            ->icon('/images/logo.png')
            ->body("Ai o programare nouă pentru {$serviceName} pe {$this->appointment->scheduled_at->format('d.m.Y la H:i')}")
            ->action('Vezi', 'open')
            ->options([
                'TTL' => 86400,
                'urgency' => 'high',
            ])
            ->data([
                'url' => url('/craftsman/appointments'),
                'notificationId' => $notification->id ?? null,
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_appointment',
            'appointment_id' => $this->appointment->id,
            'scheduled_at' => $this->appointment->scheduled_at->toISOString(),
            'service_name' => $this->appointment->service?->name,
            'url' => '/craftsman/appointments',
        ];
    }
}
