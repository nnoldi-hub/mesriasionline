<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientAddress;
use App\Models\Location;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = auth()->user()->addresses()->with('location')->get();
        return view('client.addresses.index', compact('addresses'));
    }

    public function create()
    {
        $locations = Location::orderBy('name')->get();
        return view('client.addresses.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'street' => 'required|string|max:255',
            'number' => 'nullable|string|max:20',
            'building' => 'nullable|string|max:20',
            'entrance' => 'nullable|string|max:10',
            'floor' => 'nullable|string|max:10',
            'apartment' => 'nullable|string|max:20',
            'city' => 'required|string|max:100',
            'county' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'location_id' => 'nullable|exists:locations,id',
            'notes' => 'nullable|string|max:500',
            'is_default' => 'boolean',
        ]);
        
        $validated['user_id'] = auth()->id();
        
        // If this is the first address, make it default
        if (auth()->user()->addresses()->count() === 0) {
            $validated['is_default'] = true;
        }
        
        $address = ClientAddress::create($validated);
        
        // If marked as default, update others
        if ($request->boolean('is_default')) {
            $address->setAsDefault();
        }
        
        return redirect()->route('client.addresses.index')
            ->with('success', 'Adresa a fost adăugată cu succes!');
    }

    public function edit(ClientAddress $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }
        
        $locations = Location::orderBy('name')->get();
        return view('client.addresses.edit', compact('address', 'locations'));
    }

    public function update(Request $request, ClientAddress $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'street' => 'required|string|max:255',
            'number' => 'nullable|string|max:20',
            'building' => 'nullable|string|max:20',
            'entrance' => 'nullable|string|max:10',
            'floor' => 'nullable|string|max:10',
            'apartment' => 'nullable|string|max:20',
            'city' => 'required|string|max:100',
            'county' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'location_id' => 'nullable|exists:locations,id',
            'notes' => 'nullable|string|max:500',
            'is_default' => 'boolean',
        ]);
        
        $address->update($validated);
        
        // If marked as default, update others
        if ($request->boolean('is_default')) {
            $address->setAsDefault();
        }
        
        return redirect()->route('client.addresses.index')
            ->with('success', 'Adresa a fost actualizată cu succes!');
    }

    public function destroy(ClientAddress $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }
        
        $wasDefault = $address->is_default;
        $address->delete();
        
        // If deleted address was default, set another as default
        if ($wasDefault) {
            $firstAddress = auth()->user()->addresses()->first();
            if ($firstAddress) {
                $firstAddress->update(['is_default' => true]);
            }
        }
        
        return redirect()->route('client.addresses.index')
            ->with('success', 'Adresa a fost ștearsă!');
    }

    public function setDefault(ClientAddress $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }
        
        $address->setAsDefault();
        
        return back()->with('success', 'Adresa a fost setată ca implicită!');
    }
}
