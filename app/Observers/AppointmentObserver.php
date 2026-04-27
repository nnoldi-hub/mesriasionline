<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Models\Webhook;
use App\Services\WebhookService;

class AppointmentObserver
{
    protected WebhookService $webhookService;

    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        $payload = [
            'id' => $appointment->id,
            'specialist_id' => $appointment->specialist_id,
            'client_name' => $appointment->client_name,
            'client_email' => $appointment->client_email,
            'client_phone' => $appointment->client_phone,
            'service_id' => $appointment->service_id,
            'appointment_date' => $appointment->appointment_date,
            'appointment_time' => $appointment->appointment_time,
            'status' => $appointment->status,
            'is_home_service' => $appointment->is_home_service,
            'created_at' => $appointment->created_at,
        ];

        $this->webhookService->dispatch(
            Webhook::EVENT_APPOINTMENT_CREATED,
            $payload,
            $appointment->specialist_id
        );
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        // Trigger webhooks for status changes
        if ($appointment->wasChanged('status')) {
            $event = match ($appointment->status) {
                'confirmed' => Webhook::EVENT_APPOINTMENT_CONFIRMED,
                'completed' => Webhook::EVENT_APPOINTMENT_COMPLETED,
                'cancelled' => Webhook::EVENT_APPOINTMENT_CANCELLED,
                default => null,
            };

            if ($event) {
                $payload = [
                    'id' => $appointment->id,
                    'specialist_id' => $appointment->specialist_id,
                    'client_name' => $appointment->client_name,
                    'appointment_date' => $appointment->appointment_date,
                    'appointment_time' => $appointment->appointment_time,
                    'old_status' => $appointment->getOriginal('status'),
                    'new_status' => $appointment->status,
                    'updated_at' => $appointment->updated_at,
                ];

                $this->webhookService->dispatch(
                    $event,
                    $payload,
                    $appointment->specialist_id
                );
            }
        }
    }
}
