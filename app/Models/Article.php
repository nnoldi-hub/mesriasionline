<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'featured_craftsman_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'type',
        'status',
        'tags',
        'views_count',
        'published_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
            // Ensure unique slug
            $originalSlug = $article->slug;
            $count = 1;
            while (static::where('slug', $article->slug)->exists()) {
                $article->slug = $originalSlug . '-' . $count++;
            }
        });
    }

    /**
     * Get the author of the article.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the featured craftsman (for interviews).
     */
    public function featuredCraftsman()
    {
        return $this->belongsTo(User::class, 'featured_craftsman_id');
    }

    /**
     * Get the questions associated with this article.
     */
    public function questions()
    {
        return $this->hasMany(ArticleQuestion::class);
    }

    /**
     * Scope a query to only include published articles.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include articles of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get the type label in Romanian.
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'article' => 'Articol',
            'interview' => 'Interviu',
            'guide' => 'Ghid',
            'news' => 'Știri',
            default => 'Articol',
        };
    }

    /**
     * Get the status label in Romanian.
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'draft' => 'Ciornă',
            'published' => 'Publicat',
            'archived' => 'Arhivat',
            default => 'Ciornă',
        };
    }

    /**
     * Get the featured image URL.
     */
    public function getFeaturedImageUrlAttribute()
    {
        if ($this->featured_image) {
            return asset('storage/' . $this->featured_image);
        }
        return null;
    }

    /**
     * Increment view count.
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }
}
