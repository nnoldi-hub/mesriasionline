<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Models\User;
use App\Notifications\NewQuoteRequestNotification;
use App\Notifications\QuoteReceivedNotification;
use App\Notifications\QuoteAcceptedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class QuoteController extends Controller
{
    /**
     * Display quote requests for the authenticated user (client view).
     */
    public function index()
    {
        $user = Auth::user();
        
        $quoteRequests = QuoteRequest::where('client_id', $user->id)
            ->with(['craftsman', 'service', 'quotes.craftsman'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('quotes.index', compact('quoteRequests'));
    }

    /**
     * Show the form to create a new quote request.
     */
    public function create(Request $request)
    {
        $craftsmanId = $request->query('craftsman');
        $serviceId = $request->query('service');
        
        $craftsman = null;
        $service = null;
        
        if ($craftsmanId) {
            $craftsman = User::where('id', $craftsmanId)
                ->where('role', 'specialist')
                ->where('is_active', true)
                ->with('services')
                ->first();
        }
        
        if ($serviceId) {
            $service = Service::find($serviceId);
        }
        
        return view('quotes.create', compact('craftsman', 'service'));
    }

    /**
     * Store a new quote request.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'craftsman_id' => 'required|exists:users,id',
            'service_id' => 'nullable|exists:services,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'g-recaptcha-response' => 'required|captcha',
            'location' => 'nullable|string|max:255',
            'preferred_date' => 'nullable|date|after:today',
            'preferred_time' => 'nullable|in:morning,afternoon,evening',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0|gte:budget_min',
            'urgency' => 'required|in:low,normal,high,urgent',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);
        
        // Handle images upload
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('quote-requests', 'public');
            }
        }
        
        // Set expiration based on urgency
        $expiresAt = match($validated['urgency']) {
            'urgent' => now()->addDays(2),
            'high' => now()->addDays(5),
            'normal' => now()->addDays(7),
            'low' => now()->addDays(14),
        };
        
        $quoteRequest = QuoteRequest::create([
            'client_id' => $user->id,
            'craftsman_id' => $validated['craftsman_id'],
            'service_id' => $validated['service_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location' => $validated['location'] ?? null,
            'preferred_date' => $validated['preferred_date'] ?? null,
            'preferred_time' => $validated['preferred_time'] ?? null,
            'budget_min' => $validated['budget_min'] ?? null,
            'budget_max' => $validated['budget_max'] ?? null,
            'urgency' => $validated['urgency'],
            'images' => $imagePaths,
            'expires_at' => $expiresAt,
        ]);
        
        // Notify craftsman
        $craftsman = User::find($validated['craftsman_id']);
        $craftsman->notify(new NewQuoteRequestNotification($quoteRequest));
        
        return redirect()->route('quotes.show', $quoteRequest)
            ->with('success', 'Cererea de ofertă a fost trimisă cu succes!');
    }

    /**
     * Show a specific quote request.
     */
    public function show(QuoteRequest $quoteRequest)
    {
        $user = Auth::user();
        
        // Only client can view their request
        if ($quoteRequest->client_id !== $user->id) {
            abort(403, 'Nu ai acces la această cerere.');
        }
        
        $quoteRequest->load(['craftsman', 'service', 'quotes.craftsman']);
        
        return view('quotes.show', compact('quoteRequest'));
    }

    /**
     * Accept a quote.
     */
    public function acceptQuote(QuoteRequest $quoteRequest, Quote $quote)
    {
        $user = Auth::user();
        
        // Verify ownership
        if ($quoteRequest->client_id !== $user->id) {
            abort(403);
        }
        
        // Verify quote belongs to this request
        if ($quote->quote_request_id !== $quoteRequest->id) {
            abort(404);
        }
        
        if (!$quote->canBeAccepted()) {
            return back()->with('error', 'Această ofertă nu mai poate fi acceptată.');
        }
        
        $quote->accept();
        
        // Notify craftsman
        $quote->craftsman->notify(new QuoteAcceptedNotification($quote));
        
        return redirect()->route('quotes.show', $quoteRequest)
            ->with('success', 'Oferta a fost acceptată! Meseriașul va fi notificat.');
    }

    /**
     * Reject a quote.
     */
    public function rejectQuote(Request $request, QuoteRequest $quoteRequest, Quote $quote)
    {
        $user = Auth::user();
        
        // Verify ownership
        if ($quoteRequest->client_id !== $user->id) {
            abort(403);
        }
        
        // Verify quote belongs to this request
        if ($quote->quote_request_id !== $quoteRequest->id) {
            abort(404);
        }
        
        $reason = $request->input('reason');
        $quote->reject($reason);
        
        return redirect()->route('quotes.show', $quoteRequest)
            ->with('success', 'Oferta a fost respinsă.');
    }

    /**
     * Cancel a quote request.
     */
    public function cancel(QuoteRequest $quoteRequest)
    {
        $user = Auth::user();
        
        if ($quoteRequest->client_id !== $user->id) {
            abort(403);
        }
        
        if ($quoteRequest->status !== 'pending') {
            return back()->with('error', 'Această cerere nu mai poate fi anulată.');
        }
        
        $quoteRequest->update(['status' => 'expired']);
        
        return redirect()->route('quotes.index')
            ->with('success', 'Cererea a fost anulată.');
    }

    /**
     * Mark a quote request as completed.
     */
    public function complete(QuoteRequest $quoteRequest)
    {
        $user = Auth::user();
        
        if ($quoteRequest->client_id !== $user->id) {
            abort(403);
        }
        
        if ($quoteRequest->status !== 'accepted') {
            return back()->with('error', 'Doar cererile acceptate pot fi marcate ca finalizate.');
        }
        
        $quoteRequest->update(['status' => 'completed']);
        
        return redirect()->route('quotes.show', $quoteRequest)
            ->with('success', 'Lucrarea a fost marcată ca finalizată. Mulțumim!');
    }
}
