<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            CategoriesSeeder::class,
            LocationsSeeder::class,
            CraftsmenSeeder::class,
            CraftsmenServicesSeeder::class,
            // SpecialistUserSeeder::class, // Deprecated - folosim CraftsmenSeeder
            // SpecialistGallerySeeder::class,
            // SpecialistReviewSeeder::class,
        ]);
    }
}
