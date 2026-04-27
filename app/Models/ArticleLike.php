<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'article_id',
        'type',
    ];

    const TYPE_LIKE = 'like';
    const TYPE_DISLIKE = 'dislike';

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the article
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Toggle like/dislike
     */
    public static function toggle(int $userId, int $articleId, string $type = self::TYPE_LIKE): array
    {
        $existing = static::where('user_id', $userId)
            ->where('article_id', $articleId)
            ->first();

        if ($existing) {
            if ($existing->type === $type) {
                // Same action - remove it
                $existing->delete();
                return ['action' => 'removed', 'type' => null];
            } else {
                // Different action - update it
                $existing->update(['type' => $type]);
                return ['action' => 'changed', 'type' => $type];
            }
        }

        static::create([
            'user_id' => $userId,
            'article_id' => $articleId,
            'type' => $type,
        ]);

        return ['action' => 'added', 'type' => $type];
    }

    /**
     * Get counts for an article
     */
    public static function getCounts(int $articleId): array
    {
        $likes = static::where('article_id', $articleId)
            ->where('type', self::TYPE_LIKE)
            ->count();
        
        $dislikes = static::where('article_id', $articleId)
            ->where('type', self::TYPE_DISLIKE)
            ->count();

        return [
            'likes' => $likes,
            'dislikes' => $dislikes,
            'total' => $likes - $dislikes,
        ];
    }

    /**
     * Get user's reaction to article
     */
    public static function getUserReaction(int $userId, int $articleId): ?string
    {
        return static::where('user_id', $userId)
            ->where('article_id', $articleId)
            ->value('type');
    }
}
