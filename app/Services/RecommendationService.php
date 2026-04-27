<?php

namespace App\Services;

use App\Models\User;
use App\Models\Favorite;
use App\Models\SearchHistory;
use App\Models\ProfileView;
use App\Models\Appointment;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class RecommendationService
{
    /**
     * Get personalized recommendations for a user
     */
    public function getRecommendations(?int $userId = null, int $limit = 10): Collection
    {
        if (!$userId) {
            return $this->getPopularCraftsmen($limit);
        }

        $user = User::find($userId);
        if (!$user) {
            return $this->getPopularCraftsmen($limit);
        }

        // Collect different recommendation signals
        $recommendations = collect();

        // 1. Based on favorites (similar craftsmen)
        $favoriteBased = $this->getRecommendationsFromFavorites($userId, 3);
        $recommendations = $recommendations->merge($favoriteBased);

        // 2. Based on search history
        $searchBased = $this->getRecommendationsFromSearchHistory($userId, 3);
        $recommendations = $recommendations->merge($searchBased);

        // 3. Based on profile views
        $viewBased = $this->getRecommendationsFromViews($userId, 2);
        $recommendations = $recommendations->merge($viewBased);

        // 4. Based on location
        if ($user->location_id) {
            $locationBased = $this->getRecommendationsFromLocation($user->location_id, 2);
            $recommendations = $recommendations->merge($locationBased);
        }

        // Remove duplicates and craftsmen already favorited
        $favoritedIds = Favorite::where('user_id', $userId)
            ->pluck('craftsman_id')
            ->toArray();

        $recommendations = $recommendations
            ->unique('id')
            ->reject(function ($craftsman) use ($favoritedIds, $userId) {
                return in_array($craftsman->id, $favoritedIds) || $craftsman->id === $userId;
            });

        // If not enough recommendations, fill with popular craftsmen
        if ($recommendations->count() < $limit) {
            $popular = $this->getPopularCraftsmen($limit - $recommendations->count(), $favoritedIds);
            $recommendations = $recommendations->merge($popular);
        }

        return $recommendations
            ->take($limit)
            ->values();
    }

    /**
     * Get recommendations based on user's favorites
     */
    protected function getRecommendationsFromFavorites(int $userId, int $limit): Collection
    {
        // Get categories and locations from user's favorites
        $favoriteData = Favorite::where('favorites.user_id', $userId)
            ->join('users', 'users.id', '=', 'favorites.craftsman_id')
            ->select('users.category_id', 'users.location_id')
            ->get();

        if ($favoriteData->isEmpty()) {
            return collect();
        }

        $categoryIds = $favoriteData->pluck('category_id')->filter()->unique();
        $locationIds = $favoriteData->pluck('location_id')->filter()->unique();

        // Find craftsmen with similar categories/locations
        return User::where('user_type', 'craftsman')
            ->where('is_approved', true)
            ->where(function ($query) use ($categoryIds, $locationIds) {
                $query->whereIn('category_id', $categoryIds)
                    ->orWhereIn('location_id', $locationIds);
            })
            ->whereNotIn('id', function ($query) use ($userId) {
                $query->select('craftsman_id')
                    ->from('favorites')
                    ->where('user_id', $userId);
            })
            ->with(['category', 'location', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recommendations based on search history
     */
    protected function getRecommendationsFromSearchHistory(int $userId, int $limit): Collection
    {
        $searches = SearchHistory::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        if ($searches->isEmpty()) {
            return collect();
        }

        $categoryIds = $searches->pluck('category_id')->filter()->unique();
        $locationIds = $searches->pluck('location_id')->filter()->unique();
        $searchTerms = $searches->pluck('search_term')->filter()->unique();

        $query = User::where('user_type', 'craftsman')
            ->where('is_approved', true);

        if ($categoryIds->isNotEmpty()) {
            $query->whereIn('category_id', $categoryIds);
        }

        if ($locationIds->isNotEmpty()) {
            $query->whereIn('location_id', $locationIds);
        }

        if ($searchTerms->isNotEmpty()) {
            $query->where(function ($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $q->orWhere('name', 'LIKE', "%{$term}%")
                      ->orWhere('business_name', 'LIKE', "%{$term}%")
                      ->orWhere('description', 'LIKE', "%{$term}%");
                }
            });
        }

        return $query->whereNotIn('id', function ($query) use ($userId) {
                $query->select('craftsman_id')
                    ->from('favorites')
                    ->where('user_id', $userId);
            })
            ->with(['category', 'location', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recommendations based on profile views
     */
    protected function getRecommendationsFromViews(int $userId, int $limit): Collection
    {
        // Get recently viewed craftsmen's categories and locations
        $viewedCraftsmen = ProfileView::where('viewer_id', $userId)
            ->join('users', 'users.id', '=', 'profile_views.craftsman_id')
            ->select('users.category_id', 'users.location_id')
            ->orderBy('profile_views.created_at', 'desc')
            ->limit(10)
            ->get();

        if ($viewedCraftsmen->isEmpty()) {
            return collect();
        }

        $categoryIds = $viewedCraftsmen->pluck('category_id')->filter()->unique();
        $locationIds = $viewedCraftsmen->pluck('location_id')->filter()->unique();

        return User::where('user_type', 'craftsman')
            ->where('is_approved', true)
            ->where(function ($query) use ($categoryIds, $locationIds) {
                $query->whereIn('category_id', $categoryIds)
                    ->orWhereIn('location_id', $locationIds);
            })
            ->whereNotIn('id', function ($query) use ($userId) {
                $query->select('craftsman_id')
                    ->from('favorites')
                    ->where('user_id', $userId);
            })
            ->whereNotIn('id', function ($query) use ($userId) {
                $query->select('craftsman_id')
                    ->from('profile_views')
                    ->where('viewer_id', $userId);
            })
            ->with(['category', 'location', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recommendations from same location
     */
    protected function getRecommendationsFromLocation(int $locationId, int $limit): Collection
    {
        return User::where('user_type', 'craftsman')
            ->where('is_approved', true)
            ->where('location_id', $locationId)
            ->with(['category', 'location', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->limit($limit)
            ->get();
    }

    /**
     * Get popular craftsmen (fallback)
     */
    protected function getPopularCraftsmen(int $limit, array $excludeIds = []): Collection
    {
        $query = User::where('user_type', 'craftsman')
            ->where('is_approved', true);

        if (!empty($excludeIds)) {
            $query->whereNotIn('id', $excludeIds);
        }

        return $query->with(['category', 'location', 'reviews'])
            ->withCount(['reviews', 'appointments'])
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->orderByDesc('reviews_count')
            ->orderByDesc('is_featured')
            ->limit($limit)
            ->get();
    }

    /**
     * Get similar craftsmen (based on a specific craftsman)
     */
    public function getSimilarCraftsmen(int $craftsmanId, int $limit = 6): Collection
    {
        $craftsman = User::find($craftsmanId);
        if (!$craftsman) {
            return collect();
        }

        return User::where('user_type', 'craftsman')
            ->where('is_approved', true)
            ->where('id', '!=', $craftsmanId)
            ->where(function ($query) use ($craftsman) {
                $query->where('category_id', $craftsman->category_id)
                    ->orWhere('location_id', $craftsman->location_id);
            })
            ->with(['category', 'location', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->limit($limit)
            ->get();
    }

    /**
     * Get trending craftsmen (most viewed recently)
     */
    public function getTrendingCraftsmen(int $days = 7, int $limit = 10): Collection
    {
        $date = now()->subDays($days);

        return User::where('user_type', 'craftsman')
            ->where('is_approved', true)
            ->whereHas('profileViews', function ($query) use ($date) {
                $query->where('created_at', '>=', $date);
            })
            ->with(['category', 'location', 'reviews'])
            ->withCount(['profileViews' => function ($query) use ($date) {
                $query->where('created_at', '>=', $date);
            }])
            ->withAvg('reviews', 'rating')
            ->orderByDesc('profile_views_count')
            ->orderByDesc('reviews_avg_rating')
            ->limit($limit)
            ->get();
    }

    /**
     * Get "customers also liked" recommendations
     */
    public function getCustomersAlsoLiked(int $craftsmanId, int $limit = 6): Collection
    {
        // Get users who favorited this craftsman
        $userIds = Favorite::where('craftsman_id', $craftsmanId)
            ->pluck('user_id');

        if ($userIds->isEmpty()) {
            return $this->getSimilarCraftsmen($craftsmanId, $limit);
        }

        // Get other craftsmen favorited by those users
        $otherFavorites = Favorite::whereIn('user_id', $userIds)
            ->where('craftsman_id', '!=', $craftsmanId)
            ->select('craftsman_id', DB::raw('COUNT(*) as count'))
            ->groupBy('craftsman_id')
            ->orderByDesc('count')
            ->limit($limit)
            ->pluck('craftsman_id');

        return User::whereIn('id', $otherFavorites)
            ->with(['category', 'location', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->get();
    }
}
