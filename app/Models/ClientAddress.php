<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientAddress extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'street',
        'number',
        'building',
        'entrance',
        'floor',
        'apartment',
        'city',
        'county',
        'postal_code',
        'location_id',
        'latitude',
        'longitude',
        'notes',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    /**
     * Get the user (client) for this address.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the location associated with this address.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get formatted full address.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = [$this->street];
        
        if ($this->number) {
            $parts[0] .= ' nr. ' . $this->number;
        }
        
        if ($this->building) {
            $parts[] = 'Bl. ' . $this->building;
        }
        
        if ($this->entrance) {
            $parts[] = 'Sc. ' . $this->entrance;
        }
        
        if ($this->floor) {
            $parts[] = 'Et. ' . $this->floor;
        }
        
        if ($this->apartment) {
            $parts[] = 'Ap. ' . $this->apartment;
        }
        
        $parts[] = $this->city;
        $parts[] = $this->county;
        
        if ($this->postal_code) {
            $parts[] = $this->postal_code;
        }
        
        return implode(', ', $parts);
    }

    /**
     * Set this address as default and unset others.
     */
    public function setAsDefault(): void
    {
        // Unset other defaults
        static::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);
        
        $this->update(['is_default' => true]);
    }
}
