@extends('layouts.app')

@section('title', $craftsman->name . ' - ' . ($craftsman->category->name ?? 'Meseriaș'))

@section('content')
<div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Back Button -->
        <a href="{{ route('home') }}" class="inline-flex items-center text-gray-600 hover:text-primary-600 mb-6">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Înapoi la listă
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Profile Header -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start flex-1">
                            <div class="w-24 h-24 bg-secondary-200 rounded-full flex items-center justify-center text-4xl font-bold text-primary-600">
                                {{ strtoupper(substr($craftsman->name, 0, 1)) }}
                            </div>
                            <div class="ml-6 flex-1">
                                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $craftsman->name }}</h1>
                                <p class="text-xl text-gray-600 mb-1">{{ $craftsman->category->name ?? 'Meseriaș' }}</p>
                                @if($craftsman->isPro())
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 mb-2">⭐ Pro</span>
                                @elseif($craftsman->isStarter())
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 mb-2">Starter</span>
                                @endif
                                
                                @if($craftsman->reviews_count > 0)
                                    <div class="flex items-center mb-3">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-6 h-6 {{ $i <= round($craftsman->reviews_avg_rating) ? 'text-accent-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="ml-2 text-lg font-semibold">{{ number_format($craftsman->reviews_avg_rating, 1) }}</span>
                                        <span class="ml-2 text-gray-600">({{ $craftsman->reviews_count }} recenzii)</span>
                                    </div>
                                @endif

                                <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                    @if($craftsman->location)
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $craftsman->location->name }}
                                        </div>
                                    @endif
                                    
                                    @if($craftsman->experience_years > 0)
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $craftsman->experience_years }} ani experiență
                                        </div>
                                    @endif

                                    @if($craftsman->has_insurance)
                                        <div class="flex items-center text-success-600">
                                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Asigurat profesional
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Social Share & Favorite Buttons -->
                        <div class="ml-4 flex flex-col gap-2">
                            @auth
                                <!-- Favorite Button -->
                                <button onclick="toggleFavorite({{ $craftsman->id }})" 
                                        id="favorite-btn-{{ $craftsman->id }}"
                                        class="p-2 rounded-full hover:bg-gray-100 transition"
                                        title="Adaugă la favorite">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </button>
                            @endauth

                            <!-- Share Dropdown -->
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" 
                                        class="p-2 rounded-full hover:bg-gray-100 transition"
                                        title="Distribuie profil">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                    </svg>
                                </button>

                                <!-- Share Dropdown Menu -->
                                <div x-show="open" 
                                     @click.away="open = false"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl z-10 border border-gray-200">
                                    <div class="py-2">
                                        <!-- Facebook Share -->
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('craftsman.profile', $craftsman->slug)) }}" 
                                           target="_blank"
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-5 h-5 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                            </svg>
                                            Facebook
                                        </a>

                                        <!-- WhatsApp Share -->
                                        <a href="https://wa.me/?text={{ urlencode($craftsman->name . ' - ' . route('craftsman.profile', $craftsman->slug)) }}" 
                                           target="_blank"
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-5 h-5 mr-3 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                            </svg>
                                            WhatsApp
                                        </a>

                                        <!-- Twitter Share -->
                                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($craftsman->name) }}&url={{ urlencode(route('craftsman.profile', $craftsman->slug)) }}" 
                                           target="_blank"
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-5 h-5 mr-3 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                            </svg>
                                            Twitter
                                        </a>

                                        <!-- Copy Link -->
                                        <button onclick="copyProfileLink()" 
                                                class="w-full flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-5 h-5 mr-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                            Copiază link
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                @if($craftsman->description)
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-2xl font-bold mb-4">Despre</h2>
                        <p class="text-gray-700 leading-relaxed">{{ $craftsman->description }}</p>
                    </div>
                @endif

                <!-- Services -->
                @if($craftsman->services->count() > 0)
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-2xl font-bold mb-4">Servicii Oferite</h2>
                        <div class="space-y-4">
                            @foreach($craftsman->services as $service)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-orange-300 transition">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="font-semibold text-lg">{{ $service->name }}</h3>
                                        @if($service->price)
                                            <span class="text-primary-600 font-bold text-lg">{{ number_format($service->price, 0) }} RON</span>
                                        @elseif($service->min_price && $service->max_price)
                                            <span class="text-primary-600 font-bold text-lg">{{ number_format($service->min_price, 0) }} - {{ number_format($service->max_price, 0) }} RON</span>
                                        @endif
                                    </div>
                                    <p class="text-gray-600 text-sm mb-2">{{ $service->description }}</p>
                                    <div class="flex items-center text-sm text-gray-500">
                                        @if($service->duration)
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>{{ $service->duration }} min</span>
                                        @endif
                                        @if($service->complexity)
                                            <span class="ml-4 px-2 py-1 bg-gray-100 rounded text-xs">
                                                {{ ucfirst($service->complexity) }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($service->category && in_array($service->category->name, ['Intretinere imobile', 'Mentenanta']))
                                        <button class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition" onclick="window.location.href='{{ route('service.book', ['service' => $service->id]) }}'">Rezervă</button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Gallery -->
                @if($craftsman->gallery && $craftsman->gallery->count() > 0)
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6" x-data="{ activeTab: 'all' }">
                        <h2 class="text-2xl font-bold mb-4">Galerie Lucrări</h2>
                        
                        @php
                            $galleryByCategory = $craftsman->gallery->groupBy('category');
                            $uncategorized = $galleryByCategory->pull('') ?? collect();
                            $uncategorized = $uncategorized->merge($galleryByCategory->pull(null) ?? collect());
                        @endphp
                        
                        <!-- Category Tabs -->
                        @if($galleryByCategory->count() > 0 || $uncategorized->count() > 0)
                            <div class="flex flex-wrap gap-2 mb-4">
                                <button @click="activeTab = 'all'" 
                                        :class="activeTab === 'all' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                        class="px-3 py-1.5 rounded-full text-sm font-medium transition">
                                    Toate ({{ $craftsman->gallery->count() }})
                                </button>
                                @foreach($galleryByCategory as $catKey => $catImages)
                                    <button @click="activeTab = '{{ $catKey }}'" 
                                            :class="activeTab === '{{ $catKey }}' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                            class="px-3 py-1.5 rounded-full text-sm font-medium transition">
                                        {{ \App\Models\Gallery::CATEGORIES[$catKey] ?? $catKey }} ({{ $catImages->count() }})
                                    </button>
                                @endforeach
                                @if($uncategorized->count() > 0)
                                    <button @click="activeTab = 'other'" 
                                            :class="activeTab === 'other' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                            class="px-3 py-1.5 rounded-full text-sm font-medium transition">
                                        Altele ({{ $uncategorized->count() }})
                                    </button>
                                @endif
                            </div>
                        @endif

                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($craftsman->gallery as $image)
                                <div class="aspect-square rounded-lg overflow-hidden relative group cursor-pointer"
                                     x-show="activeTab === 'all' || activeTab === '{{ $image->category ?: 'other' }}'"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     onclick="openLightbox('{{ asset('storage/' . $image->image_path) }}', '{{ $image->caption }}')">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" 
                                         alt="{{ $image->caption ?: 'Lucrare' }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @if($image->category)
                                        <span class="absolute top-2 left-2 bg-primary-600 text-white text-xs font-bold px-2 py-1 rounded">
                                            {{ $image->category_label }}
                                        </span>
                                    @endif
                                    @if($image->before_after !== 'single')
                                        <span class="absolute top-2 right-2 {{ $image->before_after === 'before' ? 'bg-blue-500' : 'bg-green-500' }} text-white text-xs font-bold px-2 py-1 rounded">
                                            {{ $image->before_after === 'before' ? 'Înainte' : 'După' }}
                                        </span>
                                    @endif
                                    @if($image->caption)
                                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <p class="text-white text-sm">{{ $image->caption }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Reviews -->
                @if($craftsman->reviews->count() > 0)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-2xl font-bold mb-4">Recenzii ({{ $craftsman->reviews_count }})</h2>
                        <div class="space-y-6">
                            @foreach($craftsman->reviews as $review)
                                <div class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <h4 class="font-semibold">{{ $review->client_name }}</h4>
                                            <div class="flex items-center mt-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-accent-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                        <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-gray-700 mt-2">{{ $review->comment }}</p>
                                    
                                    @if($review->specialist_response)
                                        <div class="mt-3 ml-6 p-3 bg-gray-50 rounded-lg">
                                            <p class="text-sm font-semibold text-gray-900 mb-1">Răspuns de la meseriaș:</p>
                                            <p class="text-sm text-gray-700">{{ $review->specialist_response }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Contact Card -->
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                    <h3 class="text-xl font-bold mb-4">Contactează-l pe {{ $craftsman->name }}</h3>
                    
                    @if($craftsman->phone)
                        <a href="tel:{{ $craftsman->phone }}" class="block w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold px-6 py-3 rounded-lg transition text-center mb-3">
                            <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                            </svg>
                            Sună Acum
                        </a>
                    @endif
                    
                    <button class="block w-full bg-white hover:bg-gray-50 text-primary-600 font-semibold px-6 py-3 rounded-lg transition border-2 border-orange-600 text-center">
                        Trimite Mesaj
                    </button>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="font-semibold mb-3">Informații suplimentare</h4>
                        <div class="space-y-2 text-sm">
                            @if($craftsman->service_radius_km)
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                    </svg>
                                    Rază servicii: {{ $craftsman->service_radius_km }} km
                                </div>
                            @endif
                            
                            @if($craftsman->available_weekends)
                                <div class="flex items-center text-success-600">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Disponibil weekend
                                </div>
                            @endif

                            @if($craftsman->emergency_services)
                                <div class="flex items-center text-error-400">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    Servicii urgență
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Social Media Links -->
                    @if($craftsman->hasSocialLinks())
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="font-semibold mb-3">Social Media</h4>
                            <div class="flex flex-wrap gap-3">
                                @if($craftsman->facebook_url)
                                    <a href="{{ $craftsman->facebook_url }}" target="_blank" rel="noopener noreferrer" 
                                       class="w-10 h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center transition-colors"
                                       title="Facebook">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                    </a>
                                @endif

                                @if($craftsman->instagram_url)
                                    <a href="{{ $craftsman->instagram_url }}" target="_blank" rel="noopener noreferrer" 
                                       class="w-10 h-10 bg-gradient-to-br from-purple-600 via-pink-500 to-orange-400 hover:opacity-80 text-white rounded-full flex items-center justify-center transition-opacity"
                                       title="Instagram">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                                        </svg>
                                    </a>
                                @endif

                                @if($craftsman->tiktok_url)
                                    <a href="{{ $craftsman->tiktok_url }}" target="_blank" rel="noopener noreferrer" 
                                       class="w-10 h-10 bg-gray-900 hover:bg-gray-700 text-white rounded-full flex items-center justify-center transition-colors"
                                       title="TikTok">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                                        </svg>
                                    </a>
                                @endif

                                @if($craftsman->youtube_url)
                                    <a href="{{ $craftsman->youtube_url }}" target="_blank" rel="noopener noreferrer" 
                                       class="w-10 h-10 bg-red-600 hover:bg-red-700 text-white rounded-full flex items-center justify-center transition-colors"
                                       title="YouTube">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                        </svg>
                                    </a>
                                @endif

                                @if($craftsman->linkedin_url)
                                    <a href="{{ $craftsman->linkedin_url }}" target="_blank" rel="noopener noreferrer" 
                                       class="w-10 h-10 bg-blue-700 hover:bg-blue-800 text-white rounded-full flex items-center justify-center transition-colors"
                                       title="LinkedIn">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                        </svg>
                                    </a>
                                @endif

                                @if($craftsman->whatsapp_number)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $craftsman->whatsapp_number) }}" target="_blank" rel="noopener noreferrer" 
                                       class="w-10 h-10 bg-green-500 hover:bg-green-600 text-white rounded-full flex items-center justify-center transition-colors"
                                       title="WhatsApp">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                        </svg>
                                    </a>
                                @endif

                                @if($craftsman->website_url)
                                    <a href="{{ $craftsman->website_url }}" target="_blank" rel="noopener noreferrer" 
                                       class="w-10 h-10 bg-gray-600 hover:bg-gray-700 text-white rounded-full flex items-center justify-center transition-colors"
                                       title="Website">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lightbox Modal -->
