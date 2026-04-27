<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleQuestion;
use App\Models\Category;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a listing of published articles.
     */
    public function index(Request $request)
    {
        $query = Article::published()->with(['author', 'featuredCraftsman']);

        // Filter by type
        if ($request->type && in_array($request->type, ['article', 'interview', 'guide', 'news'])) {
            $query->where('type', $request->type);
        }

        // Filter by tag
        if ($request->tag) {
            $query->whereJsonContains('tags', $request->tag);
        }

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('excerpt', 'like', "%{$request->search}%")
                  ->orWhere('content', 'like', "%{$request->search}%");
            });
        }

        $articles = $query->latest('published_at')->paginate(12);

        // Get featured articles for sidebar
        $featuredArticles = Article::published()
            ->where('type', 'interview')
            ->latest('published_at')
            ->take(5)
            ->get();

        // Get all unique tags
        $allTags = Article::published()
            ->whereNotNull('tags')
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->values();

        return view('articles.index', compact('articles', 'featuredArticles', 'allTags'));
    }

    /**
     * Display the specified article.
     */
    public function show($slug)
    {
        $article = Article::where('slug', $slug)
            ->published()
            ->with(['author', 'featuredCraftsman', 'questions' => function ($q) {
                $q->visible()->latest();
            }])
            ->firstOrFail();

        // Increment views
        $article->incrementViews();

        // Related articles
        $relatedArticles = Article::published()
            ->where('id', '!=', $article->id)
            ->where(function ($q) use ($article) {
                $q->where('type', $article->type);
                if ($article->tags) {
                    foreach ($article->tags as $tag) {
                        $q->orWhereJsonContains('tags', $tag);
                    }
                }
            })
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('articles.show', compact('article', 'relatedArticles'));
    }

    /**
     * Display the Q&A page.
     */
    public function questions(Request $request)
    {
        $query = ArticleQuestion::visible()->with(['category', 'answeredBy']);

        // Filter by category
        if ($request->category) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter to show only answered
        if ($request->filter === 'answered') {
            $query->answered();
        }

        $questions = $query->latest()->paginate(15);

        // Featured questions
        $featuredQuestions = ArticleQuestion::visible()
            ->featured()
            ->with(['category', 'answeredBy'])
            ->latest()
            ->take(5)
            ->get();

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('articles.questions', compact('questions', 'featuredQuestions', 'categories'));
    }

    /**
     * Show form to ask a question.
     */
    public function askQuestion()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('articles.ask-question', compact('categories'));
    }

    /**
     * Store a new question from visitor.
     */
    public function storeQuestion(Request $request)
    {
        $validated = $request->validate([
            'author_name' => 'required|string|max:100',
            'author_email' => 'required|email|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'title' => 'required|string|max:255',
            'question' => 'required|string|max:2000',
        ], [
            'author_name.required' => 'Te rugăm să introduci numele tău.',
            'author_email.required' => 'Te rugăm să introduci adresa de email.',
            'author_email.email' => 'Adresa de email nu este validă.',
            'title.required' => 'Te rugăm să introduci subiectul întrebării.',
            'question.required' => 'Te rugăm să scrii întrebarea.',
            'question.max' => 'Întrebarea nu poate depăși 2000 de caractere.',
        ]);

        ArticleQuestion::create([
            'author_name' => $validated['author_name'],
            'author_email' => $validated['author_email'],
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'question' => $validated['question'],
            'status' => 'pending',
        ]);

        return redirect()->route('intrebari')
            ->with('success', 'Întrebarea ta a fost trimisă! Vei primi răspuns pe email după ce va fi aprobată.');
    }

    /**
     * Display interviews listing.
     */
    public function interviews()
    {
        $interviews = Article::published()
            ->where('type', 'interview')
            ->with(['author', 'featuredCraftsman'])
            ->latest('published_at')
            ->paginate(12);

        return view('articles.interviews', compact('interviews'));
    }
}
