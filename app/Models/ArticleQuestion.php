<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'category_id',
        'author_name',
        'author_email',
        'title',
        'question',
        'answer',
        'answered_by',
        'answered_at',
        'status',
        'is_featured',
        'views_count',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'answered_at' => 'datetime',
    ];

    /**
     * Get the article this question belongs to.
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Get the category of the question.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user who answered the question.
     */
    public function answeredBy()
    {
        return $this->belongsTo(User::class, 'answered_by');
    }

    /**
     * Scope a query to only include approved/answered questions.
     */
    public function scopeVisible($query)
    {
        return $query->whereIn('status', ['approved', 'answered']);
    }

    /**
     * Scope a query to only include answered questions.
     */
    public function scopeAnswered($query)
    {
        return $query->where('status', 'answered');
    }

    /**
     * Scope a query to only include pending questions.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include featured questions.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get the status label in Romanian.
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'În așteptare',
            'approved' => 'Aprobat',
            'answered' => 'Răspuns',
            'rejected' => 'Respins',
            default => 'În așteptare',
        };
    }

    /**
     * Increment view count.
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }
}
