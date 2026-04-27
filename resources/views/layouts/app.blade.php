<!DOCTYPE html>
<html lang="ro" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', app(\App\Services\SeoService::class)->getTitle())</title>
    
    {{-- PWA Manifest --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#C0392B">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Omul Potrivit">
    
    {{-- WebPush VAPID Key --}}
    @if(config('webpush.vapid.public_key'))
        <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}">
    @endif
    
    {{-- User Authentication Status --}}
    <meta name="user-authenticated" content="{{ auth()->check() ? 'true' : 'false' }}">
    
    {{-- Dark Mode Script (prevent flash) --}}
    <script>
        (function() {
            const saved = localStorage.getItem('omulpotrivit_dark_mode');
            if (saved === 'true' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>>
    
    {{-- Dynamic SEO Meta Tags --}}
    @if(View::hasSection('seo'))
        @yield('seo')
    @else
        {!! app(\App\Services\SeoService::class)->render() !!}
    @endif
    
    {{-- Favicon --}}
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    
    {{-- Additional head content --}}
    @yield('head')
    
    {{-- Google Maps API --}}
    @if(config('services.google.maps_api_key'))
        <script>
            window.GOOGLE_MAPS_API_KEY = '{{ config('services.google.maps_api_key') }}';
        </script>
        <script async defer 
                src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&language=ro&region=RO"></script>
    @endif
    
    {{-- Google Fonts: Rubik (titluri) + Nunito (texte) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;600;700;800&family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-beige-100" style="font-family: 'Nunito', sans-serif;">
        <!-- Cookies Banner -->
        <div id="cookie-banner" class="fixed bottom-0 left-0 w-full bg-gray-900 text-white py-4 px-6 flex items-center justify-between z-50" style="display:none;">
            <div>
                Folosim cookies pentru a îmbunătăți experiența pe Omul Potrivit. Prin continuarea navigării, ești de acord cu <a href="{{ route('privacy') }}" class="underline text-primary-300">politica de confidențialitate</a>.
            </div>
            <button id="accept-cookies" class="bg-primary-600 text-white px-4 py-2 rounded-lg ml-4 hover:bg-primary-700 transition">Accept</button>
        </div>
    <script>
        // Simple cookie consent logic
        document.addEventListener('DOMContentLoaded', function() {
            if (!localStorage.getItem('omulpotrivit_cookies_accepted')) {
                document.getElementById('cookie-banner').style.display = 'flex';
            }
            document.getElementById('accept-cookies').onclick = function() {
                localStorage.setItem('omulpotrivit_cookies_accepted', 'yes');
                document.getElementById('cookie-banner').style.display = 'none';
            };
        });
    </script>
    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <img src="{{ asset('images/logo.png') }}" alt="Omul Potrivit PRO" class="h-10" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <div class="hidden items-center space-x-2">
                            <svg class="w-8 h-8 text-primary-600" viewBox="0 0 100 100" fill="currentColor">
                                <path d="M50 5 L15 35 L15 55 L25 55 L25 40 L50 20 L75 40 L75 55 L85 55 L85 35 Z"/>
                                <rect x="35" y="45" width="30" height="25" rx="2"/>
                                <circle cx="50" cy="55" r="4"/>
                                <rect x="47" y="75" width="6" height="20"/>
                                <rect x="42" y="85" width="16" height="4" rx="2"/>
                            </svg>
                            <span class="text-xl font-bold text-primary-700">Omul Potrivit</span>
                        </div>
                    </a>
                    
                    {{-- Mobile Hamburger Menu Button --}}
                    <button type="button" id="mobile-menu-btn" class="mobile-menu-btn ml-4 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400" aria-label="Meniu mobil">
                        <div class="hamburger-icon">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </button>
                </div>
                
                <nav class="hidden md:flex space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-secondary-600 font-medium transition">Acasă</a>
                    <a href="{{ route('home') }}#categories" class="text-gray-700 hover:text-secondary-600 font-medium transition">Categorii</a>
                    <a href="{{ route('articole') }}" class="text-gray-700 hover:text-secondary-600 font-medium transition">Articole</a>
                    <a href="{{ route('intrebari') }}" class="text-gray-700 hover:text-secondary-600 font-medium transition">Întrebări</a>
                    <a href="{{ route('about') }}" class="text-gray-700 hover:text-secondary-600 font-medium transition">Despre</a>
                    <a href="{{ route('contact') }}" class="text-gray-700 hover:text-secondary-600 font-medium transition">Contact</a>
                </nav>

                <div class="flex items-center space-x-4">
                    {{-- Dark Mode Toggle --}}
                    <button data-dark-mode-toggle 
                            class="relative text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition p-2"
                            aria-label="Toggle dark mode">
                        {{-- Sun icon (shown in dark mode) --}}
                        <svg class="w-6 h-6 moon-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        {{-- Moon icon (shown in light mode) --}}
                        <svg class="w-6 h-6 sun-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </button>
                    
                    @auth
                        {{-- Favorites Link --}}
                        <a href="{{ route('favorites.index') }}" class="relative text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition p-2" title="Favoriți">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </a>
                    
                        <!-- Notifications Bell -->
                        <div class="relative" x-data="{ open: false, loaded: false }" @click.away="open = false">
                            <button @click="open = !open; if(!loaded) { loadNotifications(); loaded = true; }" class="relative text-gray-600 hover:text-primary-600 transition p-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <span id="notification-badge" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                            </button>
                            <div x-show="open" x-cloak class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border z-50">
                                <div class="p-3 border-b flex justify-between items-center">
                                    <span class="font-semibold text-gray-900">Notificări</span>
                                    <a href="{{ route('notifications.index') }}" class="text-sm text-primary-600 hover:underline">Vezi toate</a>
                                </div>
                                <div id="notifications-dropdown" class="max-h-64 overflow-y-auto">
                                    <p class="p-4 text-sm text-gray-500 text-center">Se încarcă...</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Messages Icon -->
                        <a href="{{ route('messages.index') }}" class="relative text-gray-600 hover:text-primary-600 transition p-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            <span id="messages-badge" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                        </a>
                    
                        @if(auth()->user()->role === 'superadmin' || auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-primary-600 transition">
                                Dashboard Admin
                            </a>
                        @elseif(auth()->user()->role === 'specialist')
                            <a href="{{ route('craftsman.dashboard') }}" class="text-gray-700 hover:text-primary-600 transition">
                                Dashboard
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-primary-600 transition">
                                Deconectare
                            </button>
                        </form>
                    @else
                        <a href="{{ route('register.client.form') }}" class="text-gray-700 hover:text-secondary-600 transition font-medium">
                            Creează Cont
                        </a>
                        <a href="{{ route('register') }}" class="text-secondary-600 hover:text-secondary-700 transition font-semibold">
                            Devino Meserias
                        </a>
                        <a href="{{ route('login') }}" class="bg-primary-600 text-white px-5 py-2 rounded-xl hover:bg-primary-700 transition shadow-md font-semibold">
                            Intră în cont
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-20" style="background-color: #2C3E50; color: #fff;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <div>
                    <h3 class="text-xl font-bold mb-4" style="font-family: 'Rubik', sans-serif;">Omul Potrivit</h3>
                    <p class="text-sm leading-relaxed" style="color: #b0bec5;">
                        Reparații, întreținere, siguranță — totul pentru casa ta.<br>
                        <span class="font-semibold" style="color: #F1C40F;">Meseriașul potrivit, direct la tine.</span>
                    </p>
                    <!-- Social icons -->
                    <div class="flex items-center gap-3 mt-4">
                        <a href="#" class="w-9 h-9 rounded-full flex items-center justify-center transition hover:opacity-80" style="background-color: #3b5998;" title="Facebook">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-full flex items-center justify-center transition hover:opacity-80" style="background-color: #e1306c;" title="Instagram">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5" stroke-width="2"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z" stroke-width="2"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5" stroke-width="2" stroke-linecap="round"/></svg>
                        </a>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold mb-5 text-base" style="font-family: 'Rubik', sans-serif; color: #ECF0F1;">Servicii</h4>
                    <ul class="space-y-3 text-sm" style="color: #b0bec5;">
                        <li><a href="{{ route('home', ['category_id' => 1]) }}" class="hover:text-white transition">⚡ Electricieni</a></li>
                        <li><a href="{{ route('home', ['category_id' => 2]) }}" class="hover:text-white transition">🔧 Instalatori</a></li>
                        <li><a href="{{ route('home', ['category_id' => 3]) }}" class="hover:text-white transition">🪚 Tâmplari</a></li>
                        <li><a href="{{ route('home', ['category_id' => 4]) }}" class="hover:text-white transition">🖌️ Zugravi</a></li>
                        <li><a href="{{ route('service.request') }}" class="hover:text-white transition">🏠 Întreținere imobile</a></li>
                        <li><a href="{{ route('service.request') }}" class="hover:text-white transition">🔨 Mentenanță</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-5 text-base" style="font-family: 'Rubik', sans-serif; color: #ECF0F1;">Despre noi</h4>
                    <ul class="space-y-3 text-sm" style="color: #b0bec5;">
                        <li><a href="{{ route('about') }}" class="hover:text-white transition">Despre Omul Potrivit</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition">Contact</a></li>
                        <li><a href="{{ route('terms') }}" class="hover:text-white transition">Termeni și condiții</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-white transition">Politică de confidențialitate</a></li>
                        <li><a href="{{ route('cookies') }}" class="hover:text-white transition">Politica Cookies</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-5 text-base" style="font-family: 'Rubik', sans-serif; color: #ECF0F1;">Contact</h4>
                    <ul class="space-y-3 text-sm" style="color: #b0bec5;">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            contact@meseriasionline.ro
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            +40 123 456 789
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            L-V, 9:00 – 18:00
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 pt-8 text-center text-sm" style="border-top: 1px solid #3d5166; color: #78909c;">
                <p>&copy; {{ date('Y') }} Omul Potrivit. Toate drepturile rezervate.</p>
            </div>
        </div>
    </footer>
    
    @stack('scripts')
    
    @auth
    <script>
        // Load notification count on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadNotificationCount();
            loadUnreadMessagesCount();
        });

        function loadNotificationCount() {
            fetch('{{ route("notifications.unread-count") }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notification-badge');
                    if (data.count > 0) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                })
                .catch(error => console.error('Error loading notifications count:', error));
        }

        function loadNotifications() {
            const dropdown = document.getElementById('notifications-dropdown');
            
            fetch('{{ route("notifications.recent") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.notifications && data.notifications.length > 0) {
                        let html = '';
                        data.notifications.forEach(notification => {
                            const isUnread = !notification.read_at;
                            html += `
                                <a href="${notification.url || '#'}" class="block px-4 py-3 hover:bg-gray-50 border-b ${isUnread ? 'bg-blue-50' : ''}">
                                    <p class="text-sm text-gray-900 ${isUnread ? 'font-medium' : ''}">${notification.title || 'Notificare nouă'}</p>
                                    <p class="text-xs text-gray-500 mt-1">${notification.message || ''}</p>
                                    <p class="text-xs text-gray-400 mt-1">${notification.time_ago || ''}</p>
                                </a>
                            `;
                        });
                        dropdown.innerHTML = html;
                    } else {
                        dropdown.innerHTML = '<p class="p-4 text-sm text-gray-500 text-center">Nu ai notificări noi.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    dropdown.innerHTML = '<p class="p-4 text-sm text-red-500 text-center">Eroare la încărcare.</p>';
                });
        }

        function loadUnreadMessagesCount() {
            fetch('{{ route("messages.unread-count") }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('messages-badge');
                    if (badge && data.count > 0) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                        badge.classList.remove('hidden');
                    } else if (badge) {
                        badge.classList.add('hidden');
                    }
                })
                .catch(error => console.error('Error loading messages count:', error));
        }
    </script>
    @endauth
    
    {{-- Bottom Navigation Bar (Mobile) --}}
    <nav class="bottom-nav md:hidden">
        <a href="{{ route('home') }}" class="bottom-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Acasă</span>
        </a>
        <a href="{{ route('home') }}#categories" class="bottom-nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            <span>Categorii</span>
        </a>
        @auth
        <a href="{{ route('messages.index') }}" class="bottom-nav-item {{ request()->routeIs('messages.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
            <span>Mesaje</span>
            <span id="bottom-messages-badge" class="badge hidden">0</span>
        </a>
        <a href="{{ route('favorites.index') }}" class="bottom-nav-item {{ request()->routeIs('favorites.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <span>Favorite</span>
        </a>
        @else
        <a href="{{ route('login') }}" class="bottom-nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            <span>Autentificare</span>
        </a>
        <a href="{{ route('register') }}" class="bottom-nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            <span>Înscrie-te</span>
        </a>
        @endauth
        <a href="{{ route('articole') }}" class="bottom-nav-item {{ request()->routeIs('articole*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
            </svg>
            <span>Articole</span>
        </a>
    </nav>
</body>
</html>
