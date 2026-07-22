<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Category;
use App\Models\Location;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Cache::remember('categories_active', 1800, function () {
            return Category::where('is_active', true)->orderBy('order')->get();
        });

        $locations = Cache::remember('locations_active', 1800, function () {
            return Location::where('is_active', true)->orderBy('city')->get();
        });

        // Get user coordinates if provided
        $userLat = $request->filled('lat') ? (float) $request->lat : null;
        $userLng = $request->filled('lng') ? (float) $request->lng : null;
        $searchRadius = $request->filled('radius') ? (int) $request->radius : 50;

        $query = User::where('role', 'specialist')
            ->where('is_active', true)
            ->with(['category', 'location', 'gallery']);

        // ===== BASIC FILTERS =====
        
        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Location filter
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        // Text search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('specialization', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        // ===== ADVANCED FILTERS =====

        // Featured/Recommended filter
        if ($request->filled('featured') && $request->featured) {
            $query->where('is_featured', true);
        }

        // Verified craftsmen filter
        if ($request->filled('verified') && $request->verified) {
            $query->where('is_verified', true);
        }

        // Has gallery photos filter - verifică dacă are poze în galerie
        if ($request->filled('has_gallery') && $request->has_gallery) {
            $query->whereHas('gallery');
        }

        // Minimum rating filter
        if ($request->filled('min_rating') && $request->min_rating > 0) {
            $minRating = (float) $request->min_rating;
            $query->whereHas('reviews', function ($q) {
                $q->select('specialist_id')
                  ->groupBy('specialist_id');
            })->withAvg('reviews', 'rating')
              ->having('reviews_avg_rating', '>=', $minRating);
        }

        // Minimum reviews count filter
        if ($request->filled('min_reviews') && $request->min_reviews > 0) {
            $query->has('reviews', '>=', (int) $request->min_reviews);
        }

        // Has any reviews filter
        if ($request->filled('has_reviews') && $request->has_reviews) {
            $query->has('reviews');
        }

        // Experience years filter
        if ($request->filled('min_experience') && $request->min_experience > 0) {
            $query->where('experience_years', '>=', (int) $request->min_experience);
        }

        // Available weekends filter
        if ($request->filled('available_weekends') && $request->available_weekends) {
            $query->where('available_weekends', true);
        }

        // Emergency services filter
        if ($request->filled('emergency_services') && $request->emergency_services) {
            $query->where('emergency_services', true);
        }

        // Has insurance filter
        if ($request->filled('has_insurance') && $request->has_insurance) {
            $query->where('has_insurance', true);
        }

        // Price range filter
        if ($request->filled('price_min') || $request->filled('price_max')) {
            if ($request->filled('price_min')) {
                $query->where(function ($q) use ($request) {
                    $q->where('min_price', '>=', (float) $request->price_min)
                      ->orWhereNull('min_price');
                });
            }
            if ($request->filled('price_max')) {
                $query->where(function ($q) use ($request) {
                    $q->where('max_price', '<=', (float) $request->price_max)
                      ->orWhereNull('max_price');
                });
            }
        }

        // ===== PROXIMITY FILTER (Geolocation) =====
        if ($userLat && $userLng) {
            // Add distance calculation using Haversine formula
            $haversine = "(6371 * acos(cos(radians({$userLat})) * cos(radians(latitude)) * cos(radians(longitude) - radians({$userLng})) + sin(radians({$userLat})) * sin(radians(latitude))))";
            
            $query->selectRaw("users.*, {$haversine} AS distance")
                  ->whereNotNull('latitude')
                  ->whereNotNull('longitude')
                  ->whereRaw("{$haversine} <= ?", [$searchRadius]);
        }

        // ===== COUNTS AND AVERAGES =====
        $query->withCount('reviews')
              ->withCount('gallery');
        
        // Only add withAvg if not already added by min_rating filter
        if (!$request->filled('min_rating') || $request->min_rating <= 0) {
            $query->withAvg('reviews', 'rating');
        }

        // ===== SORTING =====
        $sortBy = $request->get('sort', 'recommended');
        
        switch ($sortBy) {
            case 'rating':
                // Sort by average rating (highest first)
                $query->orderByDesc('reviews_avg_rating');
                break;
                
            case 'reviews':
                // Sort by number of reviews (most reviewed first)
                $query->orderByDesc('reviews_count');
                break;
                
            case 'distance':
                // Sort by distance (nearest first) - only if coordinates provided
                if ($userLat && $userLng) {
                    $query->orderBy('distance');
                } else {
                    $query->orderByDesc('is_featured');
                }
                break;
                
            case 'experience':
                // Sort by experience years (most experienced first)
                $query->orderByDesc('experience_years');
                break;
                
            case 'newest':
                // Sort by registration date (newest first)
                $query->orderByDesc('created_at');
                break;
                
            case 'active':
                // Sort by last activity
                $query->orderByDesc('last_active_at');
                break;
                
            case 'recommended':
            default:
                // Smart sorting: featured first, then by composite score
                $query->orderByDesc('is_featured')
                      ->orderByDesc('is_verified')
                      ->orderByDesc('reviews_avg_rating')
                      ->orderByDesc('reviews_count');
                break;
        }

        $craftsmen = $query->paginate(12)->withQueryString();

        // Calculate additional stats (cached 10 min)
        $totalCraftsmen = Cache::remember('stat_total_craftsmen', 600, function () {
            return User::where('role', 'specialist')->where('is_active', true)->count();
        });
        $totalReviews = Cache::remember('stat_total_reviews', 600, function () {
            return \App\Models\Review::where('is_approved', true)->count();
        });
        $avgRating = Cache::remember('stat_avg_rating', 600, function () {
            return \App\Models\Review::where('is_approved', true)->avg('rating');
        });

        $videos = Cache::remember('homepage_videos', 1800, function () {
            return Video::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        });

        return view('home', compact(
            'categories',
            'locations',
            'craftsmen',
            'totalCraftsmen',
            'totalReviews',
            'avgRating',
            'userLat',
            'userLng',
            'searchRadius',
            'videos'
        ));
    }

    public function show($slug)
    {
        $craftsman = User::where('slug', $slug)
            ->where('role', 'specialist')
            ->where('is_active', true)
            ->with(['category', 'location', 'services' => function ($query) {
                $query->where('is_active', true);
            }, 'reviews' => function ($query) {
                $query->where('is_approved', true)
                      ->with('appointment')
                      ->latest();
            }, 'gallery' => function ($query) {
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('sort_order')
                      ->orderBy('created_at', 'desc');
            }])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->firstOrFail();

        // Update last active timestamp
        $craftsman->update(['last_active_at' => now()]);

        return view('craftsman.show', compact('craftsman'));
    }

    /**
     * API endpoint for getting nearby craftsmen (AJAX).
     */
    public function nearby(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|integer|min:1|max:500',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $lat = (float) $request->lat;
        $lng = (float) $request->lng;
        $radius = $request->get('radius', 50);

        $haversine = "(6371 * acos(cos(radians({$lat})) * cos(radians(latitude)) * cos(radians(longitude) - radians({$lng})) + sin(radians({$lat})) * sin(radians(latitude))))";

        $query = User::where('role', 'specialist')
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->selectRaw("users.*, {$haversine} AS distance")
            ->whereRaw("{$haversine} <= ?", [$radius])
            // Also respect craftsman's own service radius
            ->whereRaw("(service_radius_km IS NULL OR {$haversine} <= service_radius_km)")
            ->with(['category', 'location'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $craftsmen = $query->orderBy('distance')
            ->limit(20)
            ->get()
            ->map(function ($craftsman) {
                return [
                    'id' => $craftsman->id,
                    'name' => $craftsman->name,
                    'slug' => $craftsman->slug,
                    'specialization' => $craftsman->specialization,
                    'category' => $craftsman->category?->name,
                    'location' => $craftsman->location?->name,
                    'latitude' => $craftsman->latitude,
                    'longitude' => $craftsman->longitude,
                    'distance' => round($craftsman->distance, 1),
                    'rating' => round($craftsman->reviews_avg_rating ?? 0, 1),
                    'reviews_count' => $craftsman->reviews_count,
                    'is_verified' => $craftsman->is_verified,
                    'is_featured' => $craftsman->is_featured,
                ];
            });

        return response()->json([
            'success' => true,
            'craftsmen' => $craftsmen,
            'count' => $craftsmen->count(),
        ]);
    }
}
