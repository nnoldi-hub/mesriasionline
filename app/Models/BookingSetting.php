<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSetting extends Model
{
    protected $fillable = [
        'user_id',
        'accepts_online_booking',
        'advance_booking_days',
        'min_notice_hours',
        'max_bookings_per_day',
        'requires_confirmation',
        'auto_confirm',
        'booking_instructions',
        'cancellation_policy',
        'cancellation_hours',
        'cancellation_fee_percent',
        'send_reminders',
        'reminder_hours_before',
        'send_sms_reminders',
        'send_email_reminders',
        'sms_reminder_hours_before',
    ];

    protected function casts(): array
    {
        return [
            'accepts_online_booking' => 'boolean',
            'requires_confirmation' => 'boolean',
            'auto_confirm' => 'boolean',
            'send_reminders' => 'boolean',
            'send_sms_reminders' => 'boolean',
            'send_email_reminders' => 'boolean',
            'cancellation_fee_percent' => 'decimal:2',
        ];
    }

    /**
     * Get the craftsman who owns these settings.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get default settings for a new craftsman.
     */
    public static function getDefaults(): array
    {
        return [
            'accepts_online_booking' => true,
            'advance_booking_days' => 30,
            'min_notice_hours' => 24,
            'max_bookings_per_day' => null,
            'requires_confirmation' => true,
            'auto_confirm' => false,
            'booking_instructions' => null,
            'cancellation_policy' => 'Anularea gratuită cu cel puțin 24 de ore înainte de programare.',
            'cancellation_hours' => 24,
            'cancellation_fee_percent' => 0,
            'send_sms_reminders' => false,
            'send_email_reminders' => true,
            'sms_reminder_hours_before' => 24,
            'send_reminders' => true,
            'reminder_hours_before' => 24,
        ];
    }

    /**
     * Get or create settings for a user.
     */
    public static function getOrCreate(User $user): self
    {
        return self::firstOrCreate(
            ['user_id' => $user->id],
            self::getDefaults()
        );
    }
}
