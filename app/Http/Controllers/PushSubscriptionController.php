<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController extends Controller
{
    /**
     * Salvează o subscripție push pentru utilizatorul autentificat.
     */
    public function store(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
            'keys.auth' => 'required|string',
            'keys.p256dh' => 'required|string',
        ]);

        $user = Auth::user();

        // Actualizează sau creează subscripția
        $user->updatePushSubscription(
            $request->endpoint,
            $request->keys['p256dh'],
            $request->keys['auth']
        );

        return response()->json([
            'success' => true,
            'message' => 'Subscripția pentru notificări push a fost salvată.',
        ]);
    }

    /**
     * Șterge subscripția push pentru endpoint-ul specificat.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
        ]);

        $user = Auth::user();
        
        $user->deletePushSubscription($request->endpoint);

        return response()->json([
            'success' => true,
            'message' => 'Subscripția pentru notificări push a fost ștearsă.',
        ]);
    }

    /**
     * Verifică dacă utilizatorul are subscripții active.
     */
    public function status()
    {
        $user = Auth::user();
        
        $hasSubscriptions = $user->pushSubscriptions()->exists();

        return response()->json([
            'subscribed' => $hasSubscriptions,
            'count' => $user->pushSubscriptions()->count(),
        ]);
    }
}
