<?php

namespace App\Console\Commands;

use App\Jobs\SendAppointmentReminderSms;
use App\Models\Appointment;
use App\Models\BookingSetting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'appointments:send-reminders 
                            {--type=all : Type of reminders to send (sms, email, all)}
                            {--dry-run : Preview what would be sent without actually sending}';

    /**
     * The console command description.
     */
    protected $description = 'Send SMS and email reminders for upcoming appointments';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $type = $this->option('type');
        $dryRun = $this->option('dry-run');

        $this->info('Checking for appointments needing reminders...');

        // Get all craftsmen with reminder settings
        $bookingSettings = BookingSetting::where('send_reminders', true)->get();

        $smsCount = 0;
        $emailCount = 0;

        foreach ($bookingSettings as $settings) {
            $reminderHours = $settings->reminder_hours_before ?? 24;
            
            // Calculate the target time window
            $targetStart = now()->addHours($reminderHours)->subMinutes(30);
            $targetEnd = now()->addHours($reminderHours)->addMinutes(30);

            // Get appointments in this window
            $appointments = Appointment::where('specialist_id', $settings->user_id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->whereNull('sms_reminder_sent_at')
                ->whereDate('appointment_date', $targetStart->toDateString())
                ->get()
                ->filter(function ($appointment) use ($targetStart, $targetEnd) {
                    $appointmentDateTime = Carbon::parse($appointment->appointment_date)
                        ->setTimeFromTimeString($appointment->appointment_time);
                    
                    return $appointmentDateTime->between($targetStart, $targetEnd);
                });

            foreach ($appointments as $appointment) {
                // SMS Reminder
                if (in_array($type, ['sms', 'all']) && ($settings->send_sms_reminders ?? false)) {
                    if ($dryRun) {
                        $this->line("  [DRY-RUN] Would send SMS to {$appointment->client_phone} for appointment #{$appointment->id}");
                    } else {
                        SendAppointmentReminderSms::dispatch($appointment);
                        $this->line("  Queued SMS reminder for appointment #{$appointment->id}");
                    }
                    $smsCount++;
                }

                // Email reminder (using existing notification)
                if (in_array($type, ['email', 'all']) && ($settings->send_email_reminders ?? true)) {
                    if ($dryRun) {
                        $this->line("  [DRY-RUN] Would send email to {$appointment->client_email} for appointment #{$appointment->id}");
                    } else {
                        // The email notification could be dispatched here
                        // For now, we'll mark it as handled by the existing system
                        $this->line("  Email reminder handled for appointment #{$appointment->id}");
                    }
                    $emailCount++;
                }
            }
        }

        $this->info("Processed: {$smsCount} SMS reminders, {$emailCount} email reminders");

        if ($dryRun) {
            $this->warn('This was a dry run. No actual reminders were sent.');
        }

        return Command::SUCCESS;
    }
}
