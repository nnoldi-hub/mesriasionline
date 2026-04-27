<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CategoriesApiController extends Controller
{
    /**
     * List all categories.
     * 
     * @queryParam parent_id number Filter by parent category
     * @queryParam with_count boolean Include craftsmen count
     */
    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'api_categories_' . md5(json_encode($request->all()));
        
        $categories = Cache::remember($cacheKey, 3600, function () use ($request) {
            $query = Category::where('is_active', true);

            if ($request->filled('parent_id')) {
                $query->where('parent_id', $request->parent_id);
            } else {
                $query->whereNull('parent_id');
            }

            if ($request->boolean('with_count', true)) {
                $query->withCount(['users as craftsmen_count' => function ($q) {
                    $q->where('role', 'craftsman')->where('status', 'active');
                }]);
            }

            return $query->orderBy('name')->get();
        });

        return response()->json([
            'success' => true,
            'data' => $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'icon' => $category->icon,
                    'image' => $category->image ? asset('storage/' . $category->image) : null,
                    'craftsmen_count' => $category->craftsmen_count ?? 0,
                    'parent_id' => $category->parent_id,
                    'has_children' => $category->children()->exists(),
                ];
            }),
        ]);
    }

    /**
     * Get a specific category with its subcategories.
     */
    public function show(string $identifier): JsonResponse
    {
        $category = Category::where('is_active', true)
            ->where(function ($query) use ($identifier) {
                $query->where('id', $identifier)
                    ->orWhere('slug', $identifier);
            })
            ->with(['children' => function ($q) {
                $q->where('is_active', true)->orderBy('name');
            }])
            ->withCount(['users as craftsmen_count' => function ($q) {
                $q->where('role', 'craftsman')->where('status', 'active');
            }])
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Categoria nu a fost găsită.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'icon' => $category->icon,
                'image' => $category->image ? asset('storage/' . $category->image) : null,
                'craftsmen_count' => $category->craftsmen_count,
                'parent' => $category->parent ? [
                    'id' => $category->parent->id,
                    'name' => $category->parent->name,
                    'slug' => $category->parent->slug,
                ] : null,
                'children' => $category->children->map(fn($child) => [
                    'id' => $child->id,
                    'name' => $child->name,
                    'slug' => $child->slug,
                ]),
            ],
        ]);
    }
}
