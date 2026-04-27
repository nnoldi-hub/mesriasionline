<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class GoogleCalendarService
{
    protected ?GoogleClient $client = null;
    protected ?GoogleCalendar $service = null;
    protected ?User $user = null;

    /**
     * Initialize the Google Client.
     */
    public function __construct()
    {
        if (!config('services.google.client_id') || !config('services.google.client_secret')) {
            return;
        }

        $this->client = new GoogleClient();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.calendar_redirect_uri'));
        $this->client->addScope(GoogleCalendar::CALENDAR);
        $this->client->addScope(GoogleCalendar::CALENDAR_EVENTS);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
    }

    /**
     * Check if Google Calendar integration is configured.
     */
    public function isConfigured(): bool
    {
        return $this->client !== null;
    }

    /**
     * Set the user for operations.
     */
    public function forUser(User $user): self
    {
        $this->user = $user;
        
        if ($user->google_calendar_token && $this->client) {
            $token = json_decode(Crypt::decryptString($user->google_calendar_token), true);
            $this->client->setAccessToken($token);
            
            // Refresh token if expired
            if ($this->client->isAccessTokenExpired()) {
                if ($this->client->getRefreshToken()) {
                    $newToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                    $user->update([
                        'google_calendar_token' => Crypt::encryptString(json_encode($newToken))
                    ]);
                }
            }
            
            $this->service = new GoogleCalendar($this->client);
        }
        
        return $this;
    }

    /**
     * Check if user has connected Google Calendar.
     */
    public function isConnected(): bool
    {
        return $this->user && $this->user->google_calendar_token && $this->service !== null;
    }

    /**
     * Get OAuth authorization URL.
     */
    public function getAuthUrl(): string
    {
        if (!$this->client) {
            throw new \Exception('Google Calendar nu este configurat.');
        }
        
        return $this->client->createAuthUrl();
    }

    /**
     * Handle OAuth callback and store token.
     */
    public function handleCallback(string $code, User $user): bool
    {
        if (!$this->client) {
            return false;
        }

        try {
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            
            if (isset($token['error'])) {
                Log::error('Google Calendar OAuth error', $token);
                return false;
            }
            
            $user->update([
                'google_calendar_token' => Crypt::encryptString(json_encode($token)),
                'google_calendar_id' => 'primary', // Default calendar
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Google Calendar OAuth exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Disconnect Google Calendar.
     */
    public function disconnect(User $user): bool
    {
        try {
            if ($user->google_calendar_token && $this->client) {
                $token = json_decode(Crypt::decryptString($user->google_calendar_token), true);
                $this->client->revokeToken($token['access_token'] ?? null);
            }
        } catch (\Exception $e) {
            // Token might already be invalid
        }
        
        $user->update([
            'google_calendar_token' => null,
            'google_calendar_id' => null,
        ]);
        
        return true;
    }

    /**
     * Create a calendar event for an appointment.
     */
    public function createEvent(Appointment $appointment): ?string
    {
        if (!$this->isConnected()) {
            return null;
        }

        try {
            $craftsman = $appointment->specialist;
            $calendarId = $craftsman->google_calendar_id ?? 'primary';
            
            $startDateTime = Carbon::parse($appointment->appointment_date)
                ->setTimeFromTimeString($appointment->appointment_time);
            
            // Default duration 1 hour if not specified
            $duration = $appointment->service?->duration ?? 60;
            $endDateTime = $startDateTime->copy()->addMinutes($duration);
            
            $event = new Event([
                'summary' => $this->getEventTitle($appointment),
                'description' => $this->getEventDescription($appointment),
                'start' => [
                    'dateTime' => $startDateTime->toRfc3339String(),
                    'timeZone' => config('app.timezone', 'Europe/Bucharest'),
                ],
                'end' => [
                    'dateTime' => $endDateTime->toRfc3339String(),
                    'timeZone' => config('app.timezone', 'Europe/Bucharest'),
                ],
                'reminders' => [
                    'useDefault' => false,
                    'overrides' => [
                        ['method' => 'popup', 'minutes' => 60],
                        ['method' => 'popup', 'minutes' => 30],
                    ],
                ],
            ]);
            
            // Add location if it's a home service
            if ($appointment->is_home_service && $appointment->client_address) {
                $event->setLocation($appointment->client_address . ', ' . $appointment->client_city);
            }
            
            $createdEvent = $this->service->events->insert($calendarId, $event);
            
            // Store the event ID for future updates/deletions
            $appointment->update(['google_calendar_event_id' => $createdEvent->getId()]);
            
            Log::info("Created Google Calendar event for appointment #{$appointment->id}");
            
            return $createdEvent->getId();
            
        } catch (\Exception $e) {
            Log::error('Failed to create Google Calendar event: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update a calendar event.
     */
    public function updateEvent(Appointment $appointment): bool
    {
        if (!$this->isConnected() || !$appointment->google_calendar_event_id) {
            return false;
        }

        try {
            $craftsman = $appointment->specialist;
            $calendarId = $craftsman->google_calendar_id ?? 'primary';
            
            $event = $this->service->events->get($calendarId, $appointment->google_calendar_event_id);
            
            $startDateTime = Carbon::parse($appointment->appointment_date)
                ->setTimeFromTimeString($appointment->appointment_time);
            $duration = $appointment->service?->duration ?? 60;
            $endDateTime = $startDateTime->copy()->addMinutes($duration);
            
            $event->setSummary($this->getEventTitle($appointment));
            $event->setDescription($this->getEventDescription($appointment));
            
            $event->setStart(new EventDateTime([
                'dateTime' => $startDateTime->toRfc3339String(),
                'timeZone' => config('app.timezone', 'Europe/Bucharest'),
            ]));
            
            $event->setEnd(new EventDateTime([
                'dateTime' => $endDateTime->toRfc3339String(),
                'timeZone' => config('app.timezone', 'Europe/Bucharest'),
            ]));
            
            $this->service->events->update($calendarId, $event->getId(), $event);
            
            Log::info("Updated Google Calendar event for appointment #{$appointment->id}");
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to update Google Calendar event: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a calendar event.
     */
    public function deleteEvent(Appointment $appointment): bool
    {
        if (!$this->isConnected() || !$appointment->google_calendar_event_id) {
            return false;
        }

        try {
            $craftsman = $appointment->specialist;
            $calendarId = $craftsman->google_calendar_id ?? 'primary';
            
            $this->service->events->delete($calendarId, $appointment->google_calendar_event_id);
            
            $appointment->update(['google_calendar_event_id' => null]);
            
            Log::info("Deleted Google Calendar event for appointment #{$appointment->id}");
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to delete Google Calendar event: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sync all upcoming appointments to Google Calendar.
     */
    public function syncAppointments(User $craftsman): array
    {
        $this->forUser($craftsman);
        
        if (!$this->isConnected()) {
            return ['success' => false, 'message' => 'Google Calendar nu este conectat.'];
        }

        $appointments = Appointment::where('specialist_id', $craftsman->id)
            ->where('appointment_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereNull('google_calendar_event_id')
            ->get();

        $synced = 0;
        $failed = 0;

        foreach ($appointments as $appointment) {
            if ($this->createEvent($appointment)) {
                $synced++;
            } else {
                $failed++;
            }
        }

        return [
            'success' => true,
            'synced' => $synced,
            'failed' => $failed,
            'message' => "Au fost sincronizate {$synced} programări."
        ];
    }

    /**
     * Get event title.
     */
    protected function getEventTitle(Appointment $appointment): string
    {
        $serviceName = $appointment->service?->name ?? 'Programare';
        return "📅 {$serviceName} - {$appointment->client_name}";
    }

    /**
     * Get event description.
     */
    protected function getEventDescription(Appointment $appointment): string
    {
        $lines = [
            "Client: {$appointment->client_name}",
            "Telefon: {$appointment->client_phone}",
            "Email: {$appointment->client_email}",
        ];
        
        if ($appointment->service) {
            $lines[] = "Serviciu: {$appointment->service->name}";
        }
        
        if ($appointment->notes) {
            $lines[] = "\nNote: {$appointment->notes}";
        }
        
        if ($appointment->is_home_service) {
            $lines[] = "\n📍 La domiciliul clientului";
            if ($appointment->client_address) {
                $lines[] = "Adresa: {$appointment->client_address}, {$appointment->client_city}";
            }
        }
        
        $lines[] = "\n---";
        $lines[] = "Generat automat de Meseriași.ro";
        
        return implode("\n", $lines);
    }

    /**
     * Get list of user's calendars.
     */
    public function getCalendars(): array
    {
        if (!$this->isConnected()) {
            return [];
        }

        try {
            $calendarList = $this->service->calendarList->listCalendarList();
            $calendars = [];
            
            foreach ($calendarList->getItems() as $calendar) {
                if ($calendar->getAccessRole() === 'owner' || $calendar->getAccessRole() === 'writer') {
                    $calendars[] = [
                        'id' => $calendar->getId(),
                        'name' => $calendar->getSummary(),
                        'primary' => $calendar->getPrimary() ?? false,
                    ];
                }
            }
            
            return $calendars;
        } catch (\Exception $e) {
            Log::error('Failed to get calendars: ' . $e->getMessage());
            return [];
        }
    }
}
