<?php

namespace App\Http\Controllers\Craftsman;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\QuoteRequest;
use App\Notifications\QuoteReceivedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuoteController extends Controller
{
    /**
     * Display quote requests received by the craftsman + nearby open requests.
     */
    public function index(Request $request)
    {
        $craftsman = Auth::user();
        
        $query = QuoteRequest::where('craftsman_id', $craftsman->id)
            ->with(['client', 'service', 'quotes']);
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by urgency
        if ($request->filled('urgency')) {
            $query->where('urgency', $request->urgency);
        }
        
        $quoteRequests = $query->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Calculate if craftsman already quoted
        $quoteRequests->each(function ($request) use ($craftsman) {
            $request->my_quote = $request->quotes->where('craftsman_id', $craftsman->id)->first();
        });

        // ── Cereri deschise din zona meseriașului ──
        $nearbyRequests = collect();
        if ($craftsman->latitude && $craftsman->longitude && $craftsman->service_radius_km) {
            $lat = (float) $craftsman->latitude;
            $lng = (float) $craftsman->longitude;
            $radius = (int) $craftsman->service_radius_km;

            $haversine = "(6371 * acos(cos(radians({$lat})) * cos(radians(client_lat)) * cos(radians(client_lng) - radians({$lng})) + sin(radians({$lat})) * sin(radians(client_lat))))";

            $nearbyRequests = QuoteRequest::whereNull('craftsman_id')
                ->where('status', 'pending')
                ->whereNotNull('client_lat')
                ->whereNotNull('client_lng')
                ->whereRaw("{$haversine} <= ?", [$radius])
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->selectRaw("quote_requests.*, {$haversine} AS distance")
                ->with(['client', 'service'])
                ->orderBy('distance')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }
        
        return view('craftsman.quotes.index', compact('quoteRequests', 'nearbyRequests'));
    }

    /**
     * Show a specific quote request.
     */
    public function show(QuoteRequest $quoteRequest)
    {
        $craftsman = Auth::user();
        
        // Verify this request is for the craftsman
        if ($quoteRequest->craftsman_id !== $craftsman->id) {
            abort(403, 'Nu ai acces la această cerere.');
        }
        
        $quoteRequest->load(['client', 'service']);
        
        // Get craftsman's quote if exists
        $myQuote = Quote::where('quote_request_id', $quoteRequest->id)
            ->where('craftsman_id', $craftsman->id)
            ->first();
        
        return view('craftsman.quotes.show', compact('quoteRequest', 'myQuote'));
    }

    /**
     * Store a new quote for a request.
     */
    public function store(Request $request, QuoteRequest $quoteRequest)
    {
        $craftsman = Auth::user();
        
        // Verify this request is for the craftsman
        if ($quoteRequest->craftsman_id !== $craftsman->id) {
            abort(403);
        }
        
        // Check if can still quote
        if (!$quoteRequest->canReceiveQuotes()) {
            return back()->with('error', 'Această cerere nu mai poate primi oferte.');
        }
        
        // Check if already quoted
        $existingQuote = Quote::where('quote_request_id', $quoteRequest->id)
            ->where('craftsman_id', $craftsman->id)
            ->first();
        
        if ($existingQuote) {
            return back()->with('error', 'Ai trimis deja o ofertă pentru această cerere.');
        }
        
        $validated = $request->validate([
            'price' => 'required|numeric|min:1',
            'price_max' => 'nullable|numeric|min:1|gte:price',
            'description' => 'required|string|max:5000',
            'materials_included' => 'nullable|string|max:2000',
            'estimated_duration_hours' => 'nullable|integer|min:1',
            'estimated_duration_days' => 'nullable|integer|min:1',
            'available_from' => 'nullable|date|after_or_equal:today',
            'valid_until' => 'nullable|date|after:today',
        ]);
        
        $quote = Quote::create([
            'quote_request_id' => $quoteRequest->id,
            'craftsman_id' => $craftsman->id,
            'price' => $validated['price'],
            'price_max' => $validated['price_max'] ?? null,
            'description' => $validated['description'],
            'materials_included' => $validated['materials_included'] ?? null,
            'estimated_duration_hours' => $validated['estimated_duration_hours'] ?? null,
            'estimated_duration_days' => $validated['estimated_duration_days'] ?? null,
            'available_from' => $validated['available_from'] ?? null,
            'valid_until' => $validated['valid_until'] ?? now()->addDays(7),
            'status' => 'pending',
        ]);
        
        // Update quote request status
        $quoteRequest->update(['status' => 'quoted']);
        
        // Notify client
        $quoteRequest->client->notify(new QuoteReceivedNotification($quote));
        
        return redirect()->route('craftsman.quotes.show', $quoteRequest)
            ->with('success', 'Oferta a fost trimisă cu succes!');
    }

    /**
     * Edit an existing quote.
     */
    public function edit(QuoteRequest $quoteRequest, Quote $quote)
    {
        $craftsman = Auth::user();
        
        // Verify ownership
        if ($quote->craftsman_id !== $craftsman->id) {
            abort(403);
        }
        
        if (!$quote->isPending()) {
            return back()->with('error', 'Această ofertă nu mai poate fi modificată.');
        }
        
        return view('craftsman.quotes.edit', compact('quoteRequest', 'quote'));
    }

    /**
     * Update an existing quote.
     */
    public function update(Request $request, QuoteRequest $quoteRequest, Quote $quote)
    {
        $craftsman = Auth::user();
        
        // Verify ownership
        if ($quote->craftsman_id !== $craftsman->id) {
            abort(403);
        }
        
        if (!$quote->isPending()) {
            return back()->with('error', 'Această ofertă nu mai poate fi modificată.');
        }
        
        $validated = $request->validate([
            'price' => 'required|numeric|min:1',
            'price_max' => 'nullable|numeric|min:1|gte:price',
            'description' => 'required|string|max:5000',
            'materials_included' => 'nullable|string|max:2000',
            'estimated_duration_hours' => 'nullable|integer|min:1',
            'estimated_duration_days' => 'nullable|integer|min:1',
            'available_from' => 'nullable|date|after_or_equal:today',
            'valid_until' => 'nullable|date|after:today',
        ]);
        
        $quote->update($validated);
        
        return redirect()->route('craftsman.quotes.show', $quoteRequest)
            ->with('success', 'Oferta a fost actualizată.');
    }

    /**
     * Withdraw a quote.
     */
    public function withdraw(QuoteRequest $quoteRequest, Quote $quote)
    {
        $craftsman = Auth::user();
        
        if ($quote->craftsman_id !== $craftsman->id) {
            abort(403);
        }
        
        if (!$quote->withdraw()) {
            return back()->with('error', 'Această ofertă nu poate fi retrasă.');
        }
        
        return redirect()->route('craftsman.quotes.index')
            ->with('success', 'Oferta a fost retrasă.');
    }

    /**
     * Get pending quote requests count for dashboard.
     */
    public function pendingCount()
    {
        $craftsman = Auth::user();
        
        $count = QuoteRequest::where('craftsman_id', $craftsman->id)
            ->where('status', 'pending')
            ->notExpired()
            ->count();
        
        return response()->json(['count' => $count]);
    }
}
