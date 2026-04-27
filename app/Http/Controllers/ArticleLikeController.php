<?php

namespace App\Http\Controllers;

use App\Models\ArticleLike;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ArticleLikeController extends Controller
{
    /**
     * Toggle like/dislike for an article
     */
    public function toggle(Request $request, int $articleId): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:like,dislike',
        ]);

        $article = Article::findOrFail($articleId);
        
        $result = ArticleLike::toggle(auth()->id(), $articleId, $request->type);
        $counts = ArticleLike::getCounts($articleId);

        return response()->json([
            'success' => true,
            'action' => $result['action'],
            'current_type' => $result['type'],
            'likes' => $counts['likes'],
            'dislikes' => $counts['dislikes'],
        ]);
    }

    /**
     * Get like status for an article
     */
    public function status(int $articleId): JsonResponse
    {
        $counts = ArticleLike::getCounts($articleId);
        $userReaction = auth()->check() 
            ? ArticleLike::getUserReaction(auth()->id(), $articleId) 
            : null;

        return response()->json([
            'likes' => $counts['likes'],
            'dislikes' => $counts['dislikes'],
            'user_reaction' => $userReaction,
        ]);
    }
}
