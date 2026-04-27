<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'craftsman_id',
        'subject',
        'last_message_at',
        'is_archived_by_client',
        'is_archived_by_craftsman',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
            'is_archived_by_client' => 'boolean',
            'is_archived_by_craftsman' => 'boolean',
        ];
    }

    /**
     * Get the client user.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the craftsman user.
     */
    public function craftsman()
    {
        return $this->belongsTo(User::class, 'craftsman_id');
    }

    /**
     * Get all messages in this conversation.
     */
    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get the latest message.
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Get unread messages count for a user.
     */
    public function unreadCountFor(User $user): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Mark all messages as read for a user.
     */
    public function markAsReadFor(User $user): void
    {
        $this->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Get the other participant in the conversation.
     */
    public function getOtherParticipant(User $user): User
    {
        return $user->id === $this->client_id 
            ? $this->craftsman 
            : $this->client;
    }

    /**
     * Check if conversation is archived for a user.
     */
    public function isArchivedFor(User $user): bool
    {
        if ($user->id === $this->client_id) {
            return $this->is_archived_by_client;
        }
        return $this->is_archived_by_craftsman;
    }

    /**
     * Scope for user's conversations.
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('client_id', $user->id)
              ->where('is_archived_by_client', false);
        })->orWhere(function ($q) use ($user) {
            $q->where('craftsman_id', $user->id)
              ->where('is_archived_by_craftsman', false);
        });
    }

    /**
     * Scope for active conversations.
     */
    public function scopeActive($query, User $user)
    {
        return $query->forUser($user)
            ->whereNotNull('last_message_at')
            ->orderBy('last_message_at', 'desc');
    }

    /**
     * Find or create a conversation between two users.
     */
    public static function findOrCreateBetween(int $clientId, int $craftsmanId, ?string $subject = null): self
    {
        return static::firstOrCreate(
            [
                'client_id' => $clientId,
                'craftsman_id' => $craftsmanId,
            ],
            [
                'subject' => $subject,
            ]
        );
    }
}
