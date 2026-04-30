<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatbotConversation extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'ip_address',
        'user_agent',
        'page_url',
        'intent',
        'converted',
        'conversion_url',
        'message_count',
        'user_messages',
        'was_helpful',
        'last_activity_at',
    ];

    protected $casts = [
        'converted'          => 'boolean',
        'was_helpful'        => 'boolean',
        'last_activity_at'   => 'datetime',
    ];

    // ─── Relații ───────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatbotMessage::class, 'conversation_id')->orderBy('created_at');
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeConverted($query)
    {
        return $query->where('converted', true);
    }

    public function scopeByIntent($query, string $intent)
    {
        return $query->where('intent', $intent);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // ─── Accessors ─────────────────────────────────────────────────────────────

    public function getIntentLabelAttribute(): string
    {
        return match ($this->intent) {
            'craftsman_register' => 'Înregistrare meseriaș',
            'client_request'     => 'Cerere client',
            'pricing'            => 'Prețuri / Planuri',
            'info'               => 'Informații generale',
            'support'            => 'Suport',
            'other'              => 'Altele',
            default              => 'Necunoscut',
        };
    }

    public function getIntentColorAttribute(): string
    {
        return match ($this->intent) {
            'craftsman_register' => 'blue',
            'client_request'     => 'green',
            'pricing'            => 'yellow',
            'info'               => 'gray',
            'support'            => 'red',
            default              => 'gray',
        };
    }

    // ─── Stats statice ─────────────────────────────────────────────────────────

    public static function getStats(int $days = 30): array
    {
        $since = now()->subDays($days);

        $total       = static::where('created_at', '>=', $since)->count();
        $converted   = static::where('created_at', '>=', $since)->where('converted', true)->count();
        $byIntent    = static::where('created_at', '>=', $since)
                            ->selectRaw('intent, COUNT(*) as count')
                            ->groupBy('intent')
                            ->pluck('count', 'intent')
                            ->toArray();

        $avgMessages = static::where('created_at', '>=', $since)->avg('message_count') ?? 0;

        $todayCount  = static::whereDate('created_at', today())->count();

        return [
            'total'            => $total,
            'converted'        => $converted,
            'conversion_rate'  => $total > 0 ? round($converted / $total * 100, 1) : 0,
            'by_intent'        => $byIntent,
            'avg_messages'     => round($avgMessages, 1),
            'today'            => $todayCount,
        ];
    }
}
