<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchHistory extends Model
{
    use HasFactory;

    protected $table = 'search_history';

    protected $fillable = [
        'user_id',
        'session_id',
        'query',
        'category_id',
        'location_id',
        'filters',
        'results_count',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'filters' => 'array',
    ];

    /**
     * Get the user who made the search
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category searched
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the location searched
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Record a search
     */
    public static function record(array $data): self
    {
        return static::create([
            'user_id' => $data['user_id'] ?? auth()->id(),
            'session_id' => $data['session_id'] ?? session()->getId(),
            'query' => $data['query'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'location_id' => $data['location_id'] ?? null,
            'filters' => $data['filters'] ?? null,
            'results_count' => $data['results_count'] ?? 0,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get recent searches for user
     */
    public static function getRecentForUser(?int $userId, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        $query = static::with(['category', 'location'])
            ->orderBy('created_at', 'desc')
            ->limit($limit);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', session()->getId());
        }

        return $query->get();
    }

    /**
     * Get popular searches
     */
    public static function getPopular(int $limit = 10): \Illuminate\Support\Collection
    {
        return static::whereNotNull('query')
            ->where('query', '!=', '')
            ->where('created_at', '>=', now()->subDays(30))
            ->select('query')
            ->selectRaw('COUNT(*) as search_count')
            ->groupBy('query')
            ->orderByDesc('search_count')
            ->limit($limit)
            ->pluck('search_count', 'query');
    }

    /**
     * Clear user search history
     */
    public static function clearForUser(int $userId): int
    {
        return static::where('user_id', $userId)->delete();
    }
}
