<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
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
        'referred_by_user_id',
        'referral_reward_given',
    ];

    protected $casts = [
        'invite_sent_at'        => 'datetime',
        'account_created_at'    => 'datetime',
        'referral_reward_given' => 'boolean',
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

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by_user_id');
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

    /**
     * Creează direct contul de meseriaș din datele lead-ului (folosit de admin
     * la crearea manuală a contului, spre deosebire de auto-activarea de către lead).
     */
    public function createUserAccount(string $email, string $password): User
    {
        $category = Category::where('name', 'LIKE', '%' . (self::$tradeLabels[$this->trade] ?? $this->trade) . '%')->first();

        $slug = Str::slug($this->name);
        $originalSlug = $slug;
        $counter = 1;
        while (User::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $user = User::create([
            'name'             => $this->name,
            'email'            => $email,
            'password'         => Hash::make($password),
            'phone'            => $this->phone,
            'role'             => 'specialist',
            'slug'             => $slug,
            'category_id'      => $category?->id,
            'experience_years' => match ($this->experience_range) {
                '0-2'   => 1,
                '3-5'   => 4,
                '5+'    => 6,
                default => 0,
            },
            'profile_photo'    => $this->profile_photo,
            'is_active'        => false, // Necesită aprobare admin
        ]);

        $this->markAsRegistered($user->id);

        return $user;
    }

    /**
     * Recompensează meseriașul care a recomandat acest lead, la conversia lui
     * (indiferent dacă lead-ul și-a activat singur contul sau i l-a creat admin-ul).
     */
    public function rewardReferrer(\App\Services\AdminNotificationService $adminNotifier): void
    {
        $referrer = $this->referredBy;
        if (!$referrer) {
            return;
        }

        $subscription = $referrer->activeSubscription();
        $rewardGiven = false;

        if ($subscription && $subscription->ends_at) {
            $subscription->update(['ends_at' => $subscription->ends_at->addDays(30)]);
            $rewardGiven = true;
        }

        $this->update(['referral_reward_given' => $rewardGiven]);

        $referrer->notify(new \App\Notifications\ReferralConvertedNotification($this, $rewardGiven));

        $adminNotifier->send(
            "Recomandare convertită: {$referrer->name} → {$this->name}",
            "{$referrer->name} a recomandat un coleg care tocmai și-a creat cont: {$this->name} (" .
            (self::$tradeLabels[$this->trade] ?? $this->trade) . ").\n\n" .
            ($rewardGiven
                ? "Recompensă acordată automat: 30 de zile în plus la abonamentul activ al lui {$referrer->name}.\n"
                : "{$referrer->name} nu are un abonament activ cu dată de expirare — nu s-a acordat automat nicio recompensă. Poți decide manual o recompensă.\n") .
            "Profil recomandant: " . url('/admin/craftsmen/' . $referrer->id . '/edit')
        );
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
