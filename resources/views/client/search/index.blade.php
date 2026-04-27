@extends('layouts.client')

@section('title', 'Caută Meșteri')
@section('page-title', 'Caută Meșteri în Zona Ta')

@section('content')
<div class="space-y-6">
    <!-- Filtru căutare -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('client.search') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Selectează adresa -->
                <div>
                    <label for="address_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Locația ta
                    </label>
                    <select id="address_id" name="address_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600">
                        <option value="">-- Selectează o adresă --</option>
                        @foreach(auth()->user()->addresses as $address)
                        <option value="{{ $address->id }}" 
                            data-location="{{ $address->location_id }}"
                            {{ request('address_id') == $address->id ? 'selected' : '' }}>
                            {{ $address->name }} - {{ $address->city }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sau selectează zonă direct -->
                <div>
                    <label for="location_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Sau alege zona
                    </label>
                    <select id="location_id" name="location_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600">
                        <option value="">-- Toate zonele --</option>
                        @foreach($locations ?? [] as $location)
                        <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                            {{ $location->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Categorie serviciu -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Tip serviciu
                    </label>
                    <select id="category_id" name="category_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600">
                        <option value="">-- Toate categoriile --</option>
                        @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Buton căutare -->
                <div class="flex items-end">
                    <button type="submit" 
                        class="w-full px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Caută
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Prompt adăugare adresă dacă nu are -->
    @if(auth()->user()->addresses->count() === 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-yellow-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <h4 class="text-sm font-medium text-yellow-800">Adaugă o adresă pentru rezultate mai bune</h4>
                <p class="text-sm text-yellow-700 mt-1">
                    Adaugă cel puțin o adresă pentru a găsi meșteri disponibili în zona ta.
                </p>
                <a href="{{ route('client.addresses.create') }}" class="inline-block mt-2 text-sm font-medium text-yellow-800 hover:text-yellow-900 underline">
                    Adaugă adresă →
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Rezultate -->
    @if(isset($craftsmen) && $craftsmen->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200">
            <p class="text-sm text-gray-600">
                {{ $craftsmen->total() }} meșteri găsiți 
                @if(request('location_id'))
                în zona selectată
                @endif
            </p>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($craftsmen as $craftsman)
            <div class="p-6 hover:bg-gray-50 transition">
                <div class="flex items-start space-x-4">
                    <!-- Avatar -->
                    <a href="{{ route('craftsman.show', $craftsman->slug) }}" class="block flex-shrink-0">
                        @if($craftsman->profile_image)
                        <img src="{{ Storage::url($craftsman->profile_image) }}" 
                            alt="{{ $craftsman->name }}"
                            class="w-16 h-16 rounded-lg object-cover">
                        @else
                        <div class="w-16 h-16 bg-primary-100 rounded-lg flex items-center justify-center text-primary-600 text-xl font-bold">
                            {{ strtoupper(substr($craftsman->name, 0, 2)) }}
                        </div>
                        @endif
                    </a>
                    
                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between">
                            <div>
                                <a href="{{ route('craftsman.show', $craftsman->slug) }}" 
                                    class="text-lg font-medium text-gray-900 hover:text-primary-600">
                                    {{ $craftsman->name }}
                                    @if($craftsman->is_verified)
                                    <svg class="inline w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    @endif
                                </a>
                                <p class="text-sm text-gray-600 mt-1">{{ $craftsman->trade ?? 'Meșter' }}</p>
                                @if($craftsman->location)
                                <p class="text-sm text-gray-500">
                                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $craftsman->location->name }}
                                </p>
                                @endif
                            </div>
                            
                            <!-- Rating -->
                            <div class="text-right">
                                @if($craftsman->reviews_count > 0)
                                <div class="flex items-center justify-end">
                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <span class="ml-1 font-medium text-gray-900">{{ number_format($craftsman->average_rating, 1) }}</span>
                                    <span class="ml-1 text-sm text-gray-500">({{ $craftsman->reviews_count }})</span>
                                </div>
                                @else
                                <span class="text-sm text-gray-400">Fără recenzii</span>
                                @endif
                            </div>
                        </div>

                        <!-- Servicii -->
                        @if($craftsman->services->count() > 0)
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($craftsman->services->take(4) as $service)
                            <span class="inline-block px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">
                                {{ $service->name }}
                            </span>
                            @endforeach
                            @if($craftsman->services->count() > 4)
                            <span class="inline-block px-2 py-1 text-gray-500 text-xs">
                                +{{ $craftsman->services->count() - 4 }} servicii
                            </span>
                            @endif
                        </div>
                        @endif

                        <!-- Acțiuni -->
                        <div class="mt-4 flex items-center space-x-3">
                            <a href="{{ route('craftsman.show', $craftsman->slug) }}" 
                                class="px-4 py-2 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700 transition">
                                Vezi Profil
                            </a>
                            <a href="{{ route('messages.create') }}?to={{ $craftsman->id }}" 
                                class="px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition">
                                Trimite Mesaj
                            </a>
                            <a href="{{ route('quotes.create') }}?specialist={{ $craftsman->id }}" 
                                class="px-4 py-2 border border-primary-600 text-primary-600 text-sm rounded-lg hover:bg-primary-50 transition">
                                Cere Ofertă
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($craftsmen->hasPages())
        <div class="p-4 border-t border-gray-200">
            {{ $craftsmen->links() }}
        </div>
        @endif
    </div>
    @elseif(request()->has('location_id') || request()->has('category_id'))
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Nu am găsit meșteri</h3>
        <p class="text-gray-600">Încearcă să modifici filtrele de căutare sau să alegi altă zonă.</p>
    </div>
    @else
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Caută meșteri în zona ta</h3>
        <p class="text-gray-600 mb-4">Selectează o adresă sau o zonă pentru a vedea meșerii disponibili.</p>
        @if(auth()->user()->addresses->count() === 0)
        <a href="{{ route('client.addresses.create') }}" 
            class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Adaugă Prima Adresă
        </a>
        @endif
    </div>
    @endif
</div>

@push('scripts')
<script>
document.getElementById('address_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const locationId = selectedOption.getAttribute('data-location');
    if (locationId) {
        document.getElementById('location_id').value = locationId;
    }
});
</script>
@endpush
@endsection
