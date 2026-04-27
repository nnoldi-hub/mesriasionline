{{-- Dashboard --}}
<a href="{{ route('client.dashboard') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('client.dashboard') ? 'bg-gray-800 text-white border-l-4 border-primary-600' : '' }}">
    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
    </svg>
    Dashboard
</a>

{{-- Secțiunea: Contul Meu --}}
<div x-data="{ open: true }">
    <button @click="open = !open" class="w-full flex items-center justify-between px-6 py-2 mt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-300 transition">
        <span>Contul Meu</span>
        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
        </svg>
    </button>
    <div x-show="open" x-collapse>
        <a href="{{ route('client.profile') }}" class="flex items-center px-6 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('client.profile') ? 'bg-gray-800 text-white border-l-4 border-primary-600' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
            </svg>
            Profilul Meu
        </a>

        <a href="{{ route('client.addresses.index') }}" class="flex items-center px-6 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('client.addresses*') ? 'bg-gray-800 text-white border-l-4 border-primary-600' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
            </svg>
            Adresele Mele
        </a>
    </div>
</div>

{{-- Secțiunea: Servicii & Meseriași --}}
<div x-data="{ open: true }">
    <button @click="open = !open" class="w-full flex items-center justify-between px-6 py-2 mt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-300 transition">
        <span>Servicii & Meseriași</span>
        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
        </svg>
    </button>
    <div x-show="open" x-collapse>
        <a href="{{ route('client.search') }}" class="flex items-center px-6 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('client.search') ? 'bg-gray-800 text-white border-l-4 border-primary-600' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 9a2 2 0 114 0 2 2 0 01-4 0z"/>
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a4 4 0 00-3.446 6.032l-2.261 2.26a1 1 0 101.414 1.415l2.261-2.261A4 4 0 1011 5z" clip-rule="evenodd"/>
            </svg>
            Caută Meseriași
        </a>

        <a href="{{ route('client.quotes.index') }}" class="flex items-center px-6 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('client.quotes*') ? 'bg-gray-800 text-white border-l-4 border-primary-600' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
            </svg>
            Cereri Ofertă
        </a>

        <a href="{{ route('client.appointments.index') }}" class="flex items-center px-6 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('client.appointments*') ? 'bg-gray-800 text-white border-l-4 border-primary-600' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
            </svg>
            Programări
        </a>

        <a href="{{ route('client.reviews.index') }}" class="flex items-center px-6 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('client.reviews*') ? 'bg-gray-800 text-white border-l-4 border-primary-600' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            Recenziile Mele
        </a>
    </div>
</div>

{{-- Secțiunea: Comunicare --}}
<div x-data="{ open: true }">
    <button @click="open = !open" class="w-full flex items-center justify-between px-6 py-2 mt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-300 transition">
        <span>Comunicare</span>
        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
        </svg>
    </button>
    <div x-show="open" x-collapse>
        <a href="{{ route('messages.index') }}" class="flex items-center px-6 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('messages*') ? 'bg-gray-800 text-white border-l-4 border-primary-600' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/>
                <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"/>
            </svg>
            Mesaje
        </a>
    </div>
</div>
