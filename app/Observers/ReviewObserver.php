<?php

namespace App\Observers;

use App\Models\Review;
use App\Models\Webhook;
use App\Services\WebhookService;
use Illuminate\Support\Facades\Cache;

class ReviewObserver
{
    protected WebhookService $webhookService;

    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        Cache::forget('stat_total_reviews');
        Cache::forget('stat_avg_rating');

        $payload = [
            'id' => $review->id,
            'specialist_id' => $review->specialist_id,
            'client_id' => $review->client_id,
            'rating' => $review->rating,
            'comment' => $review->comment,
            'is_approved' => $review->is_approved,
            'created_at' => $review->created_at,
        ];

        $this->webhookService->dispatch(
            Webhook::EVENT_REVIEW_CREATED,
            $payload,
            $review->specialist_id
        );
    }

    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
        // Trigger webhook when review is approved
        if ($review->wasChanged('is_approved') && $review->is_approved) {
            $payload = [
                'id' => $review->id,
                'specialist_id' => $review->specialist_id,
                'client_id' => $review->client_id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'approved_at' => $review->updated_at,
            ];

            $this->webhookService->dispatch(
                Webhook::EVENT_REVIEW_APPROVED,
                $payload,
                $review->specialist_id
            );
        }
    }
}
