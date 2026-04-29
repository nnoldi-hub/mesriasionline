<?php

namespace App\Notifications;

use App\Models\PublicJobRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPublicJobRequestNotification extends Notification
{
    use Queueable;

    public function __construct(public PublicJobRequest $jobRequest) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $req = $this->jobRequest;

        return (new MailMessage)
            ->subject('🔨 Cerere nouă: ' . $req->title)
            ->greeting('Salut, ' . $notifiable->name . '!')
            ->line('Ai primit o cerere nouă de la un client în zona ta.')
            ->line('**' . $req->title . '**')
            ->line('**Categorie:** ' . ($req->category?->name ?? '—'))
            ->line('**Locație:** ' . $req->location_display)
            ->line('**Urgență:** ' . $req->urgency_label)
            ->when($req->budget_max, fn($mail) => $mail->line('**Buget maxim:** ' . number_format($req->budget_max, 0, ',', '.') . ' lei'))
            ->line('**Descriere:** ' . \Illuminate\Support\Str::limit($req->description, 300))
            ->action('Vezi cererea și trimite ofertă', route('craftsman.public-requests.show', $req->id))
            ->line('Răspunde rapid — primul meseriaș care contactează clientul are cele mai mari șanse!')
            ->salutation('Echipa Meseriași Online');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'new_public_job_request',
            'title'   => 'Cerere nouă: ' . $this->jobRequest->title,
            'message' => 'Client în ' . $this->jobRequest->location_display . ' caută ' . ($this->jobRequest->category?->name ?? 'meseriaș'),
            'url'     => route('craftsman.public-requests.show', $this->jobRequest->id),
        ];
    }
}