<div id="lightbox" class="fixed inset-0 bg-black/90 z-50 hidden items-center justify-center" onclick="closeLightbox()">
    <button class="absolute top-4 right-4 text-white hover:text-gray-300" onclick="closeLightbox()">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    <div class="max-w-4xl max-h-[90vh] p-4" onclick="event.stopPropagation()">
        <img id="lightbox-image" src="" alt="" class="max-w-full max-h-[80vh] object-contain mx-auto rounded-lg shadow-2xl">
        <p id="lightbox-caption" class="text-white text-center mt-4 text-lg"></p>
    </div>
</div>

<script>
    function openLightbox(imageSrc, caption) {
        const lightbox = document.getElementById('lightbox');
        const lightboxImage = document.getElementById('lightbox-image');
        const lightboxCaption = document.getElementById('lightbox-caption');
        
        lightboxImage.src = imageSrc;
        lightboxCaption.textContent = caption || '';
        lightbox.classList.remove('hidden');
        lightbox.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        const lightbox = document.getElementById('lightbox');
        lightbox.classList.add('hidden');
        lightbox.classList.remove('flex');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLightbox();
        }
    });

    // Toggle favorite
    function toggleFavorite(craftsmanId) {
        fetch('/favorites/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ craftsman_id: craftsmanId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const btn = document.getElementById(`favorite-btn-${craftsmanId}`);
                const svg = btn.querySelector('svg');
                
                if (data.favorited) {
                    svg.setAttribute('fill', 'currentColor');
                    svg.classList.add('text-red-500');
                    showNotification('Adăugat la favorite!', 'success');
                } else {
                    svg.setAttribute('fill', 'none');
                    svg.classList.remove('text-red-500');
                    showNotification('Eliminat din favorite!', 'success');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('A apărut o eroare', 'error');
        });
    }

    // Copy profile link
    function copyProfileLink() {
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
            showNotification('Link copiat în clipboard!', 'success');
        }).catch(err => {
            console.error('Could not copy text: ', err);
            showNotification('Eroare la copierea linkului', 'error');
        });
    }

    // Show notification
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.3s';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Check if already favorited on page load
    @auth
        fetch('/favorites/check/{{ $craftsman->id }}')
            .then(response => response.json())
            .then(data => {
                if (data.favorited) {
                    const btn = document.getElementById('favorite-btn-{{ $craftsman->id }}');
                    if (btn) {
                        const svg = btn.querySelector('svg');
                        svg.setAttribute('fill', 'currentColor');
                        svg.classList.add('text-red-500');
                    }
                }
            });
    @endauth
</script>
@endsection
