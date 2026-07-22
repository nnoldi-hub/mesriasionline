<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    public function definition(): array
    {
        $city = fake()->unique()->city();

        return [
            'city' => $city,
            'county' => fake()->word(),
            'slug' => Str::slug($city) . '-' . fake()->unique()->numberBetween(1, 100000),
            'is_active' => true,
        ];
    }
}
