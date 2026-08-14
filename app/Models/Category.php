<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $icon
 * @property int $order
 * @property bool $is_active
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'order',
        'is_active',
        'meta_title',
        'meta_description',
        'meta_keywords'
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean'
    ];

    // Boot method pentru auto-generare slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::saved(function () { Cache::forget('categories_active'); });
        static::deleted(function () { Cache::forget('categories_active'); });
    }

    // Relatii
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function craftsmen()
    {
        return $this->hasMany(User::class)->where('role', 'specialist');
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('name', 'asc');
    }

    // Metode helper
    public function getCraftsmenCountAttribute()
    {
        return $this->craftsmen()->where('is_active', true)->count();
    }

    public function getServicesCountAttribute()
    {
        return $this->services()->where('is_active', true)->count();
    }

    public function getUrlAttribute()
    {
        return route('landing.category', $this->slug);
    }

    public function getMetaTitleFullAttribute()
    {
        return $this->meta_title ?: $this->name . ' - Meseriași Profesioniști';
    }

    public function getMetaDescriptionFullAttribute()
    {
        return $this->meta_description ?: 'Găsește cei mai buni ' . strtolower($this->name) . ' din zona ta. Recenzii verificate, prețuri transparente, programare rapidă.';
    }
}
