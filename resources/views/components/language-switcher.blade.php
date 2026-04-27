{{-- Language Switcher Component --}}
<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" 
            @click.away="open = false"
            type="button"
            class="flex items-center space-x-2 px-3 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-200"
            aria-haspopup="true"
            :aria-expanded="open">
        <span class="text-lg">{{ $supportedLocales[$currentLocale]['flag'] ?? '🌐' }}</span>
        <span class="hidden sm:inline text-sm font-medium">{{ $supportedLocales[$currentLocale]['native'] ?? 'Language' }}</span>
        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg ring-1 ring-black ring-opacity-5 z-50 overflow-hidden"
         style="display: none;">
        <div class="py-1">
            @foreach($supportedLocales as $code => $locale)
                <a href="{{ route('locale.switch', $code) }}" 
                   class="flex items-center px-4 py-3 text-sm {{ $currentLocale === $code ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' }} transition duration-150">
                    <span class="text-lg mr-3">{{ $locale['flag'] }}</span>
                    <span class="font-medium">{{ $locale['native'] }}</span>
                    @if($currentLocale === $code)
                        <svg class="w-4 h-4 ml-auto text-primary-600 dark:text-primary-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>
