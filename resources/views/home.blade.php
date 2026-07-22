@extends('layouts.app')

@section('title', 'Găsește meseriași profesioniști verificați')

@section('content')
<!-- Hero Section -->
<section style="background-color: #ECF0F1;" class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-6 leading-tight" style="font-family: 'Rubik', sans-serif; color: #2980B9;">
                Găsește meseriașul potrivit<br>în câteva minute
            </h1>
            <p class="text-xl mb-8" style="color: #2C3E50;">
                Electricieni, instalatori, zugravi și alți profesioniști verificați.
            </p>

            {{-- CTA dublu: caută SAU postează cerere --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 mb-8">
                <a href="{{ route('public-request.create') }}"
                    class="inline-flex items-center text-white font-bold px-8 py-4 rounded-2xl text-lg shadow-xl transition hover:opacity-90"
                    style="background-color: #C0392B;">
                    🔨 Ai nevoie de un meseriaș?
                    <span class="ml-2 text-sm font-normal opacity-90">Cere oferte gratuit →</span>
                </a>
            </div>

            <p class="text-sm mb-6" style="color: #7f8c8d;">sau caută direct în lista de meseriași:</p>
            
            <!-- Search Form -->
            <form action="{{ route('home') }}" method="GET" class="bg-white rounded-2xl shadow-xl p-5" id="searchForm">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <select name="category_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-secondary-500 focus:border-transparent text-gray-900">
                            <option value="">Toate categoriile</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="location_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-secondary-500 focus:border-transparent text-gray-900">
                            <option value="">Toate locațiile</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Caută meseriaṣ..." class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-secondary-500 focus:border-transparent text-gray-900">
                    </div>
                    <div>
                        <button type="submit" class="w-full text-white font-bold px-6 py-3 rounded-xl transition hover:opacity-90 shadow" style="background-color: #C0392B;">
                            🔍 Caută
                        </button>
                    </div>
                </div>
                
                <!-- Hidden fields for geolocation -->
                <input type="hidden" name="lat" id="userLat" value="{{ request('lat') }}">
                <input type="hidden" name="lng" id="userLng" value="{{ request('lng') }}">
                
                <!-- Advanced Filters Toggle -->
                <div class="mt-4 border-t pt-4">
                    <button type="button" id="toggleFilters" class="text-primary-600 hover:text-primary-700 font-medium text-sm flex items-center mx-auto">
                        <svg class="w-4 h-4 mr-1 transition-transform" id="filterArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                        Filtre avansate
                    </button>
                    
                    <!-- Advanced Filters Panel -->
                    <div id="advancedFilters" class="hidden mt-4">
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 text-left">
                            <!-- Rating Filter -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Rating minim</label>
                                <select name="min_rating" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-600 focus:border-transparent text-gray-900 text-sm">
                                    <option value="">Oricare</option>
                                    <option value="3" {{ request('min_rating') == '3' ? 'selected' : '' }}>3+ ⭐</option>
                                    <option value="4" {{ request('min_rating') == '4' ? 'selected' : '' }}>4+ ⭐</option>
                                    <option value="4.5" {{ request('min_rating') == '4.5' ? 'selected' : '' }}>4.5+ ⭐</option>
                                </select>
                            </div>
                            
                            <!-- Sort By -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Sortare</label>
                                <select name="sort" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-600 focus:border-transparent text-gray-900 text-sm">
                                    <option value="recommended" {{ request('sort', 'recommended') == 'recommended' ? 'selected' : '' }}>Recomandate</option>
                                    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Rating</option>
                                    <option value="reviews" {{ request('sort') == 'reviews' ? 'selected' : '' }}>Nr. recenzii</option>
                                    <option value="experience" {{ request('sort') == 'experience' ? 'selected' : '' }}>Experiență</option>
                                    <option value="distance" {{ request('sort') == 'distance' ? 'selected' : '' }}>Distanță</option>
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Cei mai noi</option>
                                </select>
                            </div>
                            
                            <!-- Experience Filter -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Experiență min.</label>
                                <select name="min_experience" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-600 focus:border-transparent text-gray-900 text-sm">
                                    <option value="">Oricare</option>
                                    <option value="2" {{ request('min_experience') == '2' ? 'selected' : '' }}>2+ ani</option>
                                    <option value="5" {{ request('min_experience') == '5' ? 'selected' : '' }}>5+ ani</option>
                                    <option value="10" {{ request('min_experience') == '10' ? 'selected' : '' }}>10+ ani</option>
                                </select>
                            </div>
                            
                            <!-- Distance Radius -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Rază (km)</label>
                                <select name="radius" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-600 focus:border-transparent text-gray-900 text-sm">
                                    <option value="10" {{ request('radius') == '10' ? 'selected' : '' }}>10 km</option>
                                    <option value="25" {{ request('radius') == '25' ? 'selected' : '' }}>25 km</option>
                                    <option value="50" {{ request('radius', '50') == '50' ? 'selected' : '' }}>50 km</option>
                                    <option value="100" {{ request('radius') == '100' ? 'selected' : '' }}>100 km</option>
                                </select>
                            </div>
                            
                            <!-- Checkboxes -->
                            <div class="col-span-2 md:col-span-4 lg:col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-2">Opțiuni</label>
                                <div class="flex flex-wrap gap-3">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="verified" value="1" {{ request('verified') ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-600">
                                        <span class="ml-1 text-sm text-gray-700">Verificat</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="featured" value="1" {{ request('featured') ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-600">
                                        <span class="ml-1 text-sm text-gray-700">Recomandat</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="has_gallery" value="1" {{ request('has_gallery') ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-600">
                                        <span class="ml-1 text-sm text-gray-700">Cu poze</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="has_reviews" value="1" {{ request('has_reviews') ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-600">
                                        <span class="ml-1 text-sm text-gray-700">Cu recenzii</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="available_weekends" value="1" {{ request('available_weekends') ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-600">
                                        <span class="ml-1 text-sm text-gray-700">Weekend</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="emergency_services" value="1" {{ request('emergency_services') ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-600">
                                        <span class="ml-1 text-sm text-gray-700">Urgențe</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="has_insurance" value="1" {{ request('has_insurance') ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-600">
                                        <span class="ml-1 text-sm text-gray-700">Asigurat</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Geolocation Button -->
                        <div class="mt-4 flex items-center justify-center gap-4">
                            <button type="button" id="getLocationBtn" class="inline-flex items-center px-4 py-2 bg-secondary-100 hover:bg-secondary-200 text-gray-700 rounded-lg text-sm transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span id="locationBtnText">Folosește locația mea</span>
                            </button>
                            
                            @if(request()->hasAny(['min_rating', 'verified', 'featured', 'has_gallery', 'has_reviews', 'min_experience', 'available_weekends', 'emergency_services', 'has_insurance', 'lat', 'lng']))
                                <a href="{{ route('home', array_filter(['category_id' => request('category_id'), 'location_id' => request('location_id'), 'search' => request('search')])) }}" class="text-gray-500 hover:text-gray-700 text-sm">
                                    Resetează filtrele
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Video Section -->
@if(isset($videos) && $videos->isNotEmpty())
<section class="py-16 bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold" style="font-family:'Rubik',sans-serif; color:#2C3E50;">Cum funcționează meseriasionline.ro</h2>
            <p class="mt-2" style="color:#7f8c8d;">Vezi în câteva minute cum găsești rapid omul potrivit</p>
        </div>

        <div class="rounded-2xl overflow-hidden shadow-lg bg-black mb-4" style="aspect-ratio:16/9;">
            <iframe id="main-video-player" class="w-full h-full"
                src="{{ $videos->first()->embed_url }}"
                title="{{ $videos->first()->title }}"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen></iframe>
        </div>

        <p id="main-video-title" class="text-center font-semibold mb-6" style="color:#2C3E50;">{{ $videos->first()->title }}</p>

        @if($videos->count() > 1)
        <div class="flex gap-4 overflow-x-auto pb-2">
            @foreach($videos as $video)
                <button type="button"
                    class="video-thumb shrink-0 w-40 rounded-lg overflow-hidden border-2 transition {{ $loop->first ? 'border-primary-600' : 'border-transparent hover:border-gray-300' }} focus:outline-none"
                    data-embed="{{ $video->embed_url }}"
                    data-title="{{ $video->title }}">
                    <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="w-full h-24 object-cover">
                </button>
            @endforeach
        </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const player = document.getElementById('main-video-player');
    const titleEl = document.getElementById('main-video-title');
    document.querySelectorAll('.video-thumb').forEach(function(thumb) {
        thumb.addEventListener('click', function() {
            player.src = this.dataset.embed;
            titleEl.textContent = this.dataset.title;
            document.querySelectorAll('.video-thumb').forEach(function(t) {
                t.classList.remove('border-primary-600');
                t.classList.add('border-transparent', 'hover:border-gray-300');
            });
            this.classList.remove('border-transparent', 'hover:border-gray-300');
            this.classList.add('border-primary-600');
        });
    });
});
</script>
@endpush
@endif

<!-- Stats Bar -->
@if(isset($totalCraftsmen) && $totalCraftsmen > 0)
<section class="py-5 text-white" style="background-color: #2980B9;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap justify-center items-center gap-6 md:gap-12 text-center">
            <div>
                <span class="text-2xl md:text-3xl font-extrabold" style="font-family:'Rubik',sans-serif;">{{ number_format($totalCraftsmen) }}</span>
                <span class="text-sm block opacity-80">Meseriași</span>
            </div>
            <div class="hidden md:block h-8 w-px opacity-30" style="background:#fff"></div>
            <div>
                <span class="text-2xl md:text-3xl font-extrabold" style="font-family:'Rubik',sans-serif;">{{ number_format($totalReviews ?? 0) }}</span>
                <span class="text-sm block opacity-80">Recenzii</span>
            </div>
            <div class="hidden md:block h-8 w-px opacity-30" style="background:#fff"></div>
            <div>
                <span class="text-2xl md:text-3xl font-extrabold" style="font-family:'Rubik',sans-serif;">{{ number_format($avgRating ?? 0, 1) }}</span>
                <span class="text-sm block opacity-80">Rating mediu</span>
            </div>
            @if(isset($userLat) && $userLat)
            <div class="hidden md:block h-8 w-px opacity-30" style="background:#fff"></div>
            <div class="flex items-center opacity-80">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm">Căutare în raza de {{ $searchRadius ?? 50 }} km</span>
            </div>
            @endif
        </div>
    </div>
</section>
@endif

<!-- Categories Section -->
<section id="categories" class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-extrabold text-center mb-3" style="font-family:'Rubik',sans-serif; color:#2980B9;">Categorii Populare</h2>
        <p class="text-center mb-10" style="color:#2C3E50;">Alege tipul de serviciu de care ai nevoie</p>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
            @foreach($categories->take(8) as $category)
                @if(in_array($category->slug, ['intretinere-imobile', 'mentenanta']))
                    <a href="{{ route('service.book', ['category' => $category->slug]) }}" class="flex flex-col items-center p-5 bg-gray-50 rounded-2xl border border-gray-100 hover:shadow-lg transition group" style="transition: all 0.2s;" onmouseover="this.style.backgroundColor='#F1C40F';this.querySelectorAll('svg')[0].style.color='#fff';" onmouseout="this.style.backgroundColor='#f9fafb';this.querySelectorAll('svg')[0].style.color='#2980B9';">
                @else
                    <a href="{{ route('home', ['category_id' => $category->id]) }}" class="flex flex-col items-center p-5 bg-gray-50 rounded-2xl border border-gray-100 hover:shadow-lg transition group" style="transition: all 0.2s;" onmouseover="this.style.backgroundColor='#F1C40F';this.querySelectorAll('svg')[0].style.color='#fff';" onmouseout="this.style.backgroundColor='#f9fafb';this.querySelectorAll('svg')[0].style.color='#2980B9';">
                @endif
                    <div class="w-16 h-16 bg-secondary-200 rounded-full flex items-center justify-center mb-3 group-hover:bg-secondary-300 transition">
                        @php
                            $icons = [
                                'Electrician' => '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
                                'Instalator' => '<path d="M9 2v4H7v2h2v2H7v2h2v2H7v2h2v4h2v-4h2v4h2v-4h2v-2h-2v-2h2V8h-2V6h2V2h-2v4h-2V2h-2v4H9V2zm0 6h6v2H9V8zm0 4h6v2H9v-2z"/><circle cx="12" cy="5" r="1.5"/>',
                                'Tamplar' => '<rect x="4" y="3" width="16" height="18" rx="1" fill="none" stroke="currentColor" stroke-width="2"/><path d="M8 7h8M8 11h8M8 15h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M2 8l2-2v12l-2-2V8z" fill="currentColor"/>',
                                'Zugrav' => '<path d="M19.5 3.5l-3 3m0 0l-4 4m4-4l1.5 1.5M8 8l-4 4v8h8l4-4M8 8l8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/><circle cx="17" cy="3" r="2" fill="currentColor"/><rect x="4" y="16" width="6" height="6" rx="1" fill="currentColor" opacity="0.5"/>',
                                'Zidar' => '<rect x="2" y="2" width="5" height="4" rx="0.5" fill="currentColor"/><rect x="9" y="2" width="5" height="4" rx="0.5" fill="currentColor"/><rect x="16" y="2" width="5" height="4" rx="0.5" fill="currentColor"/><rect x="2" y="8" width="5" height="4" rx="0.5" fill="currentColor"/><rect x="9" y="8" width="5" height="4" rx="0.5" fill="currentColor"/><rect x="16" y="8" width="5" height="4" rx="0.5" fill="currentColor"/><rect x="5.5" y="14" width="5" height="4" rx="0.5" fill="currentColor"/><rect x="12.5" y="14" width="5" height="4" rx="0.5" fill="currentColor"/><rect x="2" y="20" width="5" height="2" rx="0.5" fill="currentColor" opacity="0.6"/><rect x="9" y="20" width="5" height="2" rx="0.5" fill="currentColor" opacity="0.6"/><rect x="16" y="20" width="5" height="2" rx="0.5" fill="currentColor" opacity="0.6"/>',
                                'Faiantar' => '<rect x="3" y="3" width="7" height="7" rx="0.5" stroke="currentColor" stroke-width="1.5" fill="none"/><rect x="13" y="3" width="7" height="7" rx="0.5" stroke="currentColor" stroke-width="1.5" fill="none"/><rect x="3" y="13" width="7" height="7" rx="0.5" stroke="currentColor" stroke-width="1.5" fill="none"/><rect x="13" y="13" width="7" height="7" rx="0.5" stroke="currentColor" stroke-width="1.5" fill="none"/><rect x="5" y="5" width="3" height="3" fill="currentColor" opacity="0.4"/><rect x="15" y="5" width="3" height="3" fill="currentColor" opacity="0.4"/><rect x="5" y="15" width="3" height="3" fill="currentColor" opacity="0.4"/><rect x="15" y="15" width="3" height="3" fill="currentColor" opacity="0.4"/>',
                                'Frigotehnist' => '<rect x="4" y="2" width="16" height="20" rx="2" stroke="currentColor" stroke-width="2" fill="none"/><path d="M12 6v5m0 0v5m0-5h4m-4 0H8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M8 18h8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="9" cy="8" r="1" fill="currentColor"/><circle cx="15" cy="8" r="1" fill="currentColor"/><circle cx="9" cy="14" r="1" fill="currentColor"/><circle cx="15" cy="14" r="1" fill="currentColor"/>',
                                'Mecanic Auto' => '<path d="M6.5 17a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm11 0a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" fill="currentColor"/><path d="M4 11l2-6h12l2 6v8a1 1 0 01-1 1h-1a1 1 0 01-1-1v-1H7v1a1 1 0 01-1 1H5a1 1 0 01-1-1v-8z" stroke="currentColor" stroke-width="2" fill="none"/><path d="M7 8h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
                            ];
                            $iconPath = $icons[$category->name] ?? '<path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>';
                        @endphp
                        <svg class="w-8 h-8" style="color:#2980B9;" fill="currentColor" viewBox="0 0 24 24">
                            {!! $iconPath !!}
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900 text-center">{{ $category->name }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Craftsmen Listing -->
<section class="py-16" style="background-color: #ECF0F1;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-extrabold" style="font-family:'Rubik',sans-serif; color:#2C3E50;">
                    @if(request()->hasAny(['category_id', 'location_id', 'search', 'min_rating', 'verified', 'featured', 'has_gallery']))
                        Rezultate căutare
                    @else
                        Meseriași Recomandați
                    @endif
                </h2>
                <p class="mt-1" style="color:#7f8c8d;" id="results-count">{{ $craftsmen->total() }} meseriași găsiți</p>
            </div>
            
            <!-- View Toggle & Sort -->
            <div class="flex items-center gap-4">
                <!-- View Toggle (List/Map) -->
                <div class="flex items-center bg-white rounded-lg border border-gray-300 p-1">
                    <button type="button" id="view-list" class="px-3 py-1.5 rounded text-sm font-medium text-white transition" style="background-color:#C0392B;" title="Vedere listă">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                    </button>
                    <button type="button" id="view-map" class="px-3 py-1.5 rounded text-sm font-medium text-gray-600 hover:bg-gray-100 transition" title="Vedere hartă">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                    </button>
                </div>
                
                <!-- Compare Button -->
                <button type="button" id="compare-btn" class="hidden items-center px-3 py-2 bg-primary-100 text-primary-700 rounded-lg text-sm font-medium hover:bg-primary-200 transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Compară (<span id="compare-count">0</span>)
                </button>
                
                <!-- Quick sort (mobile friendly) -->
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">Sortare:</span>
                    <select id="quickSort" class="px-3 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-secondary-500 focus:border-transparent text-gray-900 text-sm">
                        <option value="recommended" {{ request('sort', 'recommended') == 'recommended' ? 'selected' : '' }}>Recomandate</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Rating</option>
                        <option value="reviews" {{ request('sort') == 'reviews' ? 'selected' : '' }}>Recenzii</option>
                        <option value="distance" {{ request('sort') == 'distance' ? 'selected' : '' }}>Distanță</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Active Filters Display -->
        @if(request()->hasAny(['min_rating', 'verified', 'featured', 'has_gallery', 'has_reviews', 'min_experience', 'available_weekends', 'emergency_services', 'has_insurance']))
        <div class="flex flex-wrap gap-2 mb-6">
            @if(request('min_rating'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-primary-100 text-primary-800">
                    Rating {{ request('min_rating') }}+ ⭐
                    <a href="{{ request()->fullUrlWithQuery(['min_rating' => null]) }}" class="ml-2 text-primary-600 hover:text-primary-800">&times;</a>
                </span>
            @endif
            @if(request('verified'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                    Verificat ✓
                    <a href="{{ request()->fullUrlWithQuery(['verified' => null]) }}" class="ml-2 text-blue-600 hover:text-blue-800">&times;</a>
                </span>
            @endif
            @if(request('featured'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800">
                    Recomandat ★
                    <a href="{{ request()->fullUrlWithQuery(['featured' => null]) }}" class="ml-2 text-yellow-600 hover:text-yellow-800">&times;</a>
                </span>
            @endif
            @if(request('has_gallery'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-100 text-purple-800">
                    Cu poze
                    <a href="{{ request()->fullUrlWithQuery(['has_gallery' => null]) }}" class="ml-2 text-purple-600 hover:text-purple-800">&times;</a>
                </span>
            @endif
            @if(request('has_reviews'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                    Cu recenzii
                    <a href="{{ request()->fullUrlWithQuery(['has_reviews' => null]) }}" class="ml-2 text-green-600 hover:text-green-800">&times;</a>
                </span>
            @endif
            @if(request('min_experience'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-800">
                    {{ request('min_experience') }}+ ani exp.
                    <a href="{{ request()->fullUrlWithQuery(['min_experience' => null]) }}" class="ml-2 text-gray-600 hover:text-gray-800">&times;</a>
                </span>
            @endif
            @if(request('available_weekends'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-orange-100 text-orange-800">
                    Weekend
                    <a href="{{ request()->fullUrlWithQuery(['available_weekends' => null]) }}" class="ml-2 text-orange-600 hover:text-orange-800">&times;</a>
                </span>
            @endif
            @if(request('emergency_services'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-red-100 text-red-800">
                    Urgențe
                    <a href="{{ request()->fullUrlWithQuery(['emergency_services' => null]) }}" class="ml-2 text-red-600 hover:text-red-800">&times;</a>
                </span>
            @endif
        </div>
        @endif

        @if($craftsmen->count() > 0)
            <!-- Map Container (hidden by default) -->
            <div id="craftsmen-map" class="hidden mb-6 h-[500px] rounded-xl overflow-hidden border border-gray-200 shadow-sm"></div>
            
            <!-- Loading Overlay -->
            <div id="loading-overlay" class="hidden fixed inset-0 bg-black/20 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 shadow-xl flex items-center gap-3">
                    <svg class="animate-spin h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-gray-700 font-medium">Se încarcă...</span>
                </div>
            </div>
            
            <div id="craftsmen-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($craftsmen as $craftsman)
                    <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition overflow-hidden relative craftsman-card"
                         data-id="{{ $craftsman->id }}"
                         data-slug="{{ $craftsman->slug }}"
                         data-name="{{ $craftsman->name }}"
                         data-lat="{{ $craftsman->latitude }}"
                         data-lng="{{ $craftsman->longitude }}"
                         data-rating="{{ $craftsman->reviews_avg_rating ?? 0 }}"
                         data-verified="{{ $craftsman->is_verified ? 'true' : 'false' }}"
                         data-featured="{{ $craftsman->is_featured ? 'true' : 'false' }}"
                         data-category="{{ $craftsman->category->name ?? '' }}"
                         data-location="{{ $craftsman->location->city ?? '' }}">
                        <!-- Compare Checkbox -->
                        <label class="absolute top-3 left-3 z-20 cursor-pointer group">
                            <input type="checkbox" 
                                   class="compare-select peer sr-only" 
                                   data-craftsman-id="{{ $craftsman->id }}"
                                   data-craftsman-name="{{ $craftsman->name }}">
                            <span class="flex items-center justify-center w-6 h-6 rounded bg-white/90 border-2 border-gray-300 peer-checked:bg-primary-600 peer-checked:border-primary-600 transition shadow-sm group-hover:border-primary-400">
                                <svg class="w-4 h-4 text-white opacity-0 peer-checked:opacity-100 transition" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                        </label>
                        
                        <!-- Badges -->
                        <div class="absolute top-3 right-3 flex flex-col gap-1 z-10">
                            @if($craftsman->is_featured)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    Top
                                </span>
                            @endif
                            @if($craftsman->is_verified)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background-color:#e8f8ef; color:#27AE60;">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Verificat
                                </span>
                            @endif
                            @if($craftsman->isPro())
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                    ⭐ Pro
                                </span>
                            @elseif($craftsman->isStarter())
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">
                                    Starter
                                </span>
                            @endif
                        </div>

                        <!-- Gallery Preview (if has photos) -->
                        @if($craftsman->gallery->count() > 0)
                            <div class="h-32 bg-gray-100 overflow-hidden">
                                <div class="flex h-full">
                                    @foreach($craftsman->gallery->take(3) as $photo)
                                        <div class="flex-1 {{ !$loop->last ? 'border-r border-white' : '' }}">
                                            <img src="{{ asset('storage/' . $photo->image_path) }}" 
                                                 alt="Lucrare {{ $craftsman->name }}" 
                                                 loading="lazy"
                                                 class="w-full h-full object-cover">
                                        </div>
                                    @endforeach
                                </div>
                                @if($craftsman->gallery_count > 3)
                                    <div class="absolute bottom-32 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded">
                                        +{{ $craftsman->gallery_count - 3 }} poze
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="w-16 h-16 bg-secondary-200 rounded-full flex items-center justify-center text-2xl font-bold text-secondary-700">
                                        {{ strtoupper(substr($craftsman->name, 0, 1)) }}
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="font-bold text-lg text-gray-900">{{ $craftsman->name }}</h3>
                                        <p class="text-sm text-gray-600">{{ $craftsman->category->name ?? 'Meseriaș' }}</p>
                                    </div>
                                </div>
                            </div>

                            @if($craftsman->reviews_count > 0)
                                <div class="flex items-center mb-3">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-5 h-5 {{ $i <= round($craftsman->reviews_avg_rating) ? 'text-accent-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="ml-2 text-sm text-gray-600">{{ number_format($craftsman->reviews_avg_rating, 1) }} ({{ $craftsman->reviews_count }} recenzii)</span>
                                </div>
                            @else
                                <div class="flex items-center mb-3 text-gray-400">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span class="ml-2 text-sm">Fără recenzii încă</span>
                                </div>
                            @endif

                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit(strip_tags($craftsman->description), 120) }}</p>

                            <div class="flex flex-wrap gap-3 text-sm text-gray-500 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $craftsman->location->name ?? 'România' }}
                                </div>

                                @if(isset($craftsman->distance))
                                    <div class="flex items-center text-primary-600 font-medium">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                        </svg>
                                        {{ round($craftsman->distance, 1) }} km
                                    </div>
                                @endif

                                @if($craftsman->experience_years > 0)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $craftsman->experience_years }} ani exp.
                                    </div>
                                @endif

                                @if($craftsman->gallery_count > 0)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $craftsman->gallery_count }} poze
                                    </div>
                                @endif
                            </div>

                            <!-- Quick Tags -->
                            <div class="flex flex-wrap gap-1 mb-4">
                                @if($craftsman->available_weekends)
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded">Weekend</span>
                                @endif
                                @if($craftsman->emergency_services)
                                    <span class="px-2 py-0.5 bg-red-50 text-red-600 text-xs rounded">Urgențe</span>
                                @endif
                                @if($craftsman->has_insurance)
                                    <span class="px-2 py-0.5 bg-green-50 text-green-600 text-xs rounded">Asigurat</span>
                                @endif
                            </div>

                            <a href="{{ route('craftsman.show', $craftsman->slug) }}" class="block w-full text-center bg-primary-600 hover:bg-primary-700 text-white font-semibold px-6 py-3 rounded-xl transition">
                                Vezi Profil
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $craftsmen->links() }}
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-lg">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Nu am găsit meseriași</h3>
                <p class="mt-2 text-gray-500">Încearcă să modifici criteriile de căutare sau să extinzi raza.</p>
                <a href="{{ route('home') }}" class="mt-4 inline-block text-primary-600 hover:text-primary-700 font-medium">
                    Resetează toate filtrele →
                </a>
            </div>
        @endif
    </div>
</section>

<!-- Features Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-extrabold text-center mb-3" style="font-family:'Rubik',sans-serif; color:#2980B9;">De Ce Să Ne Alegi?</h2>
        <p class="text-center mb-12" style="color:#2C3E50;">Platforma ta de încredere pentru servicii profesionale</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-6 rounded-2xl border border-gray-100 hover:shadow-lg transition">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color:#e8f8ef;">
                    <svg class="w-8 h-8" style="color:#27AE60;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2" style="font-family:'Rubik',sans-serif; color:#2C3E50;">Meseriași Verificați</h3>
                <p class="text-gray-600">Toți meseriașii sunt verificați și au recenzii reale de la clienți.</p>
            </div>
            <div class="text-center p-6 rounded-2xl border border-gray-100 hover:shadow-lg transition">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color:#eaf4fb;">
                    <svg class="w-8 h-8" style="color:#2980B9;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2" style="font-family:'Rubik',sans-serif; color:#2C3E50;">Prețuri Transparente</h3>
                <p class="text-gray-600">Fără taxe ascunse. Vezi prețurile înainte de a face programare.</p>
            </div>
            <div class="text-center p-6 rounded-2xl border border-gray-100 hover:shadow-lg transition">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color:#fef9e7;">
                    <svg class="w-8 h-8" style="color:#F1C40F;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2" style="font-family:'Rubik',sans-serif; color:#2C3E50;">Rapid și Ușor</h3>
                <p class="text-gray-600">Găsește și contactează meseriașul potrivit în câteva minute.</p>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle Advanced Filters
    const toggleBtn = document.getElementById('toggleFilters');
    const filtersPanel = document.getElementById('advancedFilters');
    const filterArrow = document.getElementById('filterArrow');
    
    // Check if filters are active and show panel
    const hasActiveFilters = {{ request()->hasAny(['min_rating', 'verified', 'featured', 'has_gallery', 'has_reviews', 'min_experience', 'available_weekends', 'emergency_services', 'has_insurance', 'lat', 'lng']) ? 'true' : 'false' }};
    
    if (hasActiveFilters) {
        filtersPanel.classList.remove('hidden');
        filterArrow.style.transform = 'rotate(180deg)';
    }
    
    toggleBtn.addEventListener('click', function() {
        filtersPanel.classList.toggle('hidden');
        if (filtersPanel.classList.contains('hidden')) {
            filterArrow.style.transform = 'rotate(0deg)';
        } else {
            filterArrow.style.transform = 'rotate(180deg)';
        }
    });
    
    // Geolocation
    const getLocationBtn = document.getElementById('getLocationBtn');
    const locationBtnText = document.getElementById('locationBtnText');
    const latInput = document.getElementById('userLat');
    const lngInput = document.getElementById('userLng');
    
    // Check if location is already set
    if (latInput.value && lngInput.value) {
        locationBtnText.textContent = 'Locație detectată ✓';
        getLocationBtn.classList.add('bg-green-100', 'text-green-700');
        getLocationBtn.classList.remove('bg-secondary-100', 'text-gray-700');
    }
    
    getLocationBtn.addEventListener('click', function() {
        if (!navigator.geolocation) {
            alert('Geolocația nu este suportată de browser-ul tău.');
            return;
        }
        
        locationBtnText.textContent = 'Se detectează...';
        getLocationBtn.disabled = true;
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                latInput.value = position.coords.latitude.toFixed(6);
                lngInput.value = position.coords.longitude.toFixed(6);
                locationBtnText.textContent = 'Locație detectată ✓';
                getLocationBtn.classList.add('bg-green-100', 'text-green-700');
                getLocationBtn.classList.remove('bg-secondary-100', 'text-gray-700');
                getLocationBtn.disabled = false;
                
                // Auto-submit form with location
                // Uncomment if you want auto-submit:
                // document.getElementById('searchForm').submit();
            },
            function(error) {
                let message = 'Nu am putut obține locația.';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        message = 'Permite accesul la locație în browser.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        message = 'Locația nu este disponibilă.';
                        break;
                    case error.TIMEOUT:
                        message = 'Cererea a expirat.';
                        break;
                }
                alert(message);
                locationBtnText.textContent = 'Folosește locația mea';
                getLocationBtn.disabled = false;
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 300000 // 5 minutes cache
            }
        );
    });
    
    // Quick Sort
    const quickSort = document.getElementById('quickSort');
    if (quickSort) {
        quickSort.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', this.value);
            window.location.href = url.toString();
        });
    }
});
</script>
@endpush

@push('scripts')
<script>
(function () {
    let map = null;
    let markers = [];
    let infoWindows = [];
    let userCircle = null;
    let mapInitialized = false;

    function initCraftsmenMap() {
        const mapEl = document.getElementById('craftsmen-map');
        if (!mapEl) return;

        if (!window.google || !window.google.maps) {
            mapEl.innerHTML = '<div class="flex items-center justify-center h-full bg-gray-50 text-gray-400 text-sm">'
                + '<span>Harta nu este disponibilă. Adaugă GOOGLE_MAPS_API_KEY în fișierul .env</span></div>';
            return;
        }

        if (mapInitialized) return;
        mapInitialized = true;

        // Collect craftsmen from DOM data-* attributes
        const cards = document.querySelectorAll('.craftsman-card');
        const craftsmenData = [];
        cards.forEach(function (card) {
            const lat = parseFloat(card.dataset.lat);
            const lng = parseFloat(card.dataset.lng);
            if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
                craftsmenData.push({
                    id:       card.dataset.id,
                    slug:     card.dataset.slug,
                    name:     card.dataset.name,
                    lat,
                    lng,
                    rating:   parseFloat(card.dataset.rating) || 0,
                    verified: card.dataset.verified === 'true',
                    featured: card.dataset.featured === 'true',
                    category: card.dataset.category || '',
                    location: card.dataset.location || '',
                });
            }
        });

        // Determine center & zoom
        const userLat = parseFloat(document.getElementById('userLat').value);
        const userLng = parseFloat(document.getElementById('userLng').value);
        const defaultCenter = { lat: 45.9432, lng: 24.9668 }; // Romania
        let center = defaultCenter;
        let zoom = 7;

        if (!isNaN(userLat) && !isNaN(userLng) && userLat !== 0 && userLng !== 0) {
            center = { lat: userLat, lng: userLng };
            zoom = 11;
        } else if (craftsmenData.length > 0) {
            const avgLat = craftsmenData.reduce(function (s, c) { return s + c.lat; }, 0) / craftsmenData.length;
            const avgLng = craftsmenData.reduce(function (s, c) { return s + c.lng; }, 0) / craftsmenData.length;
            center = { lat: avgLat, lng: avgLng };
            zoom = craftsmenData.length === 1 ? 13 : 8;
        }

        map = new google.maps.Map(mapEl, {
            center,
            zoom,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true,
            zoomControlOptions: { position: google.maps.ControlPosition.RIGHT_CENTER },
        });

        // User location marker + radius circle
        if (!isNaN(userLat) && !isNaN(userLng) && userLat !== 0 && userLng !== 0) {
            new google.maps.Marker({
                position: { lat: userLat, lng: userLng },
                map,
                zIndex: 1000,
                title: 'Locația ta',
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 10,
                    fillColor: '#3B82F6',
                    fillOpacity: 1,
                    strokeColor: '#ffffff',
                    strokeWeight: 2,
                },
            });

            const radiusSelect = document.querySelector('select[name="radius"]');
            const radiusKm = radiusSelect ? parseInt(radiusSelect.value) || 50 : 50;
            userCircle = new google.maps.Circle({
                map,
                center: { lat: userLat, lng: userLng },
                radius: radiusKm * 1000,
                strokeColor: '#3B82F6',
                strokeOpacity: 0.4,
                strokeWeight: 1,
                fillColor: '#3B82F6',
                fillOpacity: 0.06,
            });
        }

        // Drop craftsman markers
        craftsmenData.forEach(function (c) {
            const color = c.featured ? '#D97706' : (c.verified ? '#2563EB' : '#6B7280');
            const marker = new google.maps.Marker({
                position: { lat: c.lat, lng: c.lng },
                map,
                title: c.name,
                animation: google.maps.Animation.DROP,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 9,
                    fillColor: color,
                    fillOpacity: 1,
                    strokeColor: '#ffffff',
                    strokeWeight: 2,
                },
            });

            const stars = '★'.repeat(Math.round(c.rating)) + '☆'.repeat(5 - Math.round(c.rating));
            const ratingHtml = c.rating > 0
                ? '<p style="color:#D97706;font-size:13px;margin:0 0 6px">' + stars + ' ' + c.rating.toFixed(1) + '</p>'
                : '';
            const verifiedBadge = c.verified
                ? '<span style="background:#DBEAFE;color:#1D4ED8;padding:2px 7px;border-radius:9999px;font-size:11px">✓ Verificat</span> '
                : '';
            const featuredBadge = c.featured
                ? '<span style="background:#FEF3C7;color:#92400E;padding:2px 7px;border-radius:9999px;font-size:11px">⭐ Top</span>'
                : '';

            const iw = new google.maps.InfoWindow({
                content:
                    '<div style="min-width:190px;padding:4px 2px">'
                    + '<p style="font-weight:700;font-size:14px;margin:0 0 4px">' + c.name + '</p>'
                    + (c.category ? '<p style="color:#6B7280;font-size:12px;margin:0 0 4px">' + c.category + (c.location ? ' · ' + c.location : '') + '</p>' : '')
                    + ratingHtml
                    + '<div style="margin-bottom:6px">' + verifiedBadge + featuredBadge + '</div>'
                    + '<a href="/meserias/' + c.slug + '" '
                    + 'style="display:block;text-align:center;background:#2563EB;color:#fff;padding:6px 10px;border-radius:6px;font-size:12px;text-decoration:none">'
                    + 'Vezi profil →</a>'
                    + '</div>',
            });

            marker.addListener('click', function () {
                infoWindows.forEach(function (w) { w.close(); });
                iw.open({ anchor: marker, map });
            });

            markers.push(marker);
            infoWindows.push(iw);
        });

        // Legend
        const legend = document.createElement('div');
        legend.style.cssText = 'background:#fff;padding:8px 12px;margin:8px;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.2);font-size:12px;line-height:1.8';
        legend.innerHTML =
            '<b style="display:block;margin-bottom:4px">Legendă</b>'
            + '<span style="color:#D97706">●</span> Promovat &nbsp;'
            + '<span style="color:#2563EB">●</span> Verificat &nbsp;'
            + '<span style="color:#6B7280">●</span> Standard &nbsp;'
            + (document.getElementById('userLat').value ? '<br><span style="color:#3B82F6">●</span> Locația ta' : '');
        map.controls[google.maps.ControlPosition.LEFT_BOTTOM].push(legend);
    }

    // Wire up the existing List/Map toggle buttons
    const viewListBtn = document.getElementById('view-list');
    const viewMapBtn  = document.getElementById('view-map');
    const craftsmenGrid = document.getElementById('craftsmen-grid');
    const craftsmenMapEl = document.getElementById('craftsmen-map');

    if (viewMapBtn && craftsmenMapEl) {
        viewMapBtn.addEventListener('click', function () {
            if (craftsmenGrid)  craftsmenGrid.classList.add('hidden');
            craftsmenMapEl.classList.remove('hidden');
            viewMapBtn.classList.add('bg-primary-600', 'text-white');
            viewMapBtn.classList.remove('text-gray-600', 'hover:bg-gray-100');
            if (viewListBtn) {
                viewListBtn.classList.remove('bg-primary-600', 'text-white');
                viewListBtn.classList.add('text-gray-600', 'hover:bg-gray-100');
            }
            initCraftsmenMap();
        });
    }

    if (viewListBtn && craftsmenGrid) {
        viewListBtn.addEventListener('click', function () {
            craftsmenGrid.classList.remove('hidden');
            if (craftsmenMapEl) craftsmenMapEl.classList.add('hidden');
            viewListBtn.classList.add('bg-primary-600', 'text-white');
            viewListBtn.classList.remove('text-gray-600', 'hover:bg-gray-100');
            if (viewMapBtn) {
                viewMapBtn.classList.remove('bg-primary-600', 'text-white');
                viewMapBtn.classList.add('text-gray-600', 'hover:bg-gray-100');
            }
        });
    }
})();
</script>
@endpush
