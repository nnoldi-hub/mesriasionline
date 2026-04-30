<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        DB::table('platform_settings')->insert([
            ['key' => 'facebook_url',  'value' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'instagram_url', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'tiktok_url',    'value' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'youtube_url',   'value' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_email', 'value' => 'contact@meseriasionline.ro', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_phone', 'value' => '+40 740 173 581', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
    }
};
