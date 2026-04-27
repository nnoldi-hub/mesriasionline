<?php

namespace App\Http\Controllers\Craftsman;

use App\Http\Controllers\Controller;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;

class CalendarIntegrationController extends Controller
{
    /**
     * Show calendar integration settings.
     */
    public function index(GoogleCalendarService $googleCalendar)
    {
        $craftsman = auth()->user();
        
        $googleConnected = false;
        $googleCalendars = [];
        
        if ($craftsman->google_calendar_token) {
            $googleCalendar->forUser($craftsman);
            $googleConnected = $googleCalendar->isConnected();
            
            if ($googleConnected) {
                $googleCalendars = $googleCalendar->getCalendars();
            }
        }
        
        $outlookConnected = !empty($craftsman->outlook_calendar_token);
        
        return view('craftsman.availability.calendar-integration', compact(
            'craftsman',
            'googleConnected',
            'googleCalendars',
            'outlookConnected'
        ));
    }

    /**
     * Redirect to Google OAuth.
     */
    public function googleConnect(GoogleCalendarService $googleCalendar)
    {
        if (!$googleCalendar->isConfigured()) {
            return back()->with('error', 'Integrarea Google Calendar nu este configurată.');
        }
        
        return redirect($googleCalendar->getAuthUrl());
    }

    /**
     * Handle Google OAuth callback.
     */
    public function googleCallback(Request $request, GoogleCalendarService $googleCalendar)
    {
        if ($request->has('error')) {
            return redirect()->route('craftsman.calendar.integration')
                ->with('error', 'Autorizarea Google Calendar a fost anulată.');
        }

        $code = $request->get('code');
        
        if (!$code) {
            return redirect()->route('craftsman.calendar.integration')
                ->with('error', 'Cod de autorizare lipsă.');
        }

        $craftsman = auth()->user();
        
        if ($googleCalendar->handleCallback($code, $craftsman)) {
            return redirect()->route('craftsman.calendar.integration')
                ->with('success', 'Google Calendar a fost conectat cu succes!');
        }

        return redirect()->route('craftsman.calendar.integration')
            ->with('error', 'A apărut o eroare la conectarea Google Calendar.');
    }

    /**
     * Disconnect Google Calendar.
     */
    public function googleDisconnect(GoogleCalendarService $googleCalendar)
    {
        $craftsman = auth()->user();
        
        $googleCalendar->disconnect($craftsman);
        
        return back()->with('success', 'Google Calendar a fost deconectat.');
    }

    /**
     * Update selected Google Calendar.
     */
    public function updateGoogleCalendar(Request $request)
    {
        $validated = $request->validate([
            'calendar_id' => 'required|string',
        ]);

        $craftsman = auth()->user();
        $craftsman->update(['google_calendar_id' => $validated['calendar_id']]);

        return back()->with('success', 'Calendarul a fost actualizat.');
    }

    /**
     * Sync appointments to Google Calendar.
     */
    public function syncToGoogle(GoogleCalendarService $googleCalendar)
    {
        $craftsman = auth()->user();
        
        $result = $googleCalendar->syncAppointments($craftsman);
        
        if ($result['success']) {
            return back()->with('success', $result['message']);
        }
        
        return back()->with('error', $result['message']);
    }

    /**
     * Redirect to Microsoft OAuth.
     */
    public function outlookConnect()
    {
        $clientId = config('services.microsoft.client_id');
        $tenantId = config('services.microsoft.tenant_id');
        $redirectUri = config('services.microsoft.calendar_redirect_uri');
        
        if (!$clientId) {
            return back()->with('error', 'Integrarea Outlook Calendar nu este configurată.');
        }

        $params = http_build_query([
            'client_id' => $clientId,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'scope' => 'offline_access Calendars.ReadWrite User.Read',
            'response_mode' => 'query',
        ]);

        return redirect("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/authorize?{$params}");
    }

    /**
     * Handle Microsoft OAuth callback.
     */
    public function outlookCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('craftsman.calendar.integration')
                ->with('error', 'Autorizarea Outlook Calendar a fost anulată.');
        }

        $code = $request->get('code');
        
        if (!$code) {
            return redirect()->route('craftsman.calendar.integration')
                ->with('error', 'Cod de autorizare lipsă.');
        }

        $craftsman = auth()->user();
        
        // Exchange code for token
        $response = \Http::asForm()->post(
            'https://login.microsoftonline.com/' . config('services.microsoft.tenant_id') . '/oauth2/v2.0/token',
            [
                'client_id' => config('services.microsoft.client_id'),
                'client_secret' => config('services.microsoft.client_secret'),
                'code' => $code,
                'redirect_uri' => config('services.microsoft.calendar_redirect_uri'),
                'grant_type' => 'authorization_code',
            ]
        );

        if ($response->successful()) {
            $token = $response->json();
            
            $craftsman->update([
                'outlook_calendar_token' => \Crypt::encryptString(json_encode($token)),
                'outlook_calendar_id' => 'primary',
            ]);

            return redirect()->route('craftsman.calendar.integration')
                ->with('success', 'Outlook Calendar a fost conectat cu succes!');
        }

        return redirect()->route('craftsman.calendar.integration')
            ->with('error', 'A apărut o eroare la conectarea Outlook Calendar.');
    }

    /**
     * Disconnect Outlook Calendar.
     */
    public function outlookDisconnect()
    {
        $craftsman = auth()->user();
        
        $craftsman->update([
            'outlook_calendar_token' => null,
            'outlook_calendar_id' => null,
        ]);
        
        return back()->with('success', 'Outlook Calendar a fost deconectat.');
    }
}
