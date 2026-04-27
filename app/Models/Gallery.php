<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    /**
     * Gallery categories for portfolio organization.
     */
    public const CATEGORIES = [
        'renovari' => 'Renovări',
        'instalatii' => 'Instalații',
        'constructii' => 'Construcții',
        'finisaje' => 'Finisaje',
        'mobilier' => 'Mobilier',
        'electrice' => 'Lucrări Electrice',
        'sanitare' => 'Instalații Sanitare',
        'gradina' => 'Amenajări Grădină',
        'fatade' => 'Fațade & Exterior',
        'design' => 'Design Interior',
        'altele' => 'Altele',
    ];

    protected $fillable = [
        'user_id',
        'service_id',
        'category',
        'image_path',
        'caption',
        'sub_brand',
        'before_after',
        'is_featured',
        'tags',
        'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'tags' => 'array',
    ];

    /**
     * Get the user (craftsman) that owns the gallery image.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service associated with this gallery image.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeOfCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get the category label.
     */
    public function getCategoryLabelAttribute()
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    /**
     * Scope a query to only include featured images.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include single images (not before/after).
     */
    public function scopeSingle($query)
    {
        return $query->where('before_after', 'single');
    }

    /**
     * Scope a query to only include before images.
     */
    public function scopeBefore($query)
    {
        return $query->where('before_after', 'before');
    }

    /**
     * Scope a query to only include after images.
     */
    public function scopeAfter($query)
    {
        return $query->where('before_after', 'after');
    }

    /**
     * Get the full URL of the image.
     */
    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }
}
