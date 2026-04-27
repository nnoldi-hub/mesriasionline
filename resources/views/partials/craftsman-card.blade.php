<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-700 overflow-hidden">

    {{-- Badges --}}
    <div class="relative">
        @if($craftsman->gallery && $craftsman->gallery->count() > 0)
            <div class="h-28 bg-gray-100 dark:bg-gray-700 overflow-hidden">
                <img src="{{ asset('storage/' . $craftsman->gallery->first()->image_path) }}"
                     alt="Lucrare {{ $craftsman->name }}"
                     class="w-full h-full object-cover">
            </div>
        @endif
        <div class="absolute top-2 right-2 flex flex-col gap-1">
            @if($craftsman->is_featured)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">⭐ Top</span>
            @endif
            @if($craftsman->is_verified)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">✓ Verificat</span>
            @endif
            @if(method_exists($craftsman, 'isPro') && $craftsman->isPro())
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Pro</span>
            @elseif(method_exists($craftsman, 'isStarter') && $craftsman->isStarter())
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">Starter</span>
            @endif
        </div>
    </div>

    <div class="p-4">
        {{-- Avatar + name --}}
        <div class="flex items-center gap-3 mb-3">
            <div class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-xl font-bold text-primary-600 dark:text-primary-300 shrink-0">
                {{ strtoupper(mb_substr($craftsman->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <h3 class="font-bold text-sm text-gray-900 dark:text-white truncate">{{ $craftsman->name }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $craftsman->category->name ?? 'Meseriaș' }}</p>
            </div>
        </div>

        {{-- Rating --}}
        @if(isset($craftsman->reviews_count) && $craftsman->reviews_count > 0)
            <div class="flex items-center gap-1 mb-3">
                <div class="flex">
                    @for($s = 1; $s <= 5; $s++)
                        <svg class="w-3.5 h-3.5 {{ $s <= round($craftsman->reviews_avg_rating ?? 0) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ number_format($craftsman->reviews_avg_rating ?? 0, 1) }}</span>
                <span class="text-xs text-gray-400">({{ $craftsman->reviews_count }})</span>
            </div>
        @endif

        {{-- Location --}}
        @if($craftsman->location)
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-1">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ $craftsman->location->city ?? $craftsman->location->name ?? '' }}
            </p>
        @endif

        {{-- CTA --}}
        <a href="{{ route('craftsman.show', $craftsman->slug) }}"
           class="block w-full text-center bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition-colors">
            Vezi profil
        </a>
    </div>
</div>
