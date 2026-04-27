@extends('layouts.app')

@section('title', 'Istoric Căutări')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-sm">
                    <li>
                        <a href="{{ route('home') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Acasă</a>
                    </li>
                    <li>
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </li>
                    <li>
                        <span class="text-gray-700 dark:text-gray-200 font-medium">Istoric Căutări</span>
                    </li>
                </ol>
            </nav>
            
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Istoric Căutări</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Căutările tale recente și căutări populare pe platformă.
                    </p>
                </div>
                
                @if($searches->count() > 0)
                <form method="POST" action="{{ route('search.history.clear') }}" class="shrink-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            onclick="return confirm('Ești sigur că vrei să ștergi tot istoricul de căutări?')"
                            class="inline-flex items-center px-4 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 font-medium rounded-lg transition duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Șterge istoricul
                    </button>
                </form>
                @endif
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <div class="flex">
                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <p class="ml-3 text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content - Search History -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Căutările Tale Recente
                        </h2>
                    </div>
                    
                    @forelse($searches as $search)
                    <div class="px-6 py-4 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }} hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('search', ['q' => $search->query]) }}" 
                                   class="flex items-center group">
                                    <svg class="w-5 h-5 text-gray-400 mr-3 group-hover:text-primary-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-gray-900 dark:text-white font-medium group-hover:text-primary-600 dark:group-hover:text-primary-400 transition truncate">
                                            {{ $search->query }}
                                        </p>
                                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            <span>{{ $search->results_count }} rezultate</span>
                                            <span class="mx-2">•</span>
                                            <span>{{ $search->searched_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            
                            <form method="POST" action="{{ route('search.history.delete', $search) }}" class="ml-4">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="p-2 text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition"
                                        title="Șterge din istoric">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                        
                        @if(!empty($search->filters))
                        <div class="mt-2 flex flex-wrap gap-2 ml-8">
                            @foreach($search->filters as $key => $value)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                {{ ucfirst($key) }}: {{ is_array($value) ? implode(', ', $value) : $value }}
                            </span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Nicio căutare în istoric</h3>
                        <p class="mt-2 text-gray-500 dark:text-gray-400">Începe să cauți meseriași pentru a-ți construi istoricul.</p>
                        <a href="{{ route('search') }}" 
                           class="mt-4 inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Caută meseriași
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Sidebar - Popular Searches -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden sticky top-4">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" />
                            </svg>
                            Căutări Populare
                        </h2>
                    </div>
                    
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($popularSearches as $index => $popular)
                        <a href="{{ route('search', ['q' => $popular->query]) }}" 
                           class="flex items-center px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                            <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full 
                                         {{ $index < 3 ? 'bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }} 
                                         text-sm font-medium">
                                {{ $index + 1 }}
                            </span>
                            <span class="ml-3 text-gray-900 dark:text-white truncate flex-1">{{ $popular->query }}</span>
                            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">{{ $popular->count }}</span>
                        </a>
                        @empty
                        <div class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Nicio căutare populară încă.
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Quick Search Tips -->
                <div class="mt-6 bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/30 dark:to-primary-800/30 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-primary-900 dark:text-primary-100 mb-4">
                        Sfaturi pentru Căutare
                    </h3>
                    <ul class="space-y-3 text-sm text-primary-800 dark:text-primary-200">
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Folosește numele meseriei (ex: "instalator", "electrician")
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Adaugă locația pentru rezultate mai relevante
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Filtrează după rating pentru calitate garantată
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Verifică disponibilitatea pentru urgențe
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
