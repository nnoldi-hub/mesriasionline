<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Models\BookingSetting;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendAppointmentReminderSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Appointment $appointment
    ) {}

    /**
     * Execute the job.
     */
    public function handle(SmsService $smsService): void
    {
        // Check if appointment is still valid
        if (!$this->shouldSendReminder()) {
            Log::info("Skipping SMS reminder for appointment #{$this->appointment->id} - not eligible");
            return;
        }

        $success = $smsService->sendAppointmentReminder($this->appointment);

        if ($success) {
            // Mark that reminder was sent
            $this->appointment->update([
                'sms_reminder_sent_at' => now(),
            ]);
            
            Log::info("SMS reminder sent for appointment #{$this->appointment->id}");
        } else {
            Log::warning("Failed to send SMS reminder for appointment #{$this->appointment->id}");
        }
    }

    /**
     * Check if we should send the reminder.
     */
    protected function shouldSendReminder(): bool
    {
        $appointment = $this->appointment->fresh();

        // Already cancelled or completed
        if (in_array($appointment->status, ['cancelled', 'completed', 'no_show'])) {
            return false;
        }

        // Already sent
        if ($appointment->sms_reminder_sent_at) {
            return false;
        }

        // Check craftsman's settings
        $bookingSettings = BookingSetting::where('user_id', $appointment->specialist_id)->first();
        
        if (!$bookingSettings || !$bookingSettings->send_reminders) {
            return false;
        }

        // Check if SMS reminders are enabled for this craftsman
        if (!($bookingSettings->send_sms_reminders ?? false)) {
            return false;
        }

        // Check if phone is valid
        if (empty($appointment->client_phone)) {
            return false;
        }

        return true;
    }

    /**
     * Handle job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SMS reminder job failed for appointment #{$this->appointment->id}: " . $exception->getMessage());
    }
}
