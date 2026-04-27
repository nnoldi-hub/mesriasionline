<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleQuestion;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Display a listing of articles.
     */
    public function index(Request $request)
    {
        $query = Article::with('author', 'featuredCraftsman');

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('content', 'like', "%{$request->search}%");
            });
        }

        $articles = $query->latest()->paginate(20);

        $stats = [
            'total' => Article::count(),
            'published' => Article::where('status', 'published')->count(),
            'drafts' => Article::where('status', 'draft')->count(),
            'interviews' => Article::where('type', 'interview')->count(),
        ];

        return view('admin.articles.index', compact('articles', 'stats'));
    }

    /**
     * Show the form for creating a new article.
     */
    public function create()
    {
        $craftsmen = User::where('role', 'specialist')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.articles.create', compact('craftsmen'));
    }

    /**
     * Store a newly created article.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'type' => 'required|in:article,interview,guide,news',
            'featured_craftsman_id' => 'nullable|exists:users,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'tags' => 'nullable|string',
            'status' => 'required|in:draft,published',
        ]);

        $article = new Article();
        $article->author_id = auth()->id();
        $article->title = $validated['title'];
        $article->slug = Str::slug($validated['title']);
        $article->excerpt = $validated['excerpt'];
        $article->content = $validated['content'];
        $article->type = $validated['type'];
        $article->featured_craftsman_id = $validated['featured_craftsman_id'];
        $article->status = $validated['status'];

        if ($validated['status'] === 'published') {
            $article->published_at = now();
        }

        if ($request->tags) {
            $article->tags = array_map('trim', explode(',', $request->tags));
        }

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('articles', 'public');
            $article->featured_image = $path;
        }

        $article->save();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Articolul a fost creat cu succes!');
    }

    /**
     * Show the form for editing an article.
     */
    public function edit($id)
    {
        $article = Article::findOrFail($id);
        $craftsmen = User::where('role', 'specialist')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.articles.edit', compact('article', 'craftsmen'));
    }

    /**
     * Update the specified article.
     */
    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'type' => 'required|in:article,interview,guide,news',
            'featured_craftsman_id' => 'nullable|exists:users,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'tags' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
        ]);

        $article->title = $validated['title'];
        $article->excerpt = $validated['excerpt'];
        $article->content = $validated['content'];
        $article->type = $validated['type'];
        $article->featured_craftsman_id = $validated['featured_craftsman_id'];

        // Handle status change
        if ($validated['status'] === 'published' && $article->status !== 'published') {
            $article->published_at = now();
        }
        $article->status = $validated['status'];

        if ($request->tags) {
            $article->tags = array_map('trim', explode(',', $request->tags));
        } else {
            $article->tags = null;
        }

        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }
            $path = $request->file('featured_image')->store('articles', 'public');
            $article->featured_image = $path;
        }

        $article->save();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Articolul a fost actualizat cu succes!');
    }

    /**
     * Remove the specified article.
     */
    public function destroy($id)
    {
        $article = Article::findOrFail($id);

        if ($article->featured_image) {
            Storage::disk('public')->delete($article->featured_image);
        }

        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Articolul a fost șters.');
    }

    /**
     * Display questions list.
     */
    public function questions(Request $request)
    {
        $query = ArticleQuestion::with(['category', 'answeredBy']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $questions = $query->latest()->paginate(20);

        $stats = [
            'total' => ArticleQuestion::count(),
            'pending' => ArticleQuestion::where('status', 'pending')->count(),
            'answered' => ArticleQuestion::where('status', 'answered')->count(),
            'featured' => ArticleQuestion::where('is_featured', true)->count(),
        ];

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.articles.questions', compact('questions', 'stats', 'categories'));
    }

    /**
     * Show the form for answering a question.
     */
    public function answerQuestion($id)
    {
        $question = ArticleQuestion::with(['category', 'article'])->findOrFail($id);

        return view('admin.articles.answer-question', compact('question'));
    }

    /**
     * Store the answer for a question.
     */
    public function storeAnswer(Request $request, $id)
    {
        $question = ArticleQuestion::findOrFail($id);

        $validated = $request->validate([
            'answer' => 'required|string',
            'is_featured' => 'boolean',
        ]);

        $question->update([
            'answer' => $validated['answer'],
            'answered_by' => auth()->id(),
            'answered_at' => now(),
            'status' => 'answered',
            'is_featured' => $request->has('is_featured'),
        ]);

        return redirect()->route('admin.articles.questions')
            ->with('success', 'Răspunsul a fost salvat!');
    }

    /**
     * Update question status.
     */
    public function updateQuestionStatus(Request $request, $id)
    {
        $question = ArticleQuestion::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,answered,rejected',
        ]);

        $question->update(['status' => $validated['status']]);

        return back()->with('success', 'Statusul întrebării a fost actualizat.');
    }

    /**
     * Toggle featured status of a question.
     */
    public function toggleQuestionFeatured($id)
    {
        $question = ArticleQuestion::findOrFail($id);
        $question->update(['is_featured' => !$question->is_featured]);

        $status = $question->is_featured ? 'adăugată la' : 'eliminată din';
        return back()->with('success', "Întrebarea a fost {$status} featured.");
    }

    /**
     * Delete a question.
     */
    public function deleteQuestion($id)
    {
        $question = ArticleQuestion::findOrFail($id);
        $question->delete();

        return back()->with('success', 'Întrebarea a fost ștearsă.');
    }
}
