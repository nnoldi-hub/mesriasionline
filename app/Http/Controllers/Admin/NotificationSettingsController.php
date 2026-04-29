<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class NotificationSettingsController extends Controller
{
    /**
     * Display the notification settings panel.
     */
    public function index()
    {
        $settings = NotificationSetting::orderBy('id')->get()->keyBy('notification_type');

        // SMTP configuration status
        $mailDriver  = config('mail.default');
        $mailHost    = config('mail.mailers.smtp.host');
        $mailPort    = config('mail.mailers.smtp.port');
        $mailFrom    = config('mail.from.address');
        $smtpOk      = $mailDriver === 'smtp' && !empty($mailHost) && !empty(config('mail.mailers.smtp.username'));

        return view('admin.notifications.settings', compact(
            'settings',
            'mailDriver',
            'mailHost',
            'mailPort',
            'mailFrom',
            'smtpOk'
        ));
    }

    /**
     * Bulk-update all notification settings from the form.
     */
    public function update(Request $request)
    {
        $types = NotificationSetting::pluck('notification_type');

        foreach ($types as $type) {
            $row = $request->input('settings.' . $type, []);

            NotificationSetting::where('notification_type', $type)->update([
                'is_enabled'       => isset($row['is_enabled']),
                'email_enabled'    => isset($row['email_enabled']),
                'database_enabled' => isset($row['database_enabled']),
                'push_enabled'     => isset($row['push_enabled']),
            ]);
        }

        NotificationSetting::clearCache();

        return redirect()->route('admin.notifications.settings')
            ->with('success', 'Setările de notificări au fost salvate.');
    }

    /**
     * Send a test email to verify SMTP configuration.
     */
    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => ['required', 'email', 'max:255'],
        ]);

        try {
            Mail::raw(
                "Acesta este un email de test trimis din panoul de administrare al platformei Meseriași Online.\n\n"
                . "Dacă ai primit acest email, configurația SMTP funcționează corect.\n\n"
                . "Data test: " . now()->format('d.m.Y H:i:s'),
                function (Message $message) use ($request) {
                    $message->to($request->test_email)
                            ->subject('[Test] Email notificare - Meseriași Online');
                }
            );

            return back()->with('test_success', 'Email de test trimis cu succes la ' . $request->test_email . '!');
        } catch (\Exception $e) {
            return back()->with('test_error', 'Eroare la trimiterea emailului: ' . $e->getMessage());
        }
    }
}
