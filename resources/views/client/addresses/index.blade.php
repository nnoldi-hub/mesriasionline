@extends('layouts.client')

@section('title', 'Adresele Mele')
@section('page-title', 'Adresele Mele')

@section('header-actions')
<a href="{{ route('client.addresses.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">
    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
    </svg>
    Adaugă Adresă
</a>
@endsection

@section('content')
<div class="space-y-6">
    @if($addresses->isEmpty())
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Nu ai adrese salvate</h3>
        <p class="text-gray-500 mb-6">Adaugă adresele tale pentru a găsi meșteri în zona respectivă și a face programări mai ușor.</p>
        <a href="{{ route('client.addresses.create') }}" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
            </svg>
            Adaugă Prima Adresă
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($addresses as $address)
        <div class="bg-white rounded-lg shadow hover:shadow-md transition relative {{ $address->is_default ? 'ring-2 ring-primary-500' : '' }}">
            @if($address->is_default)
            <div class="absolute -top-2 -right-2 bg-primary-600 text-white text-xs font-bold px-2 py-1 rounded-full">
                Implicită
            </div>
            @endif
            
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-semibold text-gray-900">{{ $address->name }}</h3>
                    </div>
                </div>
                
                <div class="space-y-2 text-sm text-gray-600">
                    <p>{{ $address->street }}{{ $address->number ? ' nr. ' . $address->number : '' }}</p>
                    @if($address->building || $address->apartment)
                    <p>
                        @if($address->building)Bl. {{ $address->building }}@endif
                        @if($address->entrance), Sc. {{ $address->entrance }}@endif
                        @if($address->floor), Et. {{ $address->floor }}@endif
                        @if($address->apartment), Ap. {{ $address->apartment }}@endif
                    </p>
                    @endif
                    <p class="font-medium">{{ $address->city }}, {{ $address->county }}</p>
                    @if($address->postal_code)
                    <p>Cod poștal: {{ $address->postal_code }}</p>
                    @endif
                    @if($address->notes)
                    <p class="text-gray-500 italic mt-2">{{ $address->notes }}</p>
                    @endif
                </div>
                
                @if($address->location)
                <div class="mt-4 pt-4 border-t">
                    <span class="inline-flex items-center text-sm text-primary-600">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                        Zona: {{ $address->location->name }}
                    </span>
                </div>
                @endif
                
                <div class="mt-4 pt-4 border-t flex items-center justify-between">
                    <div class="flex space-x-2">
                        <a href="{{ route('client.addresses.edit', $address) }}" class="text-sm text-gray-600 hover:text-primary-600 transition">
                            Editează
                        </a>
                        @if(!$address->is_default)
                        <form action="{{ route('client.addresses.set-default', $address) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-sm text-gray-600 hover:text-primary-600 transition">
                                Setează implicită
                            </button>
                        </form>
                        @endif
                    </div>
                    
                    <form action="{{ route('client.addresses.destroy', $address) }}" method="POST" 
                        onsubmit="return confirm('Ești sigur că vrei să ștergi această adresă?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:text-red-700 transition">
                            Șterge
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
    
    <!-- Sfat -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div class="ml-3">
                <h4 class="text-sm font-medium text-blue-800">Sfat</h4>
                <p class="mt-1 text-sm text-blue-700">
                    Poți adăuga mai multe adrese (Acasă, Birou, Părinți, etc.) și să selectezi zona în care cauți meseriași. 
                    Adresa implicită va fi folosită automat când ceri o ofertă sau faci o programare.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
