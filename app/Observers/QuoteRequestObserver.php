<?php

namespace App\Observers;

use App\Models\QuoteRequest;
use App\Models\Webhook;
use App\Services\WebhookService;

class QuoteRequestObserver
{
    protected WebhookService $webhookService;

    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    /**
     * Handle the QuoteRequest "created" event.
     */
    public function created(QuoteRequest $quoteRequest): void
    {
        $payload = [
            'id' => $quoteRequest->id,
            'specialist_id' => $quoteRequest->specialist_id,
            'client_name' => $quoteRequest->client_name,
            'client_email' => $quoteRequest->client_email,
            'client_phone' => $quoteRequest->client_phone,
            'service_id' => $quoteRequest->service_id,
            'description' => $quoteRequest->description,
            'created_at' => $quoteRequest->created_at,
        ];

        $this->webhookService->dispatch(
            Webhook::EVENT_QUOTE_REQUEST_CREATED,
            $payload,
            $quoteRequest->specialist_id
        );
    }
}
