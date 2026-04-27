<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated user.
     */
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->paginate(20);
        
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Get unread notifications count (for API/AJAX).
     */
    public function unreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Get recent unread notifications (for dropdown).
     */
    public function recent()
    {
        $notifications = Auth::user()
            ->notifications()
            ->take(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->data['type'] ?? 'general',
                    'title' => $this->getNotificationTitle($notification),
                    'message' => $this->getNotificationMessage($notification),
                    'url' => $notification->data['url'] ?? '#',
                    'time_ago' => $notification->created_at->diffForHumans(),
                    'read_at' => $notification->read_at,
                ];
            });
        
        return response()->json([
            'notifications' => $notifications,
            'total_unread' => Auth::user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(string $id)
    {
        $notification = Auth::user()
            ->notifications()
            ->where('id', $id)
            ->first();
        
        if ($notification) {
            $notification->markAsRead();
        }
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        // Redirect to the notification's URL if available
        $url = $notification?->data['url'] ?? route('notifications.index');
        
        return redirect($url);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back()
            ->with('success', 'Toate notificările au fost marcate ca citite.');
    }

    /**
     * Delete a notification.
     */
    public function destroy(string $id)
    {
        Auth::user()
            ->notifications()
            ->where('id', $id)
            ->delete();
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back()
            ->with('success', 'Notificarea a fost ștearsă.');
    }

    /**
     * Delete all read notifications.
     */
    public function destroyRead()
    {
        Auth::user()
            ->readNotifications()
            ->delete();
        
        return redirect()->back()
            ->with('success', 'Notificările citite au fost șterse.');
    }

    /**
     * Get notification title based on type.
     */
    private function getNotificationTitle($notification): string
    {
        $type = $notification->data['type'] ?? 'general';
        
        return match($type) {
            'new_message' => 'Mesaj nou',
            'new_quote_request' => 'Cerere de ofertă nouă',
            'quote_received' => 'Ofertă primită',
            'quote_accepted' => 'Ofertă acceptată',
            'new_review' => 'Recenzie nouă',
            'new_appointment' => 'Programare nouă',
            default => 'Notificare',
        };
    }

    /**
     * Get notification message based on type.
     */
    private function getNotificationMessage($notification): string
    {
        $data = $notification->data;
        $type = $data['type'] ?? 'general';
        
        return match($type) {
            'new_message' => ($data['sender_name'] ?? 'Cineva') . ': ' . ($data['preview'] ?? ''),
            'new_quote_request' => 'Cerere nouă: ' . ($data['title'] ?? ''),
            'quote_received' => ($data['craftsman_name'] ?? 'Meseriaș') . ' - ' . ($data['price'] ?? ''),
            'quote_accepted' => 'Felicitări! ' . ($data['client_name'] ?? 'Clientul') . ' a acceptat oferta.',
            'new_review' => 'Rating: ' . str_repeat('⭐', $data['rating'] ?? 5),
            'new_appointment' => 'Data: ' . ($data['scheduled_at'] ?? ''),
            default => 'Ai o notificare nouă.',
        };
    }
}
