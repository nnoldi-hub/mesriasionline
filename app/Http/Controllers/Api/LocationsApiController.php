<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class LocationsApiController extends Controller
{
    /**
     * List all locations (counties and cities).
     * 
     * @queryParam type string Filter by type: county, city
     * @queryParam county string Filter cities by county
     * @queryParam search string Search by name
     */
    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'api_locations_' . md5(json_encode($request->all()));

        $data = Cache::remember($cacheKey, 3600, function () use ($request) {
            if ($request->input('type') === 'county') {
                return $this->getCounties();
            }

            if ($request->filled('county')) {
                return $this->getCitiesByCounty($request->county);
            }

            if ($request->filled('search')) {
                return $this->searchLocations($request->search);
            }

            return $this->getCounties();
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get all counties.
     */
    protected function getCounties(): array
    {
        $counties = Location::select('county')
            ->distinct()
            ->orderBy('county')
            ->pluck('county');

        return $counties->map(function ($county) {
            return [
                'type' => 'county',
                'name' => $county,
                'slug' => \Str::slug($county),
            ];
        })->toArray();
    }

    /**
     * Get cities in a county.
     */
    protected function getCitiesByCounty(string $county): array
    {
        $cities = Location::where('county', $county)
            ->orderBy('city')
            ->get();

        return $cities->map(function ($location) {
            return [
                'type' => 'city',
                'id' => $location->id,
                'name' => $location->city,
                'county' => $location->county,
                'slug' => \Str::slug($location->city),
            ];
        })->toArray();
    }

    /**
     * Search locations by name.
     */
    protected function searchLocations(string $search): array
    {
        $locations = Location::where('city', 'LIKE', "%{$search}%")
            ->orWhere('county', 'LIKE', "%{$search}%")
            ->limit(20)
            ->get();

        return $locations->map(function ($location) {
            return [
                'type' => 'city',
                'id' => $location->id,
                'name' => $location->city,
                'county' => $location->county,
                'full_name' => $location->city . ', ' . $location->county,
            ];
        })->toArray();
    }

    /**
     * Get a specific location.
     */
    public function show(int $id): JsonResponse
    {
        $location = Location::find($id);

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Locația nu a fost găsită.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $location->id,
                'city' => $location->city,
                'county' => $location->county,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
            ],
        ]);
    }
}
