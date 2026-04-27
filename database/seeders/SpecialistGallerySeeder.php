<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Gallery;

class SpecialistGallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialists = User::where('role', 'specialist')->where('is_active', true)->get();

        foreach ($specialists as $specialist) {
            // 2 featured single images
            for ($i = 1; $i <= 2; $i++) {
                Gallery::updateOrCreate(
                    [
                        'user_id' => $specialist->id,
                        'image_path' => "gallery/sample_{$specialist->sub_brand}_feat_{$i}.jpg",
                    ],
                    [
                        'caption' => ucfirst($specialist->sub_brand) . " look #{$i}",
                        'sub_brand' => $specialist->sub_brand,
                        'before_after' => 'single',
                        'is_featured' => true,
                        'service_id' => $specialist->services()->value('id'),
                        'tags' => [$specialist->sub_brand, 'portfolio']
                    ]
                );
            }

            // 2 before/after pairs (4 records)
            for ($i = 1; $i <= 2; $i++) {
                Gallery::updateOrCreate(
                    [
                        'user_id' => $specialist->id,
                        'image_path' => "gallery/sample_{$specialist->sub_brand}_before_{$i}.jpg",
                    ],
                    [
                        'caption' => 'Inainte',
                        'sub_brand' => $specialist->sub_brand,
                        'before_after' => 'before',
                        'is_featured' => false,
                        'service_id' => $specialist->services()->value('id'),
                        'tags' => ['before', $specialist->sub_brand]
                    ]
                );

                Gallery::updateOrCreate(
                    [
                        'user_id' => $specialist->id,
                        'image_path' => "gallery/sample_{$specialist->sub_brand}_after_{$i}.jpg",
                    ],
                    [
                        'caption' => 'Dupa',
                        'sub_brand' => $specialist->sub_brand,
                        'before_after' => 'after',
                        'is_featured' => false,
                        'service_id' => $specialist->services()->value('id'),
                        'tags' => ['after', $specialist->sub_brand]
                    ]
                );
            }
        }
    }
}
