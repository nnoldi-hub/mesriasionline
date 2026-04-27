<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white shrink-0 hidden md:flex md:flex-col">
            <div class="p-6 shrink-0">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('images/logo-white.png') }}" alt="Fixacasa PRO" class="h-10" onerror="this.src='{{ asset('images/logo.png') }}';this.onerror=function(){this.style.display='none';this.nextElementSibling.style.display='flex';}">
                    <div class="hidden items-center space-x-2">
                        <svg class="w-8 h-8 text-primary-500" viewBox="0 0 100 100" fill="currentColor">
                            <path d="M50 5 L15 35 L15 55 L25 55 L25 40 L50 20 L75 40 L75 55 L85 55 L85 35 Z"/>
                            <rect x="35" y="45" width="30" height="25" rx="2"/>
                            <circle cx="50" cy="55" r="4"/>
                            <rect x="47" y="75" width="6" height="20"/>
                            <rect x="42" y="85" width="16" height="4" rx="2"/>
                        </svg>
                        <span class="text-xl font-bold text-white">Fixacasa</span>
                    </div>
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto pb-4 scrollbar-thin scrollbar-thumb-gray-700 scrollbar-track-transparent">
                @yield('sidebar')
            </nav>

            <div class="shrink-0 p-4 border-t border-gray-800">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-10 h-10 bg-primary-500 rounded-full flex items-center justify-center font-bold shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg transition">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Ieșire
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm z-10">
                <div class="px-6 py-4 flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                    
                    <div class="flex items-center space-x-4">
                        @yield('header-actions')
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-auto bg-gray-100 p-6">
                @if(session('success'))
                    <div class="mb-6 bg-success-50 border border-success-200 text-success-700 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-error-50 border border-error-200 text-error-700 px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
