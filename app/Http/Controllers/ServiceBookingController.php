<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Http\Request;

class ServiceBookingController extends Controller
{
    // Formular generic pentru solicitare mentenanță/întreținere
    public function submitGenericRequest(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'service_type' => 'required|in:intretinere,mentenanta',
            'location' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);
        
        // Salvează ca solicitare generică (Appointment fără service_id)
        $appointment = Appointment::create([
            'service_id' => null,
            'user_id' => null,
            'client_name' => $validated['client_name'],
            'client_phone' => $validated['client_phone'],
            'client_email' => $validated['client_email'] ?? null,
            'appointment_date' => now()->toDateString(),
            'appointment_time' => now()->format('H:i'),
            'message' => 'Tip serviciu: ' . $validated['service_type'] . "\nLocație: " . $validated['location'] . "\n" . $validated['message'],
            'status' => 'pending',
        ]);
        
        // Notificare email către admin
        \Illuminate\Support\Facades\Mail::raw(
            "Solicitare nouă de mentenanță/întreținere:\n" .
            "Nume: {$appointment->client_name}\n" .
            "Telefon: {$appointment->client_phone}\n" .
            "Email: {$appointment->client_email}\n" .
            "Detalii: {$appointment->message}",
            function($message) {
                $message->to(config('mail.from.address', 'contact@fixacasa.ro'));
                $message->subject('Solicitare nouă mentenanță/întreținere Fixacasa');
            }
        );
        
        return redirect()->route('home')
            ->with('success', 'Solicitarea ta a fost trimisă! Echipa Fixacasa te va contacta pentru ofertă personalizată.');
    }

    // Show booking form for a service
    public function showBookingForm(Service $service)
    {
        // Only allow booking for maintenance/technical services
        if (!$service->category || !in_array($service->category->name, ['Intretinere imobile', 'Mentenanta'])) {
            abort(404);
        }
        return view('services.book', compact('service'));
    }

    // Handle booking submission
    public function submitBooking(Request $request, Service $service)
    {
        if (!$service->category || !in_array($service->category->name, ['Intretinere imobile', 'Mentenanta'])) {
            abort(404);
        }
        
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'message' => 'nullable|string|max:500',
        ]);
        
        // Creează o solicitare generică pentru admin, nu o programare directă
        $appointment = Appointment::create([
            'service_id' => $service->id,
            'user_id' => null,
            'client_name' => $validated['client_name'],
            'client_phone' => $validated['client_phone'],
            'client_email' => $validated['client_email'] ?? null,
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
        ]);
        
        // Notificare email către admin
        \Illuminate\Support\Facades\Mail::raw(
            "Rezervare nouă serviciu:\n" .
            "Nume: {$appointment->client_name}\n" .
            "Telefon: {$appointment->client_phone}\n" .
            "Email: {$appointment->client_email}\n" .
            "Serviciu: {$service->name}\n" .
            "Data: {$appointment->appointment_date} {$appointment->appointment_time}\n" .
            "Detalii: {$appointment->message}",
            function($message) {
                $message->to(config('mail.from.address', 'contact@fixacasa.ro'));
                $message->subject('Rezervare nouă serviciu Fixacasa');
            }
        );
        
        return redirect()->route('home')
            ->with('success', 'Solicitarea ta a fost trimisă către echipa Fixacasa! Un administrator te va contacta pentru soluția potrivită.');
    }
}
