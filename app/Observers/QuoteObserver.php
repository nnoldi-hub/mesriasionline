<?php

namespace App\Observers;

use App\Models\Quote;
use App\Models\Webhook;
use App\Services\WebhookService;

class QuoteObserver
{
    protected WebhookService $webhookService;

    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    /**
     * Handle the Quote "created" event.
     */
    public function created(Quote $quote): void
    {
        $payload = [
            'id' => $quote->id,
            'quote_request_id' => $quote->quote_request_id,
            'specialist_id' => $quote->specialist_id,
            'amount' => $quote->amount,
            'description' => $quote->description,
            'status' => $quote->status,
            'created_at' => $quote->created_at,
        ];

        $this->webhookService->dispatch(
            Webhook::EVENT_QUOTE_CREATED,
            $payload,
            $quote->specialist_id
        );
    }

    /**
     * Handle the Quote "updated" event.
     */
    public function updated(Quote $quote): void
    {
        // Trigger webhooks for status changes
        if ($quote->wasChanged('status')) {
            $event = match ($quote->status) {
                'accepted' => Webhook::EVENT_QUOTE_ACCEPTED,
                'rejected' => Webhook::EVENT_QUOTE_REJECTED,
                default => null,
            };

            if ($event) {
                $payload = [
                    'id' => $quote->id,
                    'quote_request_id' => $quote->quote_request_id,
                    'specialist_id' => $quote->specialist_id,
                    'amount' => $quote->amount,
                    'old_status' => $quote->getOriginal('status'),
                    'new_status' => $quote->status,
                    'updated_at' => $quote->updated_at,
                ];

                $this->webhookService->dispatch(
                    $event,
                    $payload,
                    $quote->specialist_id
                );
            }
        }
    }
}
