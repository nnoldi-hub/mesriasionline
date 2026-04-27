<?php

namespace App\Http\Controllers;

use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SessionController extends Controller
{
    /**
     * Show user sessions page
     */
    public function index()
    {
        $sessions = UserSession::where('user_id', auth()->id())
            ->orderBy('last_activity_at', 'desc')
            ->get();

        $currentSessionId = session()->getId();

        return view('security.sessions', compact('sessions', 'currentSessionId'));
    }

    /**
     * Terminate a specific session
     */
    public function destroy(int $sessionId): JsonResponse
    {
        $session = UserSession::where('user_id', auth()->id())
            ->where('id', $sessionId)
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Sesiunea nu a fost găsită.',
            ], 404);
        }

        if ($session->session_id === session()->getId()) {
            return response()->json([
                'success' => false,
                'message' => 'Nu poți închide sesiunea curentă.',
            ], 400);
        }

        $session->terminate();

        return response()->json([
            'success' => true,
            'message' => 'Sesiunea a fost închisă.',
        ]);
    }

    /**
     * Logout all other sessions
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $count = UserSession::terminateOtherSessions(
            auth()->id(),
            session()->getId()
        );

        return response()->json([
            'success' => true,
            'message' => "Au fost închise {$count} sesiuni.",
            'count' => $count,
        ]);
    }
}
