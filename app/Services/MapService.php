<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MapService
{
    protected string $apiKey;
    protected string $geocodingUrl = 'https://maps.googleapis.com/maps/api/geocode/json';
    protected string $distanceUrl = 'https://maps.googleapis.com/maps/api/distancematrix/json';

    public function __construct()
    {
        $this->apiKey = config('services.google.maps_api_key');
    }

    /**
     * Geocode an address to get latitude and longitude
     */
    public function geocodeAddress(string $address): ?array
    {
        try {
            $cacheKey = 'geocode_' . md5($address);

            return Cache::remember($cacheKey, now()->addDays(30), function () use ($address) {
                $response = Http::get($this->geocodingUrl, [
                    'address' => $address,
                    'key' => $this->apiKey,
                    'region' => 'ro',
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if ($data['status'] === 'OK' && isset($data['results'][0])) {
                        $location = $data['results'][0]['geometry']['location'];
                        return [
                            'lat' => $location['lat'],
                            'lng' => $location['lng'],
                            'formatted_address' => $data['results'][0]['formatted_address'],
                        ];
                    }
                }

                return null;
            });
        } catch (\Exception $e) {
            Log::error('Geocoding error', [
                'address' => $address,
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Reverse geocode coordinates to get address
     */
    public function reverseGeocode(float $lat, float $lng): ?string
    {
        try {
            $cacheKey = 'reverse_geocode_' . md5("{$lat},{$lng}");

            return Cache::remember($cacheKey, now()->addDays(30), function () use ($lat, $lng) {
                $response = Http::get($this->geocodingUrl, [
                    'latlng' => "{$lat},{$lng}",
                    'key' => $this->apiKey,
                    'region' => 'ro',
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if ($data['status'] === 'OK' && isset($data['results'][0])) {
                        return $data['results'][0]['formatted_address'];
                    }
                }

                return null;
            });
        } catch (\Exception $e) {
            Log::error('Reverse geocoding error', [
                'lat' => $lat,
                'lng' => $lng,
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Calculate distance between two points
     */
    public function calculateDistance(
        float $lat1,
        float $lng1,
        float $lat2,
        float $lng2,
        string $unit = 'km'
    ): ?float {
        try {
            $response = Http::get($this->distanceUrl, [
                'origins' => "{$lat1},{$lng1}",
                'destinations' => "{$lat2},{$lng2}",
                'key' => $this->apiKey,
                'units' => 'metric',
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['status'] === 'OK' && isset($data['rows'][0]['elements'][0])) {
                    $element = $data['rows'][0]['elements'][0];

                    if ($element['status'] === 'OK') {
                        $meters = $element['distance']['value'];
                        
                        return match ($unit) {
                            'km' => round($meters / 1000, 2),
                            'm' => $meters,
                            'mi' => round($meters / 1609.344, 2),
                            default => round($meters / 1000, 2),
                        };
                    }
                }
            }

            // Fallback to Haversine formula if API fails
            return $this->haversineDistance($lat1, $lng1, $lat2, $lng2, $unit);
        } catch (\Exception $e) {
            Log::error('Distance calculation error', [
                'message' => $e->getMessage(),
            ]);
            return $this->haversineDistance($lat1, $lng1, $lat2, $lng2, $unit);
        }
    }

    /**
     * Calculate distance using Haversine formula (fallback)
     */
    protected function haversineDistance(
        float $lat1,
        float $lng1,
        float $lat2,
        float $lng2,
        string $unit = 'km'
    ): float {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return match ($unit) {
            'km' => round($distance, 2),
            'm' => round($distance * 1000, 2),
            'mi' => round($distance * 0.621371, 2),
            default => round($distance, 2),
        };
    }

    /**
     * Find craftsmen within radius of a location
     */
    public function findCraftsmenInRadius(
        float $lat,
        float $lng,
        float $radiusKm = 50,
        ?int $categoryId = null
    ): array {
        // Calculate bounding box for initial filtering
        $latDelta = $radiusKm / 111; // 1 degree latitude ≈ 111 km
        $lngDelta = $radiusKm / (111 * cos(deg2rad($lat)));

        $query = \App\Models\User::where('role', 'craftsman')
            ->where('is_active', true)
            ->whereBetween('latitude', [$lat - $latDelta, $lat + $latDelta])
            ->whereBetween('longitude', [$lng - $lngDelta, $lng + $lngDelta])
            ->with(['services', 'location']);

        if ($categoryId) {
            $query->whereHas('services', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        $craftsmen = $query->get();

        // Filter by actual distance and add distance field
        return $craftsmen->map(function ($craftsman) use ($lat, $lng, $radiusKm) {
            if ($craftsman->latitude && $craftsman->longitude) {
                $distance = $this->haversineDistance(
                    $lat,
                    $lng,
                    $craftsman->latitude,
                    $craftsman->longitude
                );

                if ($distance <= $radiusKm) {
                    $craftsman->distance = $distance;
                    return $craftsman;
                }
            }
            return null;
        })->filter()->sortBy('distance')->values()->toArray();
    }

    /**
     * Get map center for a collection of points
     */
    public function getCenterPoint(array $coordinates): array
    {
        if (empty($coordinates)) {
            // Default to Romania center
            return ['lat' => 45.9432, 'lng' => 24.9668];
        }

        $latSum = 0;
        $lngSum = 0;
        $count = count($coordinates);

        foreach ($coordinates as $coord) {
            $latSum += $coord['lat'];
            $lngSum += $coord['lng'];
        }

        return [
            'lat' => $latSum / $count,
            'lng' => $lngSum / $count,
        ];
    }

    /**
     * Generate map markers data for craftsmen
     */
    public function generateMarkersData(array $craftsmen): array
    {
        return array_map(function ($craftsman) {
            return [
                'id' => $craftsman['id'],
                'lat' => $craftsman['latitude'],
                'lng' => $craftsman['longitude'],
                'name' => $craftsman['name'],
                'avatar' => $craftsman['profile_photo_url'] ?? asset('images/default-avatar.png'),
                'rating' => $craftsman['rating'] ?? 0,
                'reviews_count' => $craftsman['reviews_count'] ?? 0,
                'url' => route('craftsman.show', $craftsman['id']),
                'distance' => $craftsman['distance'] ?? null,
            ];
        }, $craftsmen);
    }

    /**
     * Validate API key
     */
    public function validateApiKey(): bool
    {
        try {
            $response = Http::get($this->geocodingUrl, [
                'address' => 'Bucuresti, Romania',
                'key' => $this->apiKey,
            ]);

            $data = $response->json();
            return $data['status'] === 'OK';
        } catch (\Exception $e) {
            return false;
        }
    }
}
