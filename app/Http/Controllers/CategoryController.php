<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Afișează lista tuturor categoriilor
     */
    public function index()
    {
        $categories = Category::active()
            ->ordered()
            ->withCount(['craftsmen' => function ($query) {
                $query->where('is_active', true);
            }])
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Afișează o categorie specifică cu meseriașii ei
     */
    public function show($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $craftsmen = User::where('role', 'specialist')
            ->where('category_id', $category->id)
            ->where('is_active', true)
            ->with(['location', 'services', 'reviews'])
            ->withCount(['reviews as reviews_count'])
            ->withAvg(['reviews as average_rating'], 'rating')
            ->paginate(12);

        // SEO meta tags
        $metaTitle = $category->meta_title_full;
        $metaDescription = $category->meta_description_full;

        return view('categories.show', compact('category', 'craftsmen', 'metaTitle', 'metaDescription'));
    }

    /**
     * Filtrare meseriași după categorie și locație
     */
    public function filter(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $query = User::where('role', 'specialist')
            ->where('category_id', $category->id)
            ->where('is_active', true);

        // Filtrare după locație
        if ($request->has('location_id') && $request->location_id) {
            $query->where('location_id', $request->location_id);
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

        // Filtrare după disponibilitate weekend
        if ($request->has('available_weekends') && $request->available_weekends) {
            $query->where('available_weekends', true);
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
            case 'name':
                $query->orderBy('name');
                break;
        }

        $craftsmen = $query->with(['location', 'services', 'reviews'])
            ->withCount(['reviews as reviews_count'])
            ->paginate(12);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('categories.partials.craftsmen-list', compact('craftsmen'))->render(),
                'pagination' => $craftsmen->links()->render()
            ]);
        }

        return view('categories.show', compact('category', 'craftsmen'));
    }
}
