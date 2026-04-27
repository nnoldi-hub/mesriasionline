<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Coordonate GPS pentru calcul distanță
            $table->decimal('latitude', 10, 8)->nullable()->after('location_id');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            
            // Câmpuri pentru filtre avansate
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->boolean('is_verified')->default(false)->after('is_featured');
            $table->boolean('has_gallery')->default(false)->after('is_verified');
            $table->decimal('min_price', 10, 2)->nullable()->after('transport_fee');
            $table->decimal('max_price', 10, 2)->nullable()->after('min_price');
            $table->integer('response_time_hours')->nullable()->after('max_price'); // Timp mediu de răspuns
            $table->integer('completed_projects')->default(0)->after('response_time_hours');
            $table->timestamp('last_active_at')->nullable()->after('completed_projects');
            
            // Indexuri pentru performanță
            $table->index(['latitude', 'longitude']);
            $table->index('is_featured');
            $table->index('is_verified');
            $table->index('has_gallery');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropIndex(['is_featured']);
            $table->dropIndex(['is_verified']);
            $table->dropIndex(['has_gallery']);
            
            $table->dropColumn([
                'latitude',
                'longitude',
                'is_featured',
                'is_verified',
                'has_gallery',
                'min_price',
                'max_price',
                'response_time_hours',
                'completed_projects',
                'last_active_at',
            ]);
        });
    }
};
