<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Video extends Model
{
    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('homepage_videos'));
        static::deleted(fn () => Cache::forget('homepage_videos'));
    }

    protected $fillable = [
        'title',
        'description',
        'youtube_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getThumbnailUrlAttribute(): string
    {
        return "https://img.youtube.com/vi/{$this->youtube_id}/hqdefault.jpg";
    }

    public function getEmbedUrlAttribute(): string
    {
        return "https://www.youtube-nocookie.com/embed/{$this->youtube_id}";
    }

    /**
     * Extrage ID-ul video din orice format de URL YouTube (watch, youtu.be, shorts, embed).
     */
    public static function extractYoutubeId(string $url): ?string
    {
        $url = trim($url);

        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $url)) {
            return $url;
        }

        if (preg_match('~(?:youtube\.com/(?:watch\?v=|shorts/|embed/)|youtu\.be/)([A-Za-z0-9_-]{11})~', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
