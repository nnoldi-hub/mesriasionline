<?php

namespace App\Services;

use App\Models\Webhook;
use App\Models\WebhookDelivery;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    /**
     * Dispatch webhook event to all listening webhooks.
     *
     * @param string $event Event type constant
     * @param array $payload Event data
     * @param int|null $userId Optional user ID to filter webhooks
     * @return int Number of webhooks triggered
     */
    public function dispatch(string $event, array $payload, ?int $userId = null): int
    {
        $query = Webhook::active()->listeningTo($event);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $webhooks = $query->get();
        $triggered = 0;

        foreach ($webhooks as $webhook) {
            $this->trigger($webhook, $event, $payload);
            $triggered++;
        }

        return $triggered;
    }

    /**
     * Trigger a specific webhook.
     *
     * @param Webhook $webhook
     * @param string $event
     * @param array $payload
     * @return WebhookDelivery
     */
    public function trigger(Webhook $webhook, string $event, array $payload): WebhookDelivery
    {
        // Prepare payload with metadata
        $fullPayload = [
            'event' => $event,
            'timestamp' => now()->toIso8601String(),
            'data' => $payload,
        ];

        // Create delivery record
        $delivery = WebhookDelivery::create([
            'webhook_id' => $webhook->id,
            'event_type' => $event,
            'payload' => $fullPayload,
            'url' => $webhook->url,
            'attempts' => 1,
        ]);

        try {
            // Prepare headers
            $headers = [
                'Content-Type' => 'application/json',
                'X-Webhook-Event' => $event,
                'X-Webhook-ID' => $webhook->id,
                'X-Webhook-Delivery-ID' => $delivery->id,
            ];

            // Add signature if secret is set
            if ($webhook->secret) {
                $signature = $this->generateSignature($fullPayload, $webhook->secret);
                $headers['X-Webhook-Signature'] = $signature;
            }

            // Send webhook request
            $response = Http::timeout(10)
                ->withHeaders($headers)
                ->post($webhook->url, $fullPayload);

            // Update delivery record
            $delivery->update([
                'response_status' => $response->status(),
                'response_body' => $response->body(),
                'success' => $response->successful(),
                'delivered_at' => now(),
            ]);

            if ($response->successful()) {
                $webhook->recordSuccess();
            } else {
                $webhook->recordFailure();
                $delivery->update([
                    'error_message' => "HTTP {$response->status()}: {$response->body()}",
                ]);
            }
        } catch (\Exception $e) {
            // Log error
            Log::error('Webhook delivery failed', [
                'webhook_id' => $webhook->id,
                'event' => $event,
                'url' => $webhook->url,
                'error' => $e->getMessage(),
            ]);

            // Update delivery record
            $delivery->update([
                'success' => false,
                'error_message' => $e->getMessage(),
                'delivered_at' => now(),
            ]);

            $webhook->recordFailure();
        }

        return $delivery;
    }

    /**
     * Retry a failed webhook delivery.
     *
     * @param WebhookDelivery $delivery
     * @return WebhookDelivery
     */
    public function retry(WebhookDelivery $delivery): WebhookDelivery
    {
        $webhook = $delivery->webhook;

        if (!$webhook || !$webhook->is_active) {
            throw new \Exception('Webhook is not active or does not exist.');
        }

        try {
            $headers = [
                'Content-Type' => 'application/json',
                'X-Webhook-Event' => $delivery->event_type,
                'X-Webhook-ID' => $webhook->id,
                'X-Webhook-Delivery-ID' => $delivery->id,
                'X-Webhook-Retry-Attempt' => $delivery->attempts + 1,
            ];

            if ($webhook->secret) {
                $signature = $this->generateSignature($delivery->payload, $webhook->secret);
                $headers['X-Webhook-Signature'] = $signature;
            }

            $response = Http::timeout(10)
                ->withHeaders($headers)
                ->post($webhook->url, $delivery->payload);

            $delivery->update([
                'response_status' => $response->status(),
                'response_body' => $response->body(),
                'success' => $response->successful(),
                'attempts' => $delivery->attempts + 1,
                'delivered_at' => now(),
                'error_message' => $response->successful() ? null : "HTTP {$response->status()}: {$response->body()}",
            ]);

            if ($response->successful()) {
                $webhook->recordSuccess();
            } else {
                $webhook->recordFailure();
            }
        } catch (\Exception $e) {
            $delivery->update([
                'success' => false,
                'error_message' => $e->getMessage(),
                'attempts' => $delivery->attempts + 1,
                'delivered_at' => now(),
            ]);

            $webhook->recordFailure();
        }

        return $delivery->fresh();
    }

    /**
     * Generate HMAC signature for webhook payload.
     *
     * @param array $payload
     * @param string $secret
     * @return string
     */
    protected function generateSignature(array $payload, string $secret): string
    {
        $json = json_encode($payload);
        return 'sha256=' . hash_hmac('sha256', $json, $secret);
    }

    /**
     * Verify webhook signature.
     *
     * @param string $signature Header signature
     * @param array $payload Request payload
     * @param string $secret Webhook secret
     * @return bool
     */
    public function verifySignature(string $signature, array $payload, string $secret): bool
    {
        $expected = $this->generateSignature($payload, $secret);
        return hash_equals($expected, $signature);
    }

    /**
     * Test webhook endpoint.
     *
     * @param Webhook $webhook
     * @return array Test result
     */
    public function test(Webhook $webhook): array
    {
        $testPayload = [
            'event' => 'webhook.test',
            'timestamp' => now()->toIso8601String(),
            'data' => [
                'message' => 'This is a test webhook delivery',
                'webhook_id' => $webhook->id,
                'webhook_name' => $webhook->name,
            ],
        ];

        try {
            $headers = [
                'Content-Type' => 'application/json',
                'X-Webhook-Event' => 'webhook.test',
                'X-Webhook-ID' => $webhook->id,
            ];

            if ($webhook->secret) {
                $headers['X-Webhook-Signature'] = $this->generateSignature($testPayload, $webhook->secret);
            }

            $response = Http::timeout(10)
                ->withHeaders($headers)
                ->post($webhook->url, $testPayload);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'body' => $response->body(),
                'time' => $response->transferStats->getTransferTime(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get webhook statistics.
     *
     * @param Webhook $webhook
     * @param int $days Number of days to analyze
     * @return array
     */
    public function getStatistics(Webhook $webhook, int $days = 30): array
    {
        $deliveries = $webhook->deliveries()
            ->where('created_at', '>=', now()->subDays($days))
            ->get();

        return [
            'total_deliveries' => $deliveries->count(),
            'successful' => $deliveries->where('success', true)->count(),
            'failed' => $deliveries->where('success', false)->count(),
            'success_rate' => $deliveries->count() > 0 
                ? round(($deliveries->where('success', true)->count() / $deliveries->count()) * 100, 2) 
                : 0,
            'avg_response_time' => null, // Would need to store response times
            'last_delivery' => $deliveries->sortByDesc('created_at')->first()?->created_at,
        ];
    }
}