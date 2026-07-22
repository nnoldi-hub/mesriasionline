<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Location;
use App\Models\PublicJobRequest;
use App\Models\User;
use App\Notifications\NewPublicJobRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicJobRequestController extends Controller
{
    public function create()
    {
        $categories = Cache::remember('categories_active', 3600, fn() =>
            Category::where('is_active', true)->orderBy('order')->orderBy('name')->get()
        );

        $locations = Cache::remember('locations_active', 3600, fn() =>
            Location::where('is_active', true)->orderBy('city')->get()
        );

        return view('public-request.create', compact('categories', 'locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'phone'          => 'required|string|max:20',
            'email'          => 'required|email|max:150',
            'category_id'    => 'required|exists:categories,id',
            'location_id'    => 'nullable|exists:locations,id',
            'city'           => 'nullable|string|max:100',
            'title'          => 'required|string|max:200',
            'description'    => 'required|string|min:20|max:2000',
            'preferred_date' => 'nullable|date|after_or_equal:today',
            'urgency'        => 'required|in:flexible,this_week,urgent',
            'budget_max'     => 'nullable|numeric|min:0|max:999999',
        ]);

        $jobRequest = PublicJobRequest::create($validated);

        // Găsește meseriași cu abonament activ în aceeași categorie + locație
        $craftsmenQuery = User::where('role', 'specialist')
            ->where('is_active', true)
            ->where('category_id', $validated['category_id'])
            ->whereHas('subscriptions', function ($q) {
                $q->whereIn('status', ['active', 'trial'])
                  ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()));
            });

        if (!empty($validated['location_id'])) {
            $craftsmenQuery->where('location_id', $validated['location_id']);
        }

        $craftsmen = $craftsmenQuery->get();

        foreach ($craftsmen as $craftsman) {
            try {
                $craftsman->notify(new NewPublicJobRequestNotification($jobRequest));
            } catch (\Exception $e) {
                // Nu opri procesul dacă un email eșuează
                \Log::warning("Public job request notification failed for craftsman #{$craftsman->id}: " . $e->getMessage());
            }
        }

        $jobRequest->update(['notified_craftsmen' => $craftsmen->count()]);

        return redirect()->route('public-request.success', $jobRequest->access_token)
            ->with('success', true);
    }

    public function success(string $token)
    {
        $jobRequest = PublicJobRequest::where('access_token', $token)->firstOrFail();

        return view('public-request.success', compact('jobRequest'));
    }
}
