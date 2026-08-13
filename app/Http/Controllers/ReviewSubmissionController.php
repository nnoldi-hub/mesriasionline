<?php

namespace App\Http\Controllers;

use App\Models\QuoteRequest;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewSubmissionController extends Controller
{
    /**
     * Formular public de recenzie (link primit prin email, fără login).
     */
    public function create(string $token)
    {
        $quoteRequest = QuoteRequest::where('review_token', $token)
            ->with('craftsman')
            ->firstOrFail();

        if ($quoteRequest->review()->exists()) {
            return view('reviews.already-submitted', compact('quoteRequest'));
        }

        return view('reviews.create', compact('quoteRequest'));
    }

    /**
     * Salvare recenzie trimisă de client.
     */
    public function store(Request $request, string $token)
    {
        $quoteRequest = QuoteRequest::where('review_token', $token)->firstOrFail();

        if ($quoteRequest->review()->exists()) {
            return redirect()->route('reviews.public.create', $token);
        }

        $validated = $request->validate([
            'rating'                  => 'required|integer|min:1|max:5',
            'comment'                 => 'required|string|max:2000',
            'service_quality_rating'  => 'nullable|integer|min:1|max:5',
            'punctuality_rating'      => 'nullable|integer|min:1|max:5',
            'cleanliness_rating'      => 'nullable|integer|min:1|max:5',
        ]);

        Review::create([
            'quote_request_id'        => $quoteRequest->id,
            'specialist_id'           => $quoteRequest->craftsman_id,
            'client_name'             => $quoteRequest->client->name,
            'rating'                  => $validated['rating'],
            'comment'                 => $validated['comment'],
            'service_quality_rating'  => $validated['service_quality_rating'] ?? null,
            'punctuality_rating'      => $validated['punctuality_rating'] ?? null,
            'cleanliness_rating'      => $validated['cleanliness_rating'] ?? null,
            'is_approved'             => false,
        ]);

        return view('reviews.thanks', compact('quoteRequest'));
    }
}
