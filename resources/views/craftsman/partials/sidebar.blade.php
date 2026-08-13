{{-- Dashboard --}}
<a href="{{ route('craftsman.dashboard') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('craftsman.dashboard') ? 'bg-gray-800 text-white border-l-4 border-primary-600' : '' }}">
    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
    </svg>
    Dashboard
</a>

{{-- Secțiunea: Profil & Servicii --}}
<div x-data="{ open: true }">
    <button @click="open = !open" class="w-full flex items-center justify-between px-6 py-2 mt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-300 transition">
        <span>Profil & Servicii</span>
        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
        </svg>
    </button>
    <div x-show="open" x-collapse>
        <a href="{{ route('craftsman.profile') }}" class="flex items-center px-6 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('craftsman.profile') ? 'bg-gray-800 text-white border-l-4 border-primary-600' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
            </svg>
            Profilul Meu
        </a>

        <a href="{{ route('craftsman.services') }}" class="flex items-center px-6 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('craftsman.services*') ? 'bg-gray-800 text-white border-l-4 border-primary-600' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
            </svg>
            Serviciile Mele
        </a>

        <a href="{{ route('craftsman.gallery') }}" class="flex items-center px-6 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('craftsman.gallery*') ? 'bg-gray-800 text-white border-l-4 border-primary-600' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
            </svg>
            Galerie Lucrări
        </a>

        <a href="{{ route('craftsman.social-media') }}" class="flex items-center px-6 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('craftsman.social-media*') ? 'bg-gray-800 text-white border-l-4 border-primary-600' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"/>
            </svg>
            Social Media
        </a>

        <a href="{{ route('craftsman.certifications.index') }}" class="flex items-center px-6 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('craftsman.certifications*') ? 'bg-gray-800 text-white border-l-4 border-primary-600' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            Certificări
        </a>
    </div>
</div>

{{-- Secțiunea: Programări & Clienți --}}
<div x-data="{ open: true }">
    <button @click="open = !open" class="w-full flex items-center justify-between px-6 py-2 mt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-300 transition">
        <span>Programări & Clienți</span>
        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
        </svg>
    </button>
    <div x-show="open" x-collapse>
        <a href="{{ route('craftsman.appointments') }}" class="flex items-center px-6 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('craftsman.appointments*') ? 'bg-gray-800 text-white border-l-4 border-primary-600' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
            </svg>
            Programări
        </a>

        <a href="{{ route('craftsman.availability') }}" class="flex items-center px-6 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('craftsman.availability') ? 'bg-gray-800 text-white border-l-4 border-primary-600' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
            </svg>
            Disponibilitate
        </a>

        <a href="{{ route('craftsman.calendar.integration') }}" class="flex items-center px-6 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('craftsman.calendar.*') ? 'bg-gray-800 text-white border-l-4 border-primary-600' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 10-2 0v1H4zm0 4h12v8H4V8z"/>
            </svg>
            Integrare Calendar
        </a>

        <a href="{{ route('craftsman.quotes.index') }}" class="flex items-center px-6 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('craftsman.quotes*') ? 'bg-gray-800 text-white border-l-4 border-primary-600' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
            </svg>
            Cereri Ofertă
            @php
                $pendingQuoteRequests = \App\Models\QuoteRequest::where('craftsman_id', auth()->id())
                    ->where('status', 'pending')
                    ->whereDoesntHave('quotes', fn($q) => $q->where('craftsman_id', auth()->id()))
                    ->count();
            @endphp
            @if($pendingQuoteRequests > 0)
                <span class="ml-auto bg-yellow-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingQuoteRequests }}</span>
            @endif
        </a>

        <a href="{{ route('craftsman.public-requests.index') }}" class="flex items-center px-6 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('craftsman.public-requests*') ? 'bg-gray-800 text-white border-l-4 border-primary-600' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
            </svg>
            Cereri Publice
            @php
                $newPublicRequests = \App\Models\PublicJobRequest::where('status', 'open')
                    ->where('category_id', auth()->user()->category_id)
                    ->where(function($q) {
                        $q->whereNull('location_id')->orWhere('location_id', auth()->user()->location_id);
                    })
                    ->whereDoesntHave('responses', fn($q) => $q->where('craftsman_id', auth()->id()))
                    ->count();
            @endphp
            @if($newPublicRequests > 0)
                <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $newPublicRequests }}</span>
            @endif
        </a>

        <a href="{{ route('craftsman.reviews') }}" class="flex items-center px-6 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('craftsman.reviews*') ? 'bg-gray-800 text-white border-l-4 border-primary-600' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            Recenzii
        </a>

        <a href="{{ route('craftsman.referrals.index') }}" class="flex items-center px-6 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('craftsman.referrals*') ? 'bg-gray-800 text-white border-l-4 border-primary-600' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Recomandă un coleg
        </a>

        <a href="{{ route('messages.index') }}" class="flex items-center px-6 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('messages*') ? 'bg-gray-800 text-white border-l-4 border-primary-600' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/>
                <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"/>
            </svg>
            Mesaje
            @php
                $unreadMessages = \App\Models\Conversation::where('craftsman_id', auth()->id())
                    ->whereHas('messages', fn($q) => $q->where('sender_id', '!=', auth()->id())->whereNull('read_at'))
                    ->count();
            @endphp
            @if($unreadMessages > 0)
                <span class="ml-auto bg-primary-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $unreadMessages }}</span>
            @endif
        </a>
    </div>
</div>

{{-- Secțiunea: Statistici & Altele --}}
<div x-data="{ open: true }">
    <button @click="open = !open" class="w-full flex items-center justify-between px-6 py-2 mt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-300 transition">
        <span>Statistici & Altele</span>
        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
        </svg>
    </button>
    <div x-show="open" x-collapse>
        <a href="{{ route('craftsman.analytics') }}" class="flex items-center px-6 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('craftsman.analytics*') ? 'bg-gray-800 text-white border-l-4 border-primary-600' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
            </svg>
            Statistici
        </a>

        <a href="{{ route('craftsman.show', auth()->user()->slug) }}" target="_blank" class="flex items-center px-6 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"/>
                <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z"/>
            </svg>
            Vezi Profil Public
        </a>
    </div>
</div>
