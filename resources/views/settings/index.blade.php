@extends('layouts.app')

@section('title', 'Setări')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('messages.settings') }}</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Gestionează setările contului și preferințele tale.
            </p>
        </div>

        <!-- Settings Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Account Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition duration-200">
                <a href="{{ route('craftsman.profile') }}" class="block p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-primary-100 dark:bg-primary-900/50 rounded-lg">
                            <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('messages.profile') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Editează profilul și informațiile personale.
                            </p>
                        </div>
                        <svg class="w-5 h-5 ml-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            </div>

            <!-- Security -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition duration-200">
                <a href="{{ route('security.two-factor') }}" class="block p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-900/50 rounded-lg">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('messages.two_factor_auth') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Configurează autentificarea cu doi factori.
                            </p>
                        </div>
                        <svg class="w-5 h-5 ml-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            </div>

            <!-- Sessions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition duration-200">
                <a href="{{ route('security.sessions') }}" class="block p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('messages.active_sessions') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Gestionează sesiunile active pe dispozitivele tale.
                            </p>
                        </div>
                        <svg class="w-5 h-5 ml-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            </div>

            <!-- Notifications -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition duration-200">
                <a href="{{ route('notifications.index') }}" class="block p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('messages.notifications') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Configurează notificările și alertele.
                            </p>
                        </div>
                        <svg class="w-5 h-5 ml-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            </div>

            <!-- Favorites -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition duration-200">
                <a href="{{ route('favorites.index') }}" class="block p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-red-100 dark:bg-red-900/50 rounded-lg">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('messages.favorites') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Vezi meseriașii salvați în favorite.
                            </p>
                        </div>
                        <svg class="w-5 h-5 ml-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            </div>

            <!-- Search History -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition duration-200">
                <a href="{{ route('search.history') }}" class="block p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-purple-100 dark:bg-purple-900/50 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Istoric Căutări</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Vezi și gestionează istoricul căutărilor.
                            </p>
                        </div>
                        <svg class="w-5 h-5 ml-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            </div>
        </div>

        <!-- Theme Settings -->
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Preferințe</h2>
            </div>
            <div class="p-6 space-y-6">
                <!-- Dark Mode Toggle -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-gray-900 dark:text-white font-medium">Mod întunecat</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Activează tema întunecată pentru o experiență mai ușoară pentru ochi.</p>
                        </div>
                    </div>
                    <button type="button" 
                            onclick="window.darkMode?.toggle()"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                            :class="{ 'bg-primary-600': document.documentElement.classList.contains('dark'), 'bg-gray-200': !document.documentElement.classList.contains('dark') }"
                            x-data
                            x-init="$el.classList.toggle('bg-primary-600', document.documentElement.classList.contains('dark'))"
                            role="switch"
                            aria-checked="false">
                        <span class="sr-only">Mod întunecat</span>
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                              :class="{ 'translate-x-5': document.documentElement.classList.contains('dark'), 'translate-x-0': !document.documentElement.classList.contains('dark') }"></span>
                    </button>
                </div>

                <!-- Language Selector -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-gray-900 dark:text-white font-medium">Limbă</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Selectează limba preferată pentru interfață.</p>
                        </div>
                    </div>
                    <x-language-switcher />
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden border border-red-200 dark:border-red-900">
            <div class="px-6 py-4 border-b border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-900/20">
                <h2 class="text-lg font-semibold text-red-700 dark:text-red-400">Zonă Periculoasă</h2>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-gray-900 dark:text-white font-medium">{{ __('messages.delete_account') }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Odată șters, contul nu poate fi recuperat. Toate datele vor fi șterse permanent.</p>
                    </div>
                    <button type="button" 
                            onclick="confirm('Ești absolut sigur că vrei să ștergi contul? Această acțiune este ireversibilă!') && document.getElementById('delete-account-form').submit()"
                            class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Șterge contul
                    </button>
                    <form id="delete-account-form" method="POST" action="#" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
