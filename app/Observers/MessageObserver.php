<?php

namespace App\Observers;

use App\Models\Message;
use App\Models\Webhook;
use App\Services\WebhookService;

class MessageObserver
{
    protected WebhookService $webhookService;

    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    /**
     * Handle the Message "created" event.
     */
    public function created(Message $message): void
    {
        // Get conversation to determine recipient (specialist)
        $conversation = $message->conversation;
        
        if ($conversation) {
            $payload = [
                'id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'sender_id' => $message->sender_id,
                'content' => $message->body,
                'created_at' => $message->created_at,
            ];

            // Dispatch to the recipient (specialist)
            $recipientId = $conversation->craftsman_id;
            
            $this->webhookService->dispatch(
                Webhook::EVENT_MESSAGE_RECEIVED,
                $payload,
                $recipientId
            );
        }
    }
}
