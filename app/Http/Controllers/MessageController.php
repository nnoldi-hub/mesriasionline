<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    /**
     * Display all conversations for the authenticated user.
     */
    public function index()
    {
        $user = Auth::user();
        
        $conversations = Conversation::query()
            ->where(function ($query) use ($user) {
                $query->where('client_id', $user->id)
                      ->where('is_archived_by_client', false);
            })
            ->orWhere(function ($query) use ($user) {
                $query->where('craftsman_id', $user->id)
                      ->where('is_archived_by_craftsman', false);
            })
            ->with(['client', 'craftsman', 'latestMessage'])
            ->whereNotNull('last_message_at')
            ->orderBy('last_message_at', 'desc')
            ->paginate(20);
        
        // Calculate unread counts for each conversation
        $conversations->each(function ($conversation) use ($user) {
            $conversation->unread_count = $conversation->unreadCountFor($user);
            $conversation->other_participant = $conversation->getOtherParticipant($user);
        });
        
        return view('messages.index', compact('conversations'));
    }

    /**
     * Show a specific conversation.
     */
    public function show(Conversation $conversation)
    {
        $user = Auth::user();
        
        // Check if user is part of this conversation
        if ($conversation->client_id !== $user->id && $conversation->craftsman_id !== $user->id) {
            abort(403, 'Nu ai acces la această conversație.');
        }
        
        // Mark messages as read
        $conversation->markAsReadFor($user);
        
        // Load messages
        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();
        
        $otherParticipant = $conversation->getOtherParticipant($user);
        
        return view('messages.show', compact('conversation', 'messages', 'otherParticipant'));
    }

    /**
     * Start a new conversation with a craftsman.
     */
    public function create(Request $request)
    {
        $craftsmanId = $request->query('craftsman');
        $craftsman = null;
        
        if ($craftsmanId) {
            $craftsman = User::where('id', $craftsmanId)
                ->where('role', 'specialist')
                ->where('is_active', true)
                ->first();
        }
        
        return view('messages.create', compact('craftsman'));
    }

    /**
     * Store a new conversation and first message.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'craftsman_id' => 'required|exists:users,id',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:5120',
        ]);
        
        $craftsman = User::where('id', $validated['craftsman_id'])
            ->where('role', 'specialist')
            ->where('is_active', true)
            ->firstOrFail();
        
        // Create or get existing conversation
        $conversation = Conversation::findOrCreateBetween(
            $user->id,
            $craftsman->id,
            $validated['subject'] ?? null
        );
        
        // Handle attachment
        $attachmentPath = null;
        $attachmentType = null;
        
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('messages/attachments', 'public');
            $attachmentType = $this->getAttachmentType($file->getMimeType());
        }
        
        // Create the message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'body' => $validated['message'],
            'attachment' => $attachmentPath,
            'attachment_type' => $attachmentType,
        ]);
        
        // Send notification to craftsman
        $craftsman->notify(new NewMessageNotification($message));
        
        return redirect()->route('messages.show', $conversation)
            ->with('success', 'Mesajul a fost trimis cu succes!');
    }

    /**
     * Send a reply in an existing conversation.
     */
    public function reply(Request $request, Conversation $conversation)
    {
        $user = Auth::user();
        
        // Check if user is part of this conversation
        if ($conversation->client_id !== $user->id && $conversation->craftsman_id !== $user->id) {
            abort(403, 'Nu ai acces la această conversație.');
        }
        
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:5120',
        ]);
        
        // Handle attachment
        $attachmentPath = null;
        $attachmentType = null;
        
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('messages/attachments', 'public');
            $attachmentType = $this->getAttachmentType($file->getMimeType());
        }
        
        // Create the message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'body' => $validated['message'],
            'attachment' => $attachmentPath,
            'attachment_type' => $attachmentType,
        ]);
        
        // Send notification to the other participant
        $otherParticipant = $conversation->getOtherParticipant($user);
        $otherParticipant->notify(new NewMessageNotification($message));
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('sender'),
            ]);
        }
        
        return redirect()->route('messages.show', $conversation)
            ->with('success', 'Mesajul a fost trimis!');
    }

    /**
     * Archive a conversation.
     */
    public function archive(Conversation $conversation)
    {
        $user = Auth::user();
        
        if ($conversation->client_id === $user->id) {
            $conversation->update(['is_archived_by_client' => true]);
        } elseif ($conversation->craftsman_id === $user->id) {
            $conversation->update(['is_archived_by_craftsman' => true]);
        } else {
            abort(403);
        }
        
        return redirect()->route('messages.index')
            ->with('success', 'Conversația a fost arhivată.');
    }

    /**
     * Get unread messages count for API.
     */
    public function unreadCount()
    {
        $user = Auth::user();
        
        $count = Message::whereHas('conversation', function ($query) use ($user) {
            $query->where('client_id', $user->id)
                  ->orWhere('craftsman_id', $user->id);
        })
        ->where('sender_id', '!=', $user->id)
        ->whereNull('read_at')
        ->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Get attachment type from mime type.
     */
    private function getAttachmentType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }
        
        return 'document';
    }
}
