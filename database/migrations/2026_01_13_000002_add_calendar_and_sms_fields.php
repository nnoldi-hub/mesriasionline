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
        // Add Google Calendar fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->text('google_calendar_token')->nullable()->after('vacation_periods');
            $table->string('google_calendar_id')->nullable()->after('google_calendar_token');
            $table->text('outlook_calendar_token')->nullable()->after('google_calendar_id');
            $table->string('outlook_calendar_id')->nullable()->after('outlook_calendar_token');
        });

        // Add SMS reminder fields to booking_settings table
        Schema::table('booking_settings', function (Blueprint $table) {
            $table->boolean('send_sms_reminders')->default(false)->after('send_reminders');
            $table->boolean('send_email_reminders')->default(true)->after('send_sms_reminders');
            $table->integer('sms_reminder_hours_before')->default(24)->after('reminder_hours_before');
        });

        // Add calendar sync fields to appointments table
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('google_calendar_event_id')->nullable()->after('followup_date');
            $table->string('outlook_calendar_event_id')->nullable()->after('google_calendar_event_id');
            $table->timestamp('sms_reminder_sent_at')->nullable()->after('outlook_calendar_event_id');
            $table->timestamp('email_reminder_sent_at')->nullable()->after('sms_reminder_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_calendar_token',
                'google_calendar_id',
                'outlook_calendar_token',
                'outlook_calendar_id',
            ]);
        });

        Schema::table('booking_settings', function (Blueprint $table) {
            $table->dropColumn([
                'send_sms_reminders',
                'send_email_reminders',
                'sms_reminder_hours_before',
            ]);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn([
                'google_calendar_event_id',
                'outlook_calendar_event_id',
                'sms_reminder_sent_at',
                'email_reminder_sent_at',
            ]);
        });
    }
};
