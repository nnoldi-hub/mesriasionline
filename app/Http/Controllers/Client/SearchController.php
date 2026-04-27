<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Afișează pagina de căutare meșteri cu filtre.
     */
    public function index(Request $request)
    {
        $locations = Location::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        
        $craftsmen = null;
        
        // Dacă avem filtre, căutăm meseriași
        if ($request->has('location_id') || $request->has('category_id') || $request->has('address_id')) {
            $query = User::where('role', 'specialist')
                ->where('is_active', true)
                ->with(['location', 'services', 'reviews']);
            
            // Filtrare după locație
            $locationId = $request->location_id;
            
            // Dacă s-a selectat o adresă, folosim locația adresei
            if ($request->address_id) {
                $address = auth()->user()->addresses()->find($request->address_id);
                if ($address && $address->location_id) {
                    $locationId = $address->location_id;
                }
            }
            
            if ($locationId) {
                $query->where('location_id', $locationId);
            }
            
            // Filtrare după categorie (prin servicii)
            if ($request->category_id) {
                $query->whereHas('services', function($q) use ($request) {
                    $q->where('category_id', $request->category_id);
                });
            }
            
            // Adaugă statistici
            $query->withCount('reviews')
                ->withAvg('reviews', 'rating');
            
            // Sortare: prioritate verificați, apoi după rating
            $query->orderByDesc('is_verified')
                ->orderByDesc('reviews_avg_rating')
                ->orderByDesc('reviews_count');
            
            $craftsmen = $query->paginate(10)->appends($request->query());
            
            // Redenumim coloana pentru template
            $craftsmen->each(function($craftsman) {
                $craftsman->average_rating = $craftsman->reviews_avg_rating ?? 0;
            });
        }
        
        return view('client.search.index', compact('locations', 'categories', 'craftsmen'));
    }
}
