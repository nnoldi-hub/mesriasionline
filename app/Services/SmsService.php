<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SmsService
{
    protected string $provider;
    protected bool $enabled;

    public function __construct()
    {
        $this->provider = config('services.sms.provider', 'twilio');
        $this->enabled = config('services.sms.enabled', false);
    }

    /**
     * Check if SMS service is enabled and configured.
     */
    public function isEnabled(): bool
    {
        if (!$this->enabled) {
            return false;
        }

        return match($this->provider) {
            'twilio' => !empty(config('services.twilio.sid')) && !empty(config('services.twilio.token')),
            'vonage' => !empty(config('services.vonage.key')) && !empty(config('services.vonage.secret')),
            'netopia' => !empty(config('services.netopia.api_key')),
            default => false,
        };
    }

    /**
     * Send an SMS message.
     */
    public function send(string $to, string $message): bool
    {
        if (!$this->isEnabled()) {
            Log::warning('SMS service is not enabled or configured');
            return false;
        }

        // Format phone number for Romania
        $to = $this->formatPhoneNumber($to);

        return match($this->provider) {
            'twilio' => $this->sendViaTwilio($to, $message),
            'vonage' => $this->sendViaVonage($to, $message),
            'netopia' => $this->sendViaNetopia($to, $message),
            default => false,
        };
    }

    /**
     * Send appointment reminder SMS.
     */
    public function sendAppointmentReminder(Appointment $appointment): bool
    {
        $craftsman = $appointment->specialist;
        $service = $appointment->service;
        
        $message = $this->buildReminderMessage($appointment);
        
        // Send to client
        $clientSent = $this->send($appointment->client_phone, $message);
        
        Log::info("SMS reminder sent for appointment #{$appointment->id}", [
            'client_sent' => $clientSent,
            'phone' => $appointment->client_phone,
        ]);

        return $clientSent;
    }

    /**
     * Send appointment confirmation SMS.
     */
    public function sendAppointmentConfirmation(Appointment $appointment): bool
    {
        $message = $this->buildConfirmationMessage($appointment);
        return $this->send($appointment->client_phone, $message);
    }

    /**
     * Send appointment cancellation SMS.
     */
    public function sendAppointmentCancellation(Appointment $appointment): bool
    {
        $craftsman = $appointment->specialist;
        
        $message = "Programarea ta la {$craftsman->name} din data de " .
                   $appointment->appointment_date->format('d.m.Y') . 
                   " ora {$appointment->appointment_time} a fost anulată. " .
                   "Pentru reprogramare, te rugăm să ne contactezi. - Meseriași.ro";
        
        return $this->send($appointment->client_phone, $message);
    }

    /**
     * Build reminder message.
     */
    protected function buildReminderMessage(Appointment $appointment): string
    {
        $craftsman = $appointment->specialist;
        $date = $appointment->appointment_date->format('d.m.Y');
        $time = $appointment->appointment_time;
        $serviceName = $appointment->service?->name ?? 'serviciul';
        
        $message = "Reminder: Ai programare pentru {$serviceName} cu {$craftsman->name} " .
                   "mâine, {$date} la ora {$time}.";
        
        if ($appointment->is_home_service) {
            $message .= " Meșterul va veni la adresa indicată.";
        }
        
        $message .= " - Meseriași.ro";
        
        return $message;
    }

    /**
     * Build confirmation message.
     */
    protected function buildConfirmationMessage(Appointment $appointment): string
    {
        $craftsman = $appointment->specialist;
        $date = $appointment->appointment_date->format('d.m.Y');
        $time = $appointment->appointment_time;
        $serviceName = $appointment->service?->name ?? 'serviciul';
        
        return "Programarea ta pentru {$serviceName} cu {$craftsman->name} " .
               "pe data de {$date} la ora {$time} a fost confirmată. - Meseriași.ro";
    }

    /**
     * Format phone number for international format.
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove spaces, dashes, dots
        $phone = preg_replace('/[\s\-\.]/', '', $phone);
        
        // If starts with 0, replace with +40 (Romania)
        if (str_starts_with($phone, '0')) {
            $phone = '+40' . substr($phone, 1);
        }
        
        // If doesn't have country code, add Romania's
        if (!str_starts_with($phone, '+')) {
            $phone = '+40' . $phone;
        }
        
        return $phone;
    }

    /**
     * Send SMS via Twilio.
     */
    protected function sendViaTwilio(string $to, string $message): bool
    {
        try {
            $sid = config('services.twilio.sid');
            $token = config('services.twilio.token');
            $from = config('services.twilio.from');

            $response = Http::withBasicAuth($sid, $token)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'From' => $from,
                    'To' => $to,
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                Log::info('SMS sent via Twilio', ['to' => $to, 'sid' => $response->json('sid')]);
                return true;
            }

            Log::error('Twilio SMS failed', ['response' => $response->json()]);
            return false;

        } catch (\Exception $e) {
            Log::error('Twilio SMS exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send SMS via Vonage (Nexmo).
     */
    protected function sendViaVonage(string $to, string $message): bool
    {
        try {
            $apiKey = config('services.vonage.key');
            $apiSecret = config('services.vonage.secret');
            $from = config('services.vonage.from', 'Meseriasi');

            $response = Http::post('https://rest.nexmo.com/sms/json', [
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
                'from' => $from,
                'to' => str_replace('+', '', $to),
                'text' => $message,
                'type' => 'unicode',
            ]);

            $data = $response->json();
            
            if (isset($data['messages'][0]['status']) && $data['messages'][0]['status'] === '0') {
                Log::info('SMS sent via Vonage', ['to' => $to, 'message_id' => $data['messages'][0]['message-id'] ?? null]);
                return true;
            }

            Log::error('Vonage SMS failed', ['response' => $data]);
            return false;

        } catch (\Exception $e) {
            Log::error('Vonage SMS exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send SMS via Netopia (Romanian provider).
     */
    protected function sendViaNetopia(string $to, string $message): bool
    {
        try {
            $apiKey = config('services.netopia.api_key');
            $sender = config('services.netopia.sender', 'Meseriasi');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.netopia-payments.com/sms/send', [
                'sender' => $sender,
                'recipient' => $to,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('SMS sent via Netopia', ['to' => $to]);
                return true;
            }

            Log::error('Netopia SMS failed', ['response' => $response->json()]);
            return false;

        } catch (\Exception $e) {
            Log::error('Netopia SMS exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get remaining SMS credits (if supported by provider).
     */
    public function getBalance(): ?float
    {
        if (!$this->isEnabled()) {
            return null;
        }

        try {
            return match($this->provider) {
                'twilio' => $this->getTwilioBalance(),
                'vonage' => $this->getVonageBalance(),
                default => null,
            };
        } catch (\Exception $e) {
            Log::error('Failed to get SMS balance: ' . $e->getMessage());
            return null;
        }
    }

    protected function getTwilioBalance(): ?float
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');

        $response = Http::withBasicAuth($sid, $token)
            ->get("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Balance.json");

        if ($response->successful()) {
            return (float) $response->json('balance');
        }

        return null;
    }

    protected function getVonageBalance(): ?float
    {
        $apiKey = config('services.vonage.key');
        $apiSecret = config('services.vonage.secret');

        $response = Http::get("https://rest.nexmo.com/account/get-balance", [
            'api_key' => $apiKey,
            'api_secret' => $apiSecret,
        ]);

        if ($response->successful()) {
            return (float) $response->json('value');
        }

        return null;
    }
}
