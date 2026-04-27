<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FavoriteController extends Controller
{
    /**
     * List user's favorites
     */
    public function index()
    {
        $favorites = Favorite::with(['craftsman' => function ($query) {
            $query->with(['category', 'location', 'reviews']);
        }])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('favorites.index', compact('favorites'));
    }

    /**
     * Toggle favorite status
     */
    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'craftsman_id' => 'required|exists:users,id',
        ]);

        $result = Favorite::toggle(auth()->id(), $request->craftsman_id);

        return response()->json([
            'success' => true,
            'favorited' => $result['favorited'],
            'message' => $result['favorited'] 
                ? 'Meșterul a fost adăugat la favorite' 
                : 'Meșterul a fost eliminat din favorite',
        ]);
    }

    /**
     * Remove from favorites
     */
    public function destroy(int $craftsmanId): JsonResponse
    {
        $deleted = Favorite::where('user_id', auth()->id())
            ->where('craftsman_id', $craftsmanId)
            ->delete();

        return response()->json([
            'success' => $deleted > 0,
            'message' => $deleted > 0 
                ? 'Meșterul a fost eliminat din favorite' 
                : 'Meșterul nu a fost găsit în favorite',
        ]);
    }

    /**
     * Update notes for a favorite
     */
    public function updateNotes(Request $request, int $craftsmanId): JsonResponse
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $favorite = Favorite::where('user_id', auth()->id())
            ->where('craftsman_id', $craftsmanId)
            ->first();

        if (!$favorite) {
            return response()->json([
                'success' => false,
                'message' => 'Favoritul nu a fost găsit',
            ], 404);
        }

        $favorite->update(['notes' => $request->notes]);

        return response()->json([
            'success' => true,
            'message' => 'Notele au fost actualizate',
        ]);
    }

    /**
     * Check if craftsman is favorited
     */
    public function check(int $craftsmanId): JsonResponse
    {
        $isFavorited = Favorite::isFavorited(auth()->id(), $craftsmanId);

        return response()->json([
            'favorited' => $isFavorited,
        ]);
    }
}
