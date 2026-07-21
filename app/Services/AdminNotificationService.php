<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminNotificationService
{
    /**
     * Trimite un email simplu către adresa de admin. Eșecul e logat, nu blochează fluxul apelant.
     */
    public function send(string $subject, string $body): void
    {
        try {
            $adminEmail = env('ADMIN_NOTIFY_EMAIL', 'admin@meseriasionline.ro');

            Mail::raw($body, function ($message) use ($adminEmail, $subject) {
                $message->to($adminEmail)->subject($subject);
            });
        } catch (\Throwable $e) {
            Log::warning('Admin notification email failed: ' . $e->getMessage());
        }
    }
}
