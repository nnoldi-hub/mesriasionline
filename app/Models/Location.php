<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $city
 * @property string $county
 * @property string $slug
 * @property float|null $latitude
 * @property float|null $longitude
 * @property bool $is_active
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'city',
        'county',
        'slug',
        'latitude',
        'longitude',
        'is_active',
        'meta_title',
        'meta_description'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean'
    ];

    // Accessor pentru name - returnează județul sau orașul
    public function getNameAttribute()
    {
        if ($this->city === $this->county) {
            return $this->city;
        }
        return $this->county . ' - ' . $this->city;
    }

    // Boot method pentru auto-generare slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($location) {
            if (empty($location->slug)) {
                $location->slug = Str::slug($location->city . '-' . $location->county);
            }
        });

        static::updating(function ($location) {
            if (($location->isDirty('city') || $location->isDirty('county')) && empty($location->slug)) {
                $location->slug = Str::slug($location->city . '-' . $location->county);
            }
        });

        static::saved(function () { Cache::forget('locations_active'); });
        static::deleted(function () { Cache::forget('locations_active'); });
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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCounty($query, $county)
    {
        return $query->where('county', $county);
    }

    // Metode helper
    public function getFullNameAttribute()
    {
        return $this->city . ', ' . $this->county;
    }

    public function getCraftsmenCountAttribute()
    {
        return $this->craftsmen()->where('is_active', true)->count();
    }

    public function getUrlAttribute()
    {
        return route('locations.show', $this->slug);
    }

    public function getMetaTitleFullAttribute()
    {
        return $this->meta_title ?: 'Meseriași în ' . $this->city . ' - ' . $this->county;
    }

    public function getMetaDescriptionFullAttribute()
    {
        return $this->meta_description ?: 'Găsește meseriași profesioniști în ' . $this->city . ', ' . $this->county . '. Electricieni, tâmplari, instalatori și mulți alții. Recenzii verificate și prețuri corecte.';
    }

    /**
     * Calculează distanța până la o altă locație (în km)
     */
    public function distanceTo($lat, $lng)
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        $earthRadius = 6371; // km

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }
}
