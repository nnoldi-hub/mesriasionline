<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'craftsman_id',
        'notes',
    ];

    /**
     * Get the user who favorited
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the favorited craftsman
     */
    public function craftsman(): BelongsTo
    {
        return $this->belongsTo(User::class, 'craftsman_id');
    }

    /**
     * Check if a craftsman is favorited by a user
     */
    public static function isFavorited(int $userId, int $craftsmanId): bool
    {
        return static::where('user_id', $userId)
            ->where('craftsman_id', $craftsmanId)
            ->exists();
    }

    /**
     * Toggle favorite status
     */
    public static function toggle(int $userId, int $craftsmanId): array
    {
        $existing = static::where('user_id', $userId)
            ->where('craftsman_id', $craftsmanId)
            ->first();

        if ($existing) {
            $existing->delete();
            return ['action' => 'removed', 'favorited' => false];
        }

        static::create([
            'user_id' => $userId,
            'craftsman_id' => $craftsmanId,
        ]);

        return ['action' => 'added', 'favorited' => true];
    }
}
