<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotKnowledge;
use Illuminate\Http\Request;

class ChatbotKnowledgeController extends Controller
{
    public function index()
    {
        $entries = ChatbotKnowledge::orderByDesc('priority')->orderByDesc('id')->paginate(20);
        return view('admin.chatbot-knowledge.index', compact('entries'));
    }

    public function create()
    {
        return view('admin.chatbot-knowledge.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question_example' => ['required', 'string', 'max:255'],
            'keywords'         => ['required', 'string', 'max:500'],
            'answer'           => ['required', 'string', 'max:1000'],
            'cta_label'        => ['nullable', 'string', 'max:100'],
            'cta_url'          => ['nullable', 'string', 'max:255'],
            'priority'         => ['nullable', 'integer', 'min:0', 'max:100'],
            'is_active'        => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['priority']  = $data['priority'] ?? 0;

        ChatbotKnowledge::create($data);

        return redirect()->route('admin.chatbot.knowledge.index')
            ->with('success', 'Intrare adăugată cu succes.');
    }

    public function edit(ChatbotKnowledge $knowledge)
    {
        return view('admin.chatbot-knowledge.edit', compact('knowledge'));
    }

    public function update(Request $request, ChatbotKnowledge $knowledge)
    {
        $data = $request->validate([
            'question_example' => ['required', 'string', 'max:255'],
            'keywords'         => ['required', 'string', 'max:500'],
            'answer'           => ['required', 'string', 'max:1000'],
            'cta_label'        => ['nullable', 'string', 'max:100'],
            'cta_url'          => ['nullable', 'string', 'max:255'],
            'priority'         => ['nullable', 'integer', 'min:0', 'max:100'],
            'is_active'        => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['priority']  = $data['priority'] ?? 0;

        $knowledge->update($data);

        return redirect()->route('admin.chatbot.knowledge.index')
            ->with('success', 'Intrare actualizată cu succes.');
    }

    public function destroy(ChatbotKnowledge $knowledge)
    {
        $knowledge->delete();

        return redirect()->route('admin.chatbot.knowledge.index')
            ->with('success', 'Intrare ștearsă.');
    }

    public function toggleActive(ChatbotKnowledge $knowledge)
    {
        $knowledge->update(['is_active' => !$knowledge->is_active]);

        return back()->with('success', 'Status actualizat.');
    }

    /**
     * Pagina de testare a knowledge base-ului.
     */
    public function test()
    {
        $totalEntries  = ChatbotKnowledge::count();
        $activeEntries = ChatbotKnowledge::where('is_active', true)->count();
        return view('admin.chatbot-knowledge.test', compact('totalEntries', 'activeEntries'));
    }

    /**
     * Testează un mesaj față de knowledge base și returnează rezultatul JSON.
     */
    public function testQuery(Request $request)
    {
        $request->validate(['message' => ['required', 'string', 'max:500']]);

        $message = trim($request->input('message'));
        $entries = ChatbotKnowledge::active()->get();

        $results = [];
        foreach ($entries as $entry) {
            $matched = $entry->matchesMessage($message);
            $results[] = [
                'id'               => $entry->id,
                'question_example' => $entry->question_example,
                'keywords'         => $entry->keywords,
                'matched'          => $matched,
                'cta_label'        => $entry->cta_label,
                'cta_url'          => $entry->cta_url,
                'answer_preview'   => mb_substr($entry->answer, 0, 80) . '...',
            ];
        }

        // Sortăm matched-urile primele
        usort($results, fn($a, $b) => $b['matched'] <=> $a['matched']);

        $firstMatch = collect($results)->firstWhere('matched', true);

        return response()->json([
            'message'     => $message,
            'total'       => count($results),
            'first_match' => $firstMatch,
            'all_results' => $results,
        ]);
    }
}
