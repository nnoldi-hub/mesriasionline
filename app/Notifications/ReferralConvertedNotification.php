<?php

namespace App\Notifications;

use App\Models\CraftsmanLead;
use App\Services\NotificationPreferenceService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReferralConvertedNotification extends Notification
{
    use Queueable;

    protected CraftsmanLead $lead;
    protected bool $rewardGiven;

    public function __construct(CraftsmanLead $lead, bool $rewardGiven)
    {
        $this->lead = $lead;
        $this->rewardGiven = $rewardGiven;
    }

    public function via(object $notifiable): array
    {
        return app(NotificationPreferenceService::class)
            ->getChannels($notifiable, 'referral_converted');
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('🎉 ' . $this->lead->name . ' și-a creat cont — mulțumită ție!')
            ->greeting('Bună, ' . $notifiable->name . '!')
            ->line('Vești bune — **' . $this->lead->name . '**, colegul pe care l-ai recomandat, tocmai și-a creat cont pe meseriasionline.ro!');

        if ($this->rewardGiven) {
            $mail->line('Ca mulțumire, am adăugat automat **30 de zile gratuite** la abonamentul tău activ.');
        } else {
            $mail->line('Îți mulțumim pentru recomandare — echipa noastră va reveni cu o recompensă pentru tine.');
        }

        return $mail
            ->line('Continuă să recomanzi colegi de breaslă — cu cât aduci mai mulți, cu atât platforma devine mai utilă pentru toată lumea.')
            ->action('Recomandă alt coleg', url('/craftsman/recomandari'))
            ->salutation('Cu stimă, Echipa Meseriași');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'referral_converted',
            'lead_id'      => $this->lead->id,
            'lead_name'    => $this->lead->name,
            'reward_given' => $this->rewardGiven,
        ];
    }
}
