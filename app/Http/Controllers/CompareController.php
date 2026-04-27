<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    /**
     * Display the comparison page for selected craftsmen.
     */
    public function index(Request $request)
    {
        $request->validate([
            'ids' => 'required|string'
        ]);

        $ids = array_filter(explode(',', $request->ids));
        
        if (count($ids) < 2) {
            return redirect()->route('home')
                ->with('error', 'Selectați cel puțin 2 meseriași pentru comparație.');
        }

        if (count($ids) > 4) {
            $ids = array_slice($ids, 0, 4);
        }

        $craftsmen = User::whereIn('id', $ids)
            ->where('role', 'specialist')
            ->where('is_active', true)
            ->with([
                'category', 
                'location', 
                'services' => fn($q) => $q->where('is_active', true)->orderBy('name'),
                'reviews' => fn($q) => $q->where('is_approved', true)->latest()->limit(3),
                'gallery' => fn($q) => $q->orderByDesc('is_featured')->limit(4),
                'certifications' => fn($q) => $q->where('is_verified', true)
            ])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->get();

        if ($craftsmen->count() < 2) {
            return redirect()->route('home')
                ->with('error', 'Meseriașii selectați nu au fost găsiți.');
        }

        // Calculate comparison data
        $comparisonData = $this->prepareComparisonData($craftsmen);

        return view('compare.index', compact('craftsmen', 'comparisonData'));
    }

    /**
     * Prepare comparison metrics data.
     */
    protected function prepareComparisonData($craftsmen)
    {
        $data = [
            'maxRating' => $craftsmen->max('reviews_avg_rating') ?? 0,
            'maxReviews' => $craftsmen->max('reviews_count') ?? 0,
            'maxExperience' => $craftsmen->max('experience_years') ?? 0,
            'maxServices' => $craftsmen->max(fn($c) => $c->services->count()),
            'maxCertifications' => $craftsmen->max(fn($c) => $c->certifications->count()),
        ];

        return $data;
    }

    /**
     * Get comparison data via AJAX.
     */
    public function getData(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:2|max:4',
            'ids.*' => 'integer|exists:users,id'
        ]);

        $craftsmen = User::whereIn('id', $request->ids)
            ->where('role', 'specialist')
            ->where('is_active', true)
            ->with(['category', 'location', 'services', 'certifications'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->get();

        return response()->json([
            'success' => true,
            'craftsmen' => $craftsmen->map(function ($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'slug' => $c->slug,
                    'specialization' => $c->specialization,
                    'category' => $c->category?->name,
                    'location' => $c->location?->name,
                    'profile_photo' => $c->profile_photo ? asset('storage/' . $c->profile_photo) : null,
                    'rating' => round($c->reviews_avg_rating ?? 0, 1),
                    'reviews_count' => $c->reviews_count ?? 0,
                    'experience_years' => $c->experience_years ?? 0,
                    'is_verified' => (bool) $c->is_verified,
                    'is_featured' => (bool) $c->is_featured,
                    'services_count' => $c->services->count(),
                    'certifications_count' => $c->certifications->count(),
                    'available_weekends' => (bool) $c->available_weekends,
                    'emergency_services' => (bool) $c->emergency_services,
                    'has_insurance' => (bool) $c->has_insurance,
                    'min_price' => $c->min_price,
                    'max_price' => $c->max_price,
                ];
            })
        ]);
    }
}
