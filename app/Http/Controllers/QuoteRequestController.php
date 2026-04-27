<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class QuoteRequestController extends Controller
{
    /**
     * Afișează formularul de cerere ofertă
     */
    public function create($specialistSlug = null)
    {
        $specialist = null;
        
        if ($specialistSlug) {
            $specialist = User::where('slug', $specialistSlug)
                ->where('role', 'specialist')
                ->where('is_active', true)
                ->with('category', 'location')
                ->firstOrFail();
        }

        return view('quotes.create', compact('specialist'));
    }

    /**
     * Salvează cererea de ofertă
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'specialist_id' => 'required|exists:users,id',
            'service_id' => 'nullable|exists:services,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'required|string|max:20',
            'client_address' => 'required|string|max:255',
            'client_city' => 'required|string|max:100',
            'client_zone' => 'nullable|string|max:100',
            'work_description' => 'required|string|min:20',
            'work_photos.*' => 'nullable|image|max:5120', // max 5MB per image
            'preferred_start_date' => 'required|date|after_or_equal:today',
            'urgency' => 'required|in:low,medium,high,emergency',
            'special_instructions' => 'nullable|string|max:1000'
        ]);

        // Upload poze lucrare
        $workPhotos = [];
        if ($request->hasFile('work_photos')) {
            foreach ($request->file('work_photos') as $photo) {
                $path = $photo->store('quote-requests', 'public');
                $workPhotos[] = $path;
            }
        }

        $quoteRequest = Appointment::create([
            'specialist_id' => $validated['specialist_id'],
            'service_id' => $validated['service_id'] ?? null,
            'request_type' => 'quote',
            'client_name' => $validated['client_name'],
            'client_email' => $validated['client_email'],
            'client_phone' => $validated['client_phone'],
            'client_address' => $validated['client_address'],
            'client_city' => $validated['client_city'],
            'client_zone' => $validated['client_zone'] ?? null,
            'work_description' => $validated['work_description'],
            'work_photos' => $workPhotos,
            'preferred_start_date' => $validated['preferred_start_date'],
            'urgency' => $validated['urgency'],
            'special_instructions' => $validated['special_instructions'] ?? null,
            'is_home_service' => true,
            'status' => 'pending'
        ]);

        // Trimite notificare către meseriaș
        // Mail::to($quoteRequest->specialist->email)->send(new QuoteRequestReceived($quoteRequest));

        return redirect()
            ->route('quotes.success', $quoteRequest->id)
            ->with('success', 'Cererea ta de ofertă a fost trimisă cu succes! Meseriașul va răspunde în cel mai scurt timp.');
    }

    /**
     * Pagină de confirmare după trimiterea cererii
     */
    public function success($id)
    {
        $quoteRequest = Appointment::findOrFail($id);

        return view('quotes.success', compact('quoteRequest'));
    }

    /**
     * Meseriaș trimite ofertă
     */
    public function sendQuote(Request $request, $id)
    {
        $quoteRequest = Appointment::findOrFail($id);

        // Verifică dacă utilizatorul curent este meseriașul pentru această cerere
        if (auth()->id() !== $quoteRequest->specialist_id) {
            abort(403, 'Nu aveți permisiunea să trimiteți ofertă pentru această cerere.');
        }

        if (!$quoteRequest->canSendQuote()) {
            return back()->with('error', 'Nu puteți trimite ofertă pentru această cerere în starea actuală.');
        }

        $validated = $request->validate([
            'quoted_price' => 'required|numeric|min:0',
            'quote_details' => 'required|string|min:50',
            'estimated_duration_hours' => 'required|integer|min:1',
            'quote_valid_until' => 'required|date|after:today',
            'warranty_months' => 'nullable|integer|min:0|max:120'
        ]);

        $quoteRequest->update([
            'quoted_price' => $validated['quoted_price'],
            'quote_details' => $validated['quote_details'],
            'estimated_duration_hours' => $validated['estimated_duration_hours'],
            'quote_valid_until' => $validated['quote_valid_until'],
            'warranty_months' => $validated['warranty_months'] ?? 0,
            'status' => 'quote_sent'
        ]);

        // Trimite email către client
        // Mail::to($quoteRequest->client_email)->send(new QuoteSent($quoteRequest));

        return back()->with('success', 'Oferta a fost trimisă cu succes către client!');
    }

    /**
     * Client acceptă oferta
     */
    public function acceptQuote($id, $token)
    {
        $quoteRequest = Appointment::where('id', $id)
            ->where('review_token', $token)
            ->firstOrFail();

        if (!$quoteRequest->canAcceptQuote()) {
            return back()->with('error', 'Această ofertă nu mai poate fi acceptată.');
        }

        $quoteRequest->update([
            'status' => 'quote_accepted',
            'payment_status' => 'pending'
        ]);

        // Trimite notificare către meseriaș
        // Mail::to($quoteRequest->specialist->email)->send(new QuoteAccepted($quoteRequest));

        return view('quotes.accepted', compact('quoteRequest'))
            ->with('success', 'Oferta a fost acceptată! Meseriașul vă va contacta pentru detalii.');
    }

    /**
     * Client respinge oferta
     */
    public function rejectQuote($id, $token)
    {
        $quoteRequest = Appointment::where('id', $id)
            ->where('review_token', $token)
            ->firstOrFail();

        if ($quoteRequest->status !== 'quote_sent') {
            return back()->with('error', 'Această ofertă nu poate fi respinsă.');
        }

        $quoteRequest->update([
            'status' => 'quote_rejected'
        ]);

        // Trimite notificare către meseriaș
        // Mail::to($quoteRequest->specialist->email)->send(new QuoteRejected($quoteRequest));

        return view('quotes.rejected', compact('quoteRequest'))
            ->with('info', 'Oferta a fost respinsă.');
    }

    /**
     * Meseriaș marchează lucrarea ca începută
     */
    public function startWork(Request $request, $id)
    {
        $quoteRequest = Appointment::findOrFail($id);

        if (auth()->id() !== $quoteRequest->specialist_id) {
            abort(403);
        }

        if (!$quoteRequest->canStartWork()) {
            return back()->with('error', 'Lucrarea nu poate fi începută în starea actuală.');
        }

        $quoteRequest->update([
            'status' => 'in_progress',
            'actual_start_date' => now()
        ]);

        return back()->with('success', 'Lucrarea a fost marcată ca începută!');
    }

    /**
     * Meseriaș marchează lucrarea ca finalizată
     */
    public function completeWork(Request $request, $id)
    {
        $quoteRequest = Appointment::findOrFail($id);

        if (auth()->id() !== $quoteRequest->specialist_id) {
            abort(403);
        }

        if (!$quoteRequest->canComplete()) {
            return back()->with('error', 'Lucrarea nu poate fi marcată ca finalizată în starea actuală.');
        }

        $validated = $request->validate([
            'actual_duration_hours' => 'required|integer|min:1',
            'completion_notes' => 'nullable|string|max:1000',
            'completion_photos.*' => 'nullable|image|max:5120',
            'requires_followup' => 'nullable|boolean',
            'followup_date' => 'nullable|required_if:requires_followup,true|date|after:today'
        ]);

        // Upload poze lucrare finalizată
        $completionPhotos = [];
        if ($request->hasFile('completion_photos')) {
            foreach ($request->file('completion_photos') as $photo) {
                $path = $photo->store('completed-works', 'public');
                $completionPhotos[] = $path;
            }
        }

        // Calculează data expirării garanției
        $warrantyExpiresAt = null;
        if ($quoteRequest->warranty_months > 0) {
            $warrantyExpiresAt = now()->addMonths($quoteRequest->warranty_months);
        }

        $quoteRequest->update([
            'status' => 'completed',
            'actual_end_date' => now(),
            'actual_duration_hours' => $validated['actual_duration_hours'],
            'completion_notes' => $validated['completion_notes'] ?? null,
            'completion_photos' => $completionPhotos,
            'requires_followup' => $validated['requires_followup'] ?? false,
            'followup_date' => $validated['followup_date'] ?? null,
            'warranty_expires_at' => $warrantyExpiresAt,
            'payment_status' => 'paid'
        ]);

        // Generează token pentru recenzie
        $quoteRequest->generateReviewToken();

        // Trimite email către client cu link pentru recenzie
        // Mail::to($quoteRequest->client_email)->send(new WorkCompleted($quoteRequest));

        return back()->with('success', 'Lucrarea a fost marcată ca finalizată! Clientul va primi un email pentru a lăsa o recenzie.');
    }
}
