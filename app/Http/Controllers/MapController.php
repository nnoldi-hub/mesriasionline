<?php

namespace App\Http\Controllers;

use App\Services\MapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MapController extends Controller
{
    protected MapService $mapService;

    public function __construct(MapService $mapService)
    {
        $this->mapService = $mapService;
    }

    /**
     * Geocode an address
     */
    public function geocode(Request $request): JsonResponse
    {
        $request->validate([
            'address' => 'required|string|min:3',
        ]);

        $result = $this->mapService->geocodeAddress($request->address);

        if ($result) {
            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Nu am putut găsi această adresă.',
        ], 404);
    }

    /**
     * Reverse geocode coordinates
     */
    public function reverseGeocode(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $address = $this->mapService->reverseGeocode(
            $request->lat,
            $request->lng
        );

        if ($address) {
            return response()->json([
                'success' => true,
                'address' => $address,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Nu am putut determina adresa.',
        ], 404);
    }

    /**
     * Calculate distance between two points
     */
    public function calculateDistance(Request $request): JsonResponse
    {
        $request->validate([
            'lat1' => 'required|numeric',
            'lng1' => 'required|numeric',
            'lat2' => 'required|numeric',
            'lng2' => 'required|numeric',
            'unit' => 'sometimes|in:km,m,mi',
        ]);

        $distance = $this->mapService->calculateDistance(
            $request->lat1,
            $request->lng1,
            $request->lat2,
            $request->lng2,
            $request->input('unit', 'km')
        );

        return response()->json([
            'success' => true,
            'distance' => $distance,
            'unit' => $request->input('unit', 'km'),
        ]);
    }

    /**
     * Find craftsmen within radius
     */
    public function searchRadius(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'sometimes|numeric|min:1|max:200',
            'category_id' => 'sometimes|exists:categories,id',
        ]);

        $craftsmen = $this->mapService->findCraftsmenInRadius(
            $request->lat,
            $request->lng,
            $request->input('radius', 50),
            $request->category_id
        );

        $markers = $this->mapService->generateMarkersData($craftsmen);

        return response()->json([
            'success' => true,
            'count' => count($markers),
            'markers' => $markers,
            'center' => $this->mapService->getCenterPoint($markers),
        ]);
    }

    /**
     * Get craftsmen for map display
     */
    public function getCraftsmenMarkers(Request $request): JsonResponse
    {
        $query = \App\Models\User::where('role', 'craftsman')
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['services', 'location'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        // Filter by category if provided
        if ($request->category_id) {
            $query->whereHas('services', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        // Filter by location if provided
        if ($request->location_id) {
            $query->where('location_id', $request->location_id);
        }

        // Limit results
        $craftsmen = $query->limit(100)->get()->toArray();

        $markers = $this->mapService->generateMarkersData($craftsmen);

        return response()->json([
            'success' => true,
            'markers' => $markers,
            'center' => $this->mapService->getCenterPoint($markers),
        ]);
    }
}
