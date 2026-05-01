<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CraftsmanLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'city',
        'trade',
        'experience_range',
        'profile_photo',
        'work_photo',
        'status',
        'admin_notes',
        'invite_token',
        'invite_sent_at',
        'user_id',
        'account_created_at',
        'utm_source',
        'utm_medium',
        'utm_campaign',
    ];

    protected $casts = [
        'invite_sent_at'      => 'datetime',
        'account_created_at'  => 'datetime',
    ];

    // ─── Etichete lizibile ────────────────────────────────────────────────────

    public static array $tradeLabels = [
        'electrician' => 'Electrician',
        'instalator'  => 'Instalator',
        'tamplar'     => 'Tâmplar',
        'zugrav'      => 'Zugrav',
        'mecanic'     => 'Mecanic',
    ];

    public static array $statusLabels = [
        'nou'         => 'Nou',
        'contactat'   => 'Contactat',
        'invitat'     => 'Invitat',
        'inregistrat' => 'Înregistrat',
        'respins'     => 'Respins',
    ];

    public static array $statusColors = [
        'nou'         => 'blue',
        'contactat'   => 'yellow',
        'invitat'     => 'purple',
        'inregistrat' => 'green',
        'respins'     => 'red',
    ];

    // ─── Accesorii ────────────────────────────────────────────────────────────

    public function getTradeLabelAttribute(): string
    {
        return self::$tradeLabels[$this->trade] ?? ucfirst($this->trade);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return self::$statusColors[$this->status] ?? 'gray';
    }

    // ─── Relații ──────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Utilitare ────────────────────────────────────────────────────────────

    /**
     * Generează un token de invitație unic.
     */
    public function generateInviteToken(): string
    {
        $token = Str::random(48);
        $this->update([
            'invite_token'   => $token,
            'invite_sent_at' => now(),
            'status'         => 'invitat',
        ]);
        return $token;
    }

    /**
     * Marchează lead-ul ca înregistrat și leagă contul creat.
     */
    public function markAsRegistered(int $userId): void
    {
        $this->update([
            'status'              => 'inregistrat',
            'user_id'             => $userId,
            'account_created_at'  => now(),
            'invite_token'        => null,
        ]);
    }

    // ─── Scopuri Eloquent ─────────────────────────────────────────────────────

    public function scopeByTrade($query, string $trade)
    {
        return $query->where('trade', $trade);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['nou', 'contactat', 'invitat']);
    }
}
