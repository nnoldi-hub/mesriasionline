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
            // Video presentation
            $table->string('video_url')->nullable()->after('website_url');
            $table->string('video_thumbnail')->nullable()->after('video_url');
            
            // Enhanced working hours structure
            $table->json('weekly_schedule')->nullable()->after('working_hours');
            $table->json('break_times')->nullable()->after('weekly_schedule');
            $table->integer('slot_duration_minutes')->default(60)->after('break_times');
            $table->integer('buffer_between_slots')->default(15)->after('slot_duration_minutes');
            
            // Vacation / unavailable periods
            $table->json('vacation_periods')->nullable()->after('buffer_between_slots');
            
            // Coverage area enhanced
            $table->json('coverage_zones')->nullable()->after('coverage_area');
            $table->decimal('extra_fee_per_km', 8, 2)->nullable()->after('transport_fee');
            
            // Additional profile info
            $table->text('short_bio')->nullable()->after('description');
            $table->json('languages_spoken')->nullable()->after('short_bio');
            $table->json('payment_methods')->nullable()->after('languages_spoken');
            $table->boolean('offers_free_estimate')->default(false)->after('payment_methods');
            $table->boolean('offers_warranty')->default(false)->after('offers_free_estimate');
            $table->integer('warranty_months')->nullable()->after('offers_warranty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'video_url',
                'video_thumbnail',
                'weekly_schedule',
                'break_times',
                'slot_duration_minutes',
                'buffer_between_slots',
                'vacation_periods',
                'coverage_zones',
                'extra_fee_per_km',
                'short_bio',
                'languages_spoken',
                'payment_methods',
                'offers_free_estimate',
                'offers_warranty',
                'warranty_months',
            ]);
        });
    }
};
