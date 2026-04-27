<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $apiKey;
    protected string $phoneNumberId;
    protected string $apiUrl = 'https://graph.facebook.com/v18.0';

    public function __construct()
    {
        $this->apiKey = config('services.whatsapp.business_api_key');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
    }

    /**
     * Send a template message
     */
    public function sendTemplateMessage(string $to, string $templateName, array $parameters = []): bool
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $this->formatPhoneNumber($to),
                    'type' => 'template',
                    'template' => [
                        'name' => $templateName,
                        'language' => [
                            'code' => 'ro',
                        ],
                        'components' => $this->buildTemplateComponents($parameters),
                    ],
                ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', [
                    'to' => $to,
                    'template' => $templateName,
                ]);
                return true;
            }

            Log::error('WhatsApp message failed', [
                'to' => $to,
                'template' => $templateName,
                'response' => $response->json(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('WhatsApp service error', [
                'message' => $e->getMessage(),
                'to' => $to,
            ]);
            return false;
        }
    }

    /**
     * Send appointment confirmation
     */
    public function sendAppointmentConfirmation(string $phone, array $appointmentData): bool
    {
        return $this->sendTemplateMessage($phone, 'appointment_confirmation', [
            ['type' => 'body', 'parameters' => [
                ['type' => 'text', 'text' => $appointmentData['client_name']],
                ['type' => 'text', 'text' => $appointmentData['date']],
                ['type' => 'text', 'text' => $appointmentData['time']],
                ['type' => 'text', 'text' => $appointmentData['specialist_name']],
            ]],
        ]);
    }

    /**
     * Send appointment reminder
     */
    public function sendAppointmentReminder(string $phone, array $appointmentData): bool
    {
        return $this->sendTemplateMessage($phone, 'appointment_reminder', [
            ['type' => 'body', 'parameters' => [
                ['type' => 'text', 'text' => $appointmentData['client_name']],
                ['type' => 'text', 'text' => $appointmentData['date']],
                ['type' => 'text', 'text' => $appointmentData['time']],
                ['type' => 'text', 'text' => $appointmentData['specialist_name']],
            ]],
        ]);
    }

    /**
     * Send quote response notification
     */
    public function sendQuoteNotification(string $phone, array $quoteData): bool
    {
        return $this->sendTemplateMessage($phone, 'quote_response', [
            ['type' => 'body', 'parameters' => [
                ['type' => 'text', 'text' => $quoteData['client_name']],
                ['type' => 'text', 'text' => $quoteData['specialist_name']],
                ['type' => 'text', 'text' => $quoteData['amount']],
            ]],
        ]);
    }

    /**
     * Send status change notification
     */
    public function sendStatusChangeNotification(string $phone, string $status, array $data): bool
    {
        $templates = [
            'confirmed' => 'appointment_confirmed',
            'completed' => 'appointment_completed',
            'cancelled' => 'appointment_cancelled',
        ];

        $templateName = $templates[$status] ?? 'status_update';

        return $this->sendTemplateMessage($phone, $templateName, [
            ['type' => 'body', 'parameters' => [
                ['type' => 'text', 'text' => $data['client_name']],
                ['type' => 'text', 'text' => $data['specialist_name']],
            ]],
        ]);
    }

    /**
     * Send custom text message (for testing)
     */
    public function sendTextMessage(string $to, string $message): bool
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $this->formatPhoneNumber($to),
                    'type' => 'text',
                    'text' => [
                        'body' => $message,
                    ],
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WhatsApp text message error', [
                'message' => $e->getMessage(),
                'to' => $to,
            ]);
            return false;
        }
    }

    /**
     * Format phone number to E.164 format
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Add Romania country code if not present
        if (!str_starts_with($phone, '40')) {
            if (str_starts_with($phone, '0')) {
                $phone = '40' . substr($phone, 1);
            } else {
                $phone = '40' . $phone;
            }
        }

        return $phone;
    }

    /**
     * Build template components from parameters
     */
    protected function buildTemplateComponents(array $parameters): array
    {
        if (empty($parameters)) {
            return [];
        }

        return $parameters;
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $secret = config('services.whatsapp.webhook_secret');
        $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Handle webhook event
     */
    public function handleWebhook(array $data): void
    {
        Log::info('WhatsApp webhook received', $data);

        // Process different webhook events
        if (isset($data['entry'][0]['changes'][0]['value'])) {
            $value = $data['entry'][0]['changes'][0]['value'];

            // Handle message status updates
            if (isset($value['statuses'])) {
                foreach ($value['statuses'] as $status) {
                    $this->handleMessageStatus($status);
                }
            }

            // Handle incoming messages
            if (isset($value['messages'])) {
                foreach ($value['messages'] as $message) {
                    $this->handleIncomingMessage($message);
                }
            }
        }
    }

    /**
     * Handle message status update
     */
    protected function handleMessageStatus(array $status): void
    {
        Log::info('WhatsApp message status', [
            'message_id' => $status['id'] ?? null,
            'status' => $status['status'] ?? null,
            'timestamp' => $status['timestamp'] ?? null,
        ]);

        // Update message delivery status in database if needed
    }

    /**
     * Handle incoming message
     */
    protected function handleIncomingMessage(array $message): void
    {
        Log::info('WhatsApp incoming message', [
            'from' => $message['from'] ?? null,
            'message_id' => $message['id'] ?? null,
            'type' => $message['type'] ?? null,
        ]);

        // Process incoming message if needed (e.g., auto-reply)
    }
}
