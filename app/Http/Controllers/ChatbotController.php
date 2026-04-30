<?php

namespace App\Http\Controllers;

use App\Models\ChatbotConversation;
use App\Models\ChatbotMessage;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function __construct(
        private readonly ChatbotService $chatbotService
    ) {}

    /**
     * Procesează un mesaj de la utilizator și returnează răspunsul AI.
     *
     * POST /api/chatbot
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message'         => ['required', 'string', 'min:1', 'max:500'],
            'conversion_url'  => ['nullable', 'string', 'max:500'],
        ]);

        $userMessage = trim($validated['message']);

        // Verificare securitate: prompt injection
        if ($this->chatbotService->isSecurityRisk($userMessage)) {
            Log::warning('Chatbot security risk detected', [
                'ip'      => $request->ip(),
                'message' => substr($userMessage, 0, 100),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Mesajul nu poate fi procesat. Încearcă să reformulezi întrebarea.',
                'actions' => [],
            ], 422);
        }

        // Preluăm/inițializăm istoricul din sesiune
        $history = $request->session()->get('chatbot_history', []);

        // Obținem/creăm conversația din DB
        $conversation = $this->getOrCreateConversation($request);

        // Obținem răspunsul de la AI
        $result = $this->chatbotService->getResponse($userMessage, $history);

        // Salvăm mesajele în DB
        $this->saveMessages($conversation, $userMessage, $result);

        // Actualizăm istoricul sesiunii
        $history[] = ['role' => 'user',      'content' => $userMessage];
        $history[] = ['role' => 'assistant', 'content' => $result['message']];

        if (count($history) > 20) {
            $history = array_slice($history, -20);
        }

        $request->session()->put('chatbot_history', $history);

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'actions' => $result['actions'],
        ]);
    }

    /**
     * Înregistrează o conversie (user a dat click pe un buton CTA).
     *
     * POST /api/chatbot/convert
     */
    public function convert(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'url' => ['required', 'string', 'max:500'],
        ]);

        $convId = $request->session()->get('chatbot_conversation_id');
        if ($convId) {
            ChatbotConversation::where('id', $convId)->update([
                'converted'      => true,
                'conversion_url' => $validated['url'],
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Resetează istoricul conversației.
     *
     * POST /api/chatbot/reset
     */
    public function reset(Request $request): JsonResponse
    {
        $request->session()->forget(['chatbot_history', 'chatbot_conversation_id']);

        return response()->json([
            'success' => true,
            'message' => 'Conversație resetată.',
        ]);
    }

    // ─── Private helpers ───────────────────────────────────────────────────────

    private function getOrCreateConversation(Request $request): ChatbotConversation
    {
        $convId = $request->session()->get('chatbot_conversation_id');

        if ($convId) {
            $conversation = ChatbotConversation::find($convId);
            if ($conversation) {
                $conversation->update(['last_activity_at' => now()]);
                return $conversation;
            }
        }

        $conversation = ChatbotConversation::create([
            'session_id'       => $request->session()->getId(),
            'user_id'          => auth()->id(),
            'ip_address'       => $request->ip(),
            'user_agent'       => substr($request->userAgent() ?? '', 0, 255),
            'page_url'         => substr($request->header('Referer', ''), 0, 500),
            'intent'           => 'unknown',
            'converted'        => false,
            'message_count'    => 0,
            'user_messages'    => 0,
            'last_activity_at' => now(),
        ]);

        $request->session()->put('chatbot_conversation_id', $conversation->id);

        return $conversation;
    }

    private function saveMessages(ChatbotConversation $conversation, string $userMessage, array $result): void
    {
        // Salvăm mesajul utilizatorului
        ChatbotMessage::create([
            'conversation_id' => $conversation->id,
            'role'            => 'user',
            'content'         => $userMessage,
            'actions'         => null,
        ]);

        // Salvăm răspunsul asistentului
        ChatbotMessage::create([
            'conversation_id' => $conversation->id,
            'role'            => 'assistant',
            'content'         => $result['message'],
            'actions'         => $result['actions'] ?: null,
        ]);

        // Detectăm intenția din mesajul utilizatorului
        $intent = $this->chatbotService->detectIntent($userMessage);

        // Actualizăm statisticile conversației
        $conversation->increment('message_count', 2);
        $conversation->increment('user_messages');
        $conversation->update([
            'last_activity_at' => now(),
            'intent'           => $intent !== 'unknown' ? $intent : $conversation->intent,
        ]);
    }
}
