<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotConversation;
use Illuminate\Http\Request;

class ChatbotAdminController extends Controller
{
    /**
     * Dashboard principal — statistici + lista conversații recente.
     */
    public function index(Request $request)
    {
        $period = (int) $request->get('period', 30);
        $period = in_array($period, [7, 14, 30, 90]) ? $period : 30;

        $stats = ChatbotConversation::getStats($period);

        // Top intenții pentru grafic
        $intentLabels = [
            'craftsman_register' => 'Înregistrare meseriaș',
            'client_request'     => 'Cerere client',
            'pricing'            => 'Prețuri',
            'info'               => 'Informații',
            'support'            => 'Suport',
            'other'              => 'Altele',
            'unknown'            => 'Necunoscut',
        ];

        // Conversații pe zilele ultimei perioade (pentru grafic linie)
        $dailyData = ChatbotConversation::where('created_at', '>=', now()->subDays($period))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total, SUM(converted) as conversions')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Lista conversații recente cu paginare
        $conversations = ChatbotConversation::with('user')
            ->when($request->get('intent'), fn($q, $i) => $q->where('intent', $i))
            ->when($request->get('converted'), fn($q) => $q->where('converted', true))
            ->when($request->get('search'), function ($q, $s) {
                $q->whereHas('messages', fn($m) => $m->where('content', 'like', "%{$s}%"));
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.chatbot.index', compact(
            'stats',
            'conversations',
            'intentLabels',
            'dailyData',
            'period',
        ));
    }

    /**
     * Detaliu conversație completă.
     */
    public function show(ChatbotConversation $conversation)
    {
        $conversation->load('messages', 'user');

        return view('admin.chatbot.show', compact('conversation'));
    }

    /**
     * Șterge o conversație (și cascadă mesajele).
     */
    public function destroy(ChatbotConversation $conversation)
    {
        $conversation->delete();

        return redirect()->route('admin.chatbot.index')
            ->with('success', 'Conversație ștearsă.');
    }
}
