<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\QuoteRequest;
use App\Models\Review;
use App\Models\Conversation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Statistici pentru dashboard
        $stats = [
            'addresses' => $user->addresses()->count(),
            'quote_requests' => QuoteRequest::where('client_id', $user->id)->count(),
            'appointments' => Appointment::where('client_email', $user->email)->count(),
            'reviews_given' => Review::where('client_id', $user->id)->count(),
            'conversations' => Conversation::where(function($q) use ($user) {
                $q->where('client_id', $user->id)->orWhere('specialist_id', $user->id);
            })->count(),
        ];
        
        // Ultimele cereri de ofertă
        $recentQuoteRequests = QuoteRequest::where('client_id', $user->id)
            ->with(['craftsman', 'service'])
            ->latest()
            ->take(5)
            ->get();
        
        // Programări viitoare (căutăm după email pentru că nu avem client_id)
        $upcomingAppointments = Appointment::where('client_email', $user->email)
            ->where('appointment_date', '>=', now()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->with('specialist')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->take(5)
            ->get();
        
        return view('client.dashboard', compact('stats', 'recentQuoteRequests', 'upcomingAppointments'));
    }

    public function profile()
    {
        $user = auth()->user();
        return view('client.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);
        
        $user->update($validated);
        
        return back()->with('success', 'Profilul a fost actualizat cu succes!');
    }
}
