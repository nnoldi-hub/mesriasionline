<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use App\Models\Service;
use App\Models\Review;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CraftsmenApiController extends Controller
{
    /**
     * List all craftsmen with optional filters.
     * 
     * @queryParam category string Filter by category slug
     * @queryParam location string Filter by location (city or county)
     * @queryParam rating number Minimum rating (1-5)
     * @queryParam verified boolean Filter by verification status
     * @queryParam available boolean Filter by availability
     * @queryParam sort string Sort by: rating, reviews, newest (default: rating)
     * @queryParam per_page number Items per page (default: 15, max: 50)
     * @queryParam page number Page number
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::where('role', 'craftsman')
            ->where('status', 'active')
            ->with(['services', 'categories', 'location']);

        // Apply filters
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('location')) {
            $query->whereHas('location', function ($q) use ($request) {
                $q->where('city', 'LIKE', "%{$request->location}%")
                    ->orWhere('county', 'LIKE', "%{$request->location}%");
            });
        }

        if ($request->filled('rating')) {
            $query->where('average_rating', '>=', floatval($request->rating));
        }

        if ($request->filled('verified')) {
            $query->where('is_verified', filter_var($request->verified, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('available')) {
            $query->where('is_available', filter_var($request->available, FILTER_VALIDATE_BOOLEAN));
        }

        // Sorting
        $sort = $request->input('sort', 'rating');
        switch ($sort) {
            case 'reviews':
                $query->orderByDesc('reviews_count');
                break;
            case 'newest':
                $query->orderByDesc('created_at');
                break;
            case 'rating':
            default:
                $query->orderByDesc('average_rating');
                break;
        }

        $perPage = min($request->input('per_page', 15), 50);
        $craftsmen = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $craftsmen->map(function ($craftsman) {
                return $this->formatCraftsman($craftsman);
            }),
            'meta' => [
                'current_page' => $craftsmen->currentPage(),
                'last_page' => $craftsmen->lastPage(),
                'per_page' => $craftsmen->perPage(),
                'total' => $craftsmen->total(),
            ],
            'links' => [
                'first' => $craftsmen->url(1),
                'last' => $craftsmen->url($craftsmen->lastPage()),
                'prev' => $craftsmen->previousPageUrl(),
                'next' => $craftsmen->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Filter craftsmen with advanced AJAX filtering (mirrors HomeController logic).
     * Used for real-time filtering on the frontend.
     */
    public function filter(Request $request): JsonResponse
    {
        $query = User::where('role', 'specialist')
            ->where('is_active', true)
            ->with(['category', 'location', 'gallery']);

        // Get user coordinates if provided
        $userLat = $request->filled('lat') ? (float) $request->lat : null;
        $userLng = $request->filled('lng') ? (float) $request->lng : null;
        $searchRadius = $request->filled('radius') ? (int) $request->radius : 50;

        // ===== BASIC FILTERS =====
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

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
        if ($request->filled('featured') && $request->featured) {
            $query->where('is_featured', true);
        }

        if ($request->filled('verified') && $request->verified) {
            $query->where('is_verified', true);
        }

        if ($request->filled('has_gallery') && $request->has_gallery) {
            $query->whereHas('gallery');
        }

        if ($request->filled('min_rating') && $request->min_rating > 0) {
            $minRating = (float) $request->min_rating;
            $query->whereHas('reviews', function ($q) {
                $q->select('specialist_id')
                  ->groupBy('specialist_id');
            })->withAvg('reviews', 'rating')
              ->having('reviews_avg_rating', '>=', $minRating);
        }

        if ($request->filled('has_reviews') && $request->has_reviews) {
            $query->has('reviews');
        }

        if ($request->filled('min_experience') && $request->min_experience > 0) {
            $query->where('experience_years', '>=', (int) $request->min_experience);
        }

        if ($request->filled('available_weekends') && $request->available_weekends) {
            $query->where('available_weekends', true);
        }

        if ($request->filled('emergency_services') && $request->emergency_services) {
            $query->where('emergency_services', true);
        }

        if ($request->filled('has_insurance') && $request->has_insurance) {
            $query->where('has_insurance', true);
        }

        // ===== PROXIMITY FILTER (Geolocation) =====
        if ($userLat && $userLng) {
            $haversine = "(6371 * acos(cos(radians({$userLat})) * cos(radians(latitude)) * cos(radians(longitude) - radians({$userLng})) + sin(radians({$userLat})) * sin(radians(latitude))))";
            
            $query->selectRaw("users.*, {$haversine} AS distance")
                  ->whereNotNull('latitude')
                  ->whereNotNull('longitude')
                  // Within user's chosen search radius
                  ->whereRaw("{$haversine} <= ?", [$searchRadius])
                  // AND craftsman covers the user's location (respects craftsman's own service radius)
                  ->whereRaw("(service_radius_km IS NULL OR {$haversine} <= service_radius_km)");
        }

        // ===== COUNTS AND AVERAGES =====
        $query->withCount('reviews')
              ->withCount('gallery');
        
        if (!$request->filled('min_rating') || $request->min_rating <= 0) {
            $query->withAvg('reviews', 'rating');
        }

        // ===== SORTING =====
        $sortBy = $request->get('sort', 'recommended');
        
        switch ($sortBy) {
            case 'rating':
                $query->orderByDesc('reviews_avg_rating');
                break;
            case 'reviews':
                $query->orderByDesc('reviews_count');
                break;
            case 'distance':
                if ($userLat && $userLng) {
                    $query->orderBy('distance');
                } else {
                    $query->orderByDesc('is_featured');
                }
                break;
            case 'experience':
                $query->orderByDesc('experience_years');
                break;
            case 'newest':
                $query->orderByDesc('created_at');
                break;
            case 'recommended':
            default:
                $query->orderByDesc('is_featured')
                      ->orderByDesc('is_verified')
                      ->orderByDesc('reviews_avg_rating')
                      ->orderByDesc('reviews_count');
                break;
        }

        $perPage = min($request->input('per_page', 12), 50);
        $craftsmen = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'craftsmen' => $craftsmen->map(function ($craftsman) use ($userLat, $userLng) {
                return [
                    'id' => $craftsman->id,
                    'slug' => $craftsman->slug,
                    'name' => $craftsman->name,
                    'specialization' => $craftsman->specialization,
                    'category' => $craftsman->category?->name,
                    'location' => $craftsman->location?->name,
                    'profile_photo' => $craftsman->profile_photo ? asset('storage/' . $craftsman->profile_photo) : null,
                    'rating' => round($craftsman->reviews_avg_rating ?? 0, 1),
                    'reviews_count' => $craftsman->reviews_count ?? 0,
                    'is_verified' => (bool) $craftsman->is_verified,
                    'is_featured' => (bool) $craftsman->is_featured,
                    'experience_years' => $craftsman->experience_years,
                    'latitude' => $craftsman->latitude,
                    'longitude' => $craftsman->longitude,
                    'distance' => isset($craftsman->distance) ? round($craftsman->distance, 1) : null,
                    'gallery' => $craftsman->gallery->take(3)->map(fn($g) => [
                        'url' => asset('storage/' . $g->image_path),
                    ])->toArray(),
                ];
            }),
            'total' => $craftsmen->total(),
            'meta' => [
                'current_page' => $craftsmen->currentPage(),
                'last_page' => $craftsmen->lastPage(),
                'per_page' => $craftsmen->perPage(),
                'total' => $craftsmen->total(),
            ],
        ]);
    }

    /**
     * Get a specific craftsman by ID or slug.
     */
    public function show(string $identifier): JsonResponse
    {
        $craftsman = User::where('role', 'craftsman')
            ->where('status', 'active')
            ->where(function ($query) use ($identifier) {
                $query->where('id', $identifier)
                    ->orWhere('slug', $identifier);
            })
            ->with(['services', 'categories', 'location', 'certifications', 'galleries'])
            ->first();

        if (!$craftsman) {
            return response()->json([
                'success' => false,
                'message' => 'Meșterul nu a fost găsit.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatCraftsman($craftsman, true),
        ]);
    }

    /**
     * Get craftsman's reviews.
     */
    public function reviews(string $identifier, Request $request): JsonResponse
    {
        $craftsman = User::where('role', 'craftsman')
            ->where(function ($query) use ($identifier) {
                $query->where('id', $identifier)
                    ->orWhere('slug', $identifier);
            })
            ->first();

        if (!$craftsman) {
            return response()->json([
                'success' => false,
                'message' => 'Meșterul nu a fost găsit.',
            ], 404);
        }

        $perPage = min($request->input('per_page', 10), 50);
        $reviews = Review::where('craftsman_id', $craftsman->id)
            ->where('status', 'approved')
            ->with('client:id,name,avatar')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'client' => [
                        'name' => $review->client->name ?? 'Anonim',
                        'avatar' => $review->client->avatar ?? null,
                    ],
                    'created_at' => $review->created_at->toISOString(),
                    'response' => $review->response,
                    'response_at' => $review->response_at?->toISOString(),
                ];
            }),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
                'average_rating' => $craftsman->average_rating,
            ],
        ]);
    }

    /**
     * Get craftsman's services.
     */
    public function services(string $identifier): JsonResponse
    {
        $craftsman = User::where('role', 'craftsman')
            ->where(function ($query) use ($identifier) {
                $query->where('id', $identifier)
                    ->orWhere('slug', $identifier);
            })
            ->first();

        if (!$craftsman) {
            return response()->json([
                'success' => false,
                'message' => 'Meșterul nu a fost găsit.',
            ], 404);
        }

        $services = Service::where('user_id', $craftsman->id)
            ->where('is_active', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $services->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'description' => $service->description,
                    'price_from' => $service->price_from,
                    'price_to' => $service->price_to,
                    'price_type' => $service->price_type,
                    'duration' => $service->duration,
                    'category' => $service->category?->name,
                ];
            }),
        ]);
    }

    /**
     * Format craftsman data for API response.
     */
    protected function formatCraftsman(User $craftsman, bool $detailed = false): array
    {
        $data = [
            'id' => $craftsman->id,
            'slug' => $craftsman->slug,
            'name' => $craftsman->company_name ?? $craftsman->name,
            'avatar' => $craftsman->avatar ? asset('storage/' . $craftsman->avatar) : null,
            'rating' => [
                'average' => round($craftsman->average_rating, 1),
                'count' => $craftsman->reviews_count,
            ],
            'is_verified' => $craftsman->is_verified,
            'is_available' => $craftsman->is_available,
            'location' => $craftsman->location ? [
                'city' => $craftsman->location->city,
                'county' => $craftsman->location->county,
            ] : null,
            'categories' => $craftsman->categories->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
            ]),
            'profile_url' => route('craftsman.show', $craftsman->slug),
        ];

        if ($detailed) {
            $data = array_merge($data, [
                'bio' => $craftsman->bio,
                'experience_years' => $craftsman->experience_years,
                'phone' => $craftsman->show_phone ? $craftsman->phone : null,
                'email' => $craftsman->show_email ? $craftsman->email : null,
                'website' => $craftsman->website,
                'social' => [
                    'facebook' => $craftsman->facebook,
                    'instagram' => $craftsman->instagram,
                ],
                'services_count' => $craftsman->services->count(),
                'certifications' => $craftsman->certifications?->map(fn($c) => [
                    'name' => $c->name,
                    'issuer' => $c->issuer,
                    'year' => $c->year,
                ]) ?? [],
                'gallery' => $craftsman->galleries?->map(fn($g) => [
                    'id' => $g->id,
                    'url' => asset('storage/' . $g->image),
                    'caption' => $g->caption,
                ]) ?? [],
                'member_since' => $craftsman->created_at->format('Y-m'),
            ]);
        }

        return $data;
    }
}
