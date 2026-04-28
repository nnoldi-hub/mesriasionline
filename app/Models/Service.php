<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'description',
        'detailed_description',
        'price',
        'pricing_type',
        'min_price',
        'max_price',
        'duration',
        'min_duration',
        'max_duration',
        'complexity',
        'category',
        'sub_brand',
        'is_on_location',
        'is_active',
        'equipment_needed',
        'materials_included',
        'required_materials',
        'client_provides_materials',
    ];

    protected $casts = [
        'equipment_needed' => 'array',
        'is_on_location' => 'boolean',
        'is_active' => 'boolean',
        'materials_included' => 'boolean',
        'client_provides_materials' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
