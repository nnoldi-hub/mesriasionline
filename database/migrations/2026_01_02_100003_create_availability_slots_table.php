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
        // Availability slots for booking system
        Schema::create('availability_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['available', 'booked', 'blocked'])->default('available');
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'date', 'status']);
            $table->index(['date', 'status']);
        });

        // Booking settings for craftsmen
        Schema::create('booking_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('accepts_online_booking')->default(true);
            $table->integer('advance_booking_days')->default(30);
            $table->integer('min_notice_hours')->default(24);
            $table->integer('max_bookings_per_day')->nullable();
            $table->boolean('requires_confirmation')->default(true);
            $table->boolean('auto_confirm')->default(false);
            $table->text('booking_instructions')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->integer('cancellation_hours')->default(24);
            $table->decimal('cancellation_fee_percent', 5, 2)->default(0);
            $table->boolean('send_reminders')->default(true);
            $table->integer('reminder_hours_before')->default(24);
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_settings');
        Schema::dropIfExists('availability_slots');
    }
};
