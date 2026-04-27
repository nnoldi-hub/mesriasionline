@extends('layouts.app')

@section('title', 'Meseriași Favoriți')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Meseriașii Mei Favoriți</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Meseriașii pe care i-ai salvat pentru acces rapid</p>
    </div>

    @if($favorites->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
            <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nu ai niciun meșter favorit</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Explorează meseriașii și salvează-i pe cei preferați pentru acces rapid.</p>
            <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Caută Meseriași
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($favorites as $favorite)
                @php $craftsman = $favorite->craftsman; @endphp
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden card-hover" id="favorite-{{ $craftsman->id }}">
                    <div class="relative">
                        <img src="{{ $craftsman->profile_image_url ?? asset('images/default-avatar.png') }}" 
                             alt="{{ $craftsman->name }}" 
                             class="w-full h-48 object-cover"
                             loading="lazy">
                        <button onclick="removeFavorite({{ $craftsman->id }})" 
                                class="absolute top-3 right-3 w-10 h-10 bg-white dark:bg-gray-800 rounded-full shadow-lg flex items-center justify-center hover:bg-red-50 transition group">
                            <svg class="w-6 h-6 text-red-500 group-hover:scale-110 transition" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        </button>
                        @if($craftsman->is_verified)
                            <span class="absolute bottom-3 left-3 bg-green-500 text-white text-xs px-2 py-1 rounded-full flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Verificat
                            </span>
                        @endif
                    </div>
                    
                    <div class="p-5">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    <a href="{{ route('craftsman.profile', $craftsman->slug) }}" class="hover:text-primary-600 transition">
                                        {{ $craftsman->name }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $craftsman->category?->name ?? 'Meșter' }} • {{ $craftsman->location?->name }}
                                </p>
                            </div>
                            @if($craftsman->reviews->count() > 0)
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span class="ml-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ number_format($craftsman->reviews->avg('rating'), 1) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        @if($favorite->notes)
                            <div class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    <span class="font-medium">Notă:</span> {{ $favorite->notes }}
                                </p>
                            </div>
                        @endif

                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">
                                Salvat {{ $favorite->created_at->diffForHumans() }}
                            </span>
                            <div class="flex space-x-2">
                                <a href="{{ route('messages.create', ['craftsman' => $craftsman->id]) }}" 
                                   class="inline-flex items-center px-3 py-1.5 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                    </svg>
                                    Mesaj
                                </a>
                                <a href="{{ route('craftsman.profile', $craftsman->slug) }}" 
                                   class="inline-flex items-center px-3 py-1.5 text-sm bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                                    Vezi Profil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $favorites->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
function removeFavorite(craftsmanId) {
    if (!confirm('Sigur vrei să elimini acest meșter din favorite?')) {
        return;
    }

    fetch(`/favorites/${craftsmanId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const card = document.getElementById(`favorite-${craftsmanId}`);
            card.classList.add('fade-out');
            setTimeout(() => card.remove(), 300);
            
            // Check if no more favorites
            const remaining = document.querySelectorAll('[id^="favorite-"]');
            if (remaining.length <= 1) {
                location.reload();
            }
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endpush
@endsection
