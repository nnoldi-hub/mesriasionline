@extends('layouts.app')

@section('title', 'Articole și Sfaturi - Meseriași.ro')

@section('content')
<!-- Hero Section -->
<section class="py-12" style="background-color: #ECF0F1;">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl">
            <h1 class="text-3xl md:text-4xl font-extrabold mb-4" style="font-family:'Rubik',sans-serif; color:#2980B9;">Articole și Sfaturi</h1>
            <p class="text-lg" style="color:#2C3E50;">
                Descoperă articole utile, interviuri cu meșteri experimentați și ghiduri practice 
                pentru proiectele tale de acasă.
            </p>
        </div>
    </div>
</section>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Main Content -->
        <div class="flex-1">
            <!-- Filters -->
            <div class="bg-white rounded-2xl shadow-sm p-4 mb-6">
                <form method="GET" action="{{ route('articole') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Caută articole..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-secondary-500 focus:border-transparent">
                    </div>
                    <div class="w-40">
                        <select name="type" onchange="this.form.submit()"
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-secondary-500 focus:border-transparent">
                            <option value="">Toate tipurile</option>
                            <option value="article" {{ request('type') == 'article' ? 'selected' : '' }}>Articole</option>
                            <option value="interview" {{ request('type') == 'interview' ? 'selected' : '' }}>Interviuri</option>
                            <option value="guide" {{ request('type') == 'guide' ? 'selected' : '' }}>Ghiduri</option>
                            <option value="news" {{ request('type') == 'news' ? 'selected' : '' }}>Știri</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 text-white font-semibold rounded-xl hover:opacity-90 transition" style="background-color:#C0392B;">
                        Caută
                    </button>
                </form>
            </div>

            <!-- Articles Grid -->
            @if($articles->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($articles as $article)
                        <article class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-shadow group">
                            <a href="{{ route('articole.show', $article->slug) }}" class="block">
                                @if($article->featured_image)
                                    <div class="aspect-video overflow-hidden">
                                        <img src="{{ $article->featured_image_url }}" alt="{{ $article->title }}" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </div>
                                @else
                                    <div class="aspect-video bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                        </svg>
                                    </div>
                                @endif
                            </a>
                            <div class="p-5">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($article->type == 'interview') bg-purple-100 text-purple-800
                                        @elseif($article->type == 'guide') bg-blue-100 text-blue-800
                                        @elseif($article->type == 'news') bg-orange-100 text-orange-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $article->type_label }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $article->published_at->format('d M Y') }}
                                    </span>
                                </div>
                                
                                <a href="{{ route('articole.show', $article->slug) }}" class="block">
                                    <h2 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-secondary-600 transition-colors line-clamp-2">
                                        {{ $article->title }}
                                    </h2>
                                </a>
                                
                                @if($article->excerpt)
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $article->excerpt }}</p>
                                @endif

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center text-sm text-gray-500">
                                        @if($article->featuredCraftsman)
                                            <img src="{{ $article->featuredCraftsman->avatar_url ?? '/images/default-avatar.png' }}" 
                                                 alt="{{ $article->featuredCraftsman->name }}"
                                                 class="w-6 h-6 rounded-full mr-2">
                                            <span>cu {{ $article->featuredCraftsman->name }}</span>
                                        @else
                                            <span>de {{ $article->author->name ?? 'Admin' }}</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('articole.show', $article->slug) }}" 
                                       class="text-sm font-medium hover:underline" style="color:#C0392B;">
                                        Citește →
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $articles->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Niciun articol găsit</h3>
                    <p class="text-gray-500">Încearcă să modifici filtrele de căutare.</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:w-80">
            <!-- Interviews Section -->
            @if($featuredArticles->count() > 0)
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Interviuri cu Meșteri
                    </h3>
                    <div class="space-y-4">
                        @foreach($featuredArticles as $interview)
                            <a href="{{ route('articole.show', $interview->slug) }}" class="flex items-start gap-3 group">
                                @if($interview->featured_image)
                                    <img src="{{ $interview->featured_image_url }}" alt="" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                                @else
                                    <div class="w-16 h-16 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-8 h-8 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 group-hover:text-secondary-600 transition-colors line-clamp-2">
                                        {{ $interview->title }}
                                    </h4>
                                    @if($interview->featuredCraftsman)
                                        <p class="text-xs text-gray-500 mt-1">cu {{ $interview->featuredCraftsman->name }}</p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Tags Cloud -->
            @if($allTags->count() > 0)
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Etichete populare</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($allTags as $tag)
                            <a href="{{ route('articole', ['tag' => $tag]) }}"
                               class="inline-block px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:text-white transition-colors" onmouseover="this.style.backgroundColor='#C0392B'" onmouseout="this.style.backgroundColor='#f3f4f6'">
                                {{ $tag }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Q&A CTA -->
            <div class="rounded-2xl shadow-sm p-6 text-white" style="background-color:#2980B9;">
                <h3 class="text-lg font-bold mb-2" style="font-family:'Rubik',sans-serif;">Ai o întrebare?</h3>
                <p class="text-sm opacity-90 mb-4">
                    Pune o întrebare și primește sfaturi de la experți.
                </p>
                <a href="{{ route('intrebari.pune') }}" 
                   class="inline-block w-full text-center px-4 py-2 bg-white font-semibold rounded-xl hover:bg-gray-100 transition-colors" style="color:#C0392B;">
                    Pune o întrebare
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
