<?php

namespace App\Http\Controllers;

use App\Models\SearchHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchHistoryController extends Controller
{
    /**
     * Display user's search history.
     */
    public function index()
    {
        $searches = SearchHistory::getRecentForUser(Auth::id(), 50);
        $popularSearches = SearchHistory::getPopular(10);
        
        return view('search.history', compact('searches', 'popularSearches'));
    }

    /**
     * Record a search query (called via AJAX).
     */
    public function store(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:255',
            'filters' => 'nullable|array',
            'results_count' => 'nullable|integer|min:0',
        ]);

        SearchHistory::record([
            'user_id' => Auth::id(),
            'query' => $request->input('query'),
            'filters' => $request->filters ?? [],
            'results_count' => $request->results_count ?? 0,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Clear user's search history.
     */
    public function destroy()
    {
        SearchHistory::clearForUser(Auth::id());

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Istoricul căutărilor a fost șters.');
    }

    /**
     * Delete a specific search from history.
     */
    public function destroyOne(SearchHistory $search)
    {
        // Verify ownership
        if ($search->user_id !== Auth::id()) {
            abort(403);
        }

        $search->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Căutarea a fost ștearsă din istoric.');
    }

    /**
     * Get search suggestions based on history and popular searches.
     */
    public function suggestions(Request $request)
    {
        $query = $request->input('q', '');
        
        $suggestions = [];

        // Get from user's history
        if (Auth::check()) {
            $userHistory = SearchHistory::where('user_id', Auth::id())
                ->where('query', 'LIKE', "%{$query}%")
                ->orderByDesc('searched_at')
                ->limit(5)
                ->pluck('query')
                ->toArray();
            
            $suggestions = array_merge($suggestions, array_map(function($q) {
                return ['query' => $q, 'type' => 'history'];
            }, $userHistory));
        }

        // Get popular searches
        $popular = SearchHistory::where('query', 'LIKE', "%{$query}%")
            ->select('query')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('query')
            ->orderByDesc('count')
            ->limit(5)
            ->pluck('query')
            ->toArray();

        $suggestions = array_merge($suggestions, array_map(function($q) {
            return ['query' => $q, 'type' => 'popular'];
        }, $popular));

        // Remove duplicates
        $unique = [];
        $seen = [];
        foreach ($suggestions as $suggestion) {
            if (!in_array($suggestion['query'], $seen)) {
                $seen[] = $suggestion['query'];
                $unique[] = $suggestion;
            }
        }

        return response()->json(array_slice($unique, 0, 10));
    }
}
