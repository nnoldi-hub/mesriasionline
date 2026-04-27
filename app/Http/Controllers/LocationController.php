<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Afișează lista tuturor locațiilor
     */
    public function index()
    {
        $locations = Location::active()
            ->orderBy('county')
            ->orderBy('city')
            ->withCount(['craftsmen' => function ($query) {
                $query->where('is_active', true);
            }])
            ->get()
            ->groupBy('county');

        return view('locations.index', compact('locations'));
    }

    /**
     * Afișează o locație specifică cu meseriașii ei
     */
    public function show($slug)
    {
        $location = Location::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $craftsmen = User::where('role', 'specialist')
            ->where('location_id', $location->id)
            ->where('is_active', true)
            ->with(['category', 'services', 'reviews'])
            ->withCount(['reviews as reviews_count'])
            ->withAvg(['reviews as average_rating'], 'rating')
            ->paginate(12);

        $categories = Category::active()
            ->ordered()
            ->whereHas('craftsmen', function ($query) use ($location) {
                $query->where('location_id', $location->id)
                      ->where('is_active', true);
            })
            ->withCount(['craftsmen' => function ($query) use ($location) {
                $query->where('location_id', $location->id)
                      ->where('is_active', true);
            }])
            ->get();

        // SEO meta tags
        $metaTitle = $location->meta_title_full;
        $metaDescription = $location->meta_description_full;

        return view('locations.show', compact('location', 'craftsmen', 'categories', 'metaTitle', 'metaDescription'));
    }

    /**
     * Filtrare meseriași după locație și categorie
     */
    public function filter(Request $request, $slug)
    {
        $location = Location::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $query = User::where('role', 'specialist')
            ->where('location_id', $location->id)
            ->where('is_active', true);

        // Filtrare după categorie
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filtrare după rating
        if ($request->has('min_rating') && $request->min_rating) {
            $query->whereHas('reviews', function ($q) use ($request) {
                $q->selectRaw('AVG(rating) as avg_rating')
                  ->groupBy('specialist_id')
                  ->havingRaw('AVG(rating) >= ?', [$request->min_rating]);
            });
        }

        // Filtrare după asigurare
        if ($request->has('has_insurance') && $request->has_insurance) {
            $query->where('has_insurance', true);
        }

        // Filtrare după servicii urgență
        if ($request->has('emergency_services') && $request->emergency_services) {
            $query->where('emergency_services', true);
        }

        // Sortare
        $sortBy = $request->get('sort_by', 'rating');
        switch ($sortBy) {
            case 'rating':
                $query->withAvg(['reviews as average_rating'], 'rating')
                      ->orderByDesc('average_rating');
                break;
            case 'reviews':
                $query->withCount('reviews')
                      ->orderByDesc('reviews_count');
                break;
            case 'experience':
                $query->orderByDesc('experience_years');
                break;
        }

        $craftsmen = $query->with(['category', 'services', 'reviews'])
            ->withCount(['reviews as reviews_count'])
            ->paginate(12);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('locations.partials.craftsmen-list', compact('craftsmen'))->render(),
                'pagination' => $craftsmen->links()->render()
            ]);
        }

        $categories = Category::active()->ordered()->get();
        return view('locations.show', compact('location', 'craftsmen', 'categories'));
    }

    /**
     * Caută meseriași în proximitate
     */
    public function nearby(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'integer|min:1|max:100'
        ]);

        $lat = $request->latitude;
        $lng = $request->longitude;
        $radius = $request->get('radius', 30); // km

        // Găsim locațiile în proximitate
        $nearbyLocations = Location::active()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->filter(function ($location) use ($lat, $lng, $radius) {
                $distance = $location->distanceTo($lat, $lng);
                return $distance !== null && $distance <= $radius;
            })
            ->pluck('id');

        $craftsmen = User::where('role', 'specialist')
            ->where('is_active', true)
            ->whereIn('location_id', $nearbyLocations)
            ->with(['category', 'location', 'services', 'reviews'])
            ->withCount(['reviews as reviews_count'])
            ->withAvg(['reviews as average_rating'], 'rating')
            ->paginate(12);

        return view('locations.nearby', compact('craftsmen', 'radius'));
    }
}
