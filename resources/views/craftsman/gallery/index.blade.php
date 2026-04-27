@extends('layouts.craftsman')

@section('title', 'Galerie Lucrări')
@section('page-title', 'Galerie Lucrări')

@section('header-actions')
<a href="{{ route('craftsman.gallery.upload') }}" class="bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2 px-4 rounded-lg inline-flex items-center gap-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
    </svg>
    Încarcă Imagini
</a>
@endsection

@section('content')
    <!-- Category Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-sm font-medium text-gray-700 mr-2">Filtrează după categorie:</span>
            <a href="{{ route('craftsman.gallery') }}" 
               class="px-3 py-1.5 rounded-full text-sm font-medium transition {{ !request('category') ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Toate
            </a>
            @foreach(\App\Models\Gallery::CATEGORIES as $key => $label)
                @if($categories->contains($key))
                    <a href="{{ route('craftsman.gallery', ['category' => $key]) }}" 
                       class="px-3 py-1.5 rounded-full text-sm font-medium transition {{ request('category') == $key ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ $label }}
                        <span class="ml-1 text-xs opacity-75">({{ $categoryCounts[$key] ?? 0 }})</span>
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    @if($images->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-700 mb-2">Nu ai imagini în galerie</h3>
            <p class="text-gray-500 mb-4">Încarcă imagini cu lucrările tale pentru a-ți prezenta portofoliul.</p>
            <a href="{{ route('craftsman.gallery.upload') }}" class="bg-primary hover:bg-primary-dark text-white font-semibold py-2 px-4 rounded-lg inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Încarcă Prima Imagine
            </a>
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($images as $image)
                <div class="bg-white rounded-lg shadow overflow-hidden group relative">
                    <div class="aspect-square relative">
                        <img src="{{ asset('storage/' . $image->image_path) }}" 
                             alt="{{ $image->caption ?: 'Imagine galerie' }}" 
                             class="w-full h-full object-cover">
                        
                        <!-- Featured badge -->
                        @if($image->is_featured)
                            <span class="absolute top-2 left-2 bg-yellow-500 text-white text-xs font-bold px-2 py-1 rounded">
                                ⭐ Featured
                            </span>
                        @endif

                        <!-- Before/After badge -->
                        @if($image->before_after !== 'single')
                            <span class="absolute top-2 right-2 {{ $image->before_after === 'before' ? 'bg-blue-500' : 'bg-green-500' }} text-white text-xs font-bold px-2 py-1 rounded">
                                {{ $image->before_after === 'before' ? 'Înainte' : 'După' }}
                            </span>
                        @endif

                        <!-- Overlay with actions -->
                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                            <a href="{{ route('craftsman.gallery.edit', $image->id) }}" 
                               class="bg-white text-gray-800 p-2 rounded-full hover:bg-gray-100 transition-colors"
                               title="Editează">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            
                            <form action="{{ route('craftsman.gallery.toggle-featured', $image->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="{{ $image->is_featured ? 'bg-yellow-500 text-white' : 'bg-white text-gray-800' }} p-2 rounded-full hover:opacity-80 transition-colors"
                                        title="{{ $image->is_featured ? 'Elimină din favorite' : 'Adaugă la favorite' }}">
                                    <svg class="w-5 h-5" fill="{{ $image->is_featured ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                </button>
                            </form>

                            <form action="{{ route('craftsman.gallery.delete', $image->id) }}" method="POST" class="inline" onsubmit="return confirm('Sigur vrei să ștergi această imagine?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition-colors"
                                        title="Șterge">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    @if($image->caption || $image->service || $image->category)
                        <div class="p-3">
                            @if($image->category)
                                <span class="inline-block px-2 py-0.5 bg-primary-100 text-primary-700 text-xs font-medium rounded mb-1">
                                    {{ $image->category_label }}
                                </span>
                            @endif
                            @if($image->caption)
                                <p class="text-sm text-gray-700 truncate">{{ $image->caption }}</p>
                            @endif
                            @if($image->service)
                                <p class="text-xs text-gray-500 mt-1">{{ $image->service->name }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $images->withQueryString()->links() }}
        </div>
    @endif
@endsection
