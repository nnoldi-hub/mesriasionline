@extends('layouts.app')

@section('title', $article->title . ' - Meseriași.ro')

@section('content')
<article>
    <!-- Hero / Featured Image -->
    @if($article->featured_image)
        <div class="relative h-64 md:h-96 bg-gray-900">
            <img src="{{ $article->featured_image_url }}" alt="{{ $article->title }}" 
                 class="w-full h-full object-cover opacity-80">
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-8">
                <div class="container mx-auto">
                    <div class="max-w-3xl">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mb-4
                            @if($article->type == 'interview') bg-purple-600 text-white
                            @elseif($article->type == 'guide') bg-blue-600 text-white
                            @elseif($article->type == 'news') bg-orange-600 text-white
                            @else bg-primary-600 text-white @endif">
                            {{ $article->type_label }}
                        </span>
                        <h1 class="text-3xl md:text-4xl font-bold text-white">{{ $article->title }}</h1>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-gradient-to-r from-primary to-primary/80 py-12">
            <div class="container mx-auto px-4">
                <div class="max-w-3xl">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 text-white mb-4">
                        {{ $article->type_label }}
                    </span>
                    <h1 class="text-3xl md:text-4xl font-bold text-white">{{ $article->title }}</h1>
                </div>
            </div>
        </div>
    @endif

    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Main Content -->
            <div class="flex-1">
                <div class="bg-white rounded-lg shadow-sm p-6 md:p-8">
                    <!-- Meta -->
                    <div class="flex flex-wrap items-center gap-4 pb-6 mb-6 border-b border-gray-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-gray-600">{{ $article->published_at->format('d MMMM Y') }}</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span class="text-gray-600">{{ number_format($article->views_count) }} vizualizări</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="text-gray-600">de {{ $article->author->name ?? 'Admin' }}</span>
                        </div>
                    </div>

                    <!-- Featured Craftsman (for interviews) -->
                    @if($article->type == 'interview' && $article->featuredCraftsman)
                        <div class="bg-purple-50 border-l-4 border-purple-400 rounded-lg p-6 mb-6">
                            <div class="flex items-center gap-4">
                                <img src="{{ $article->featuredCraftsman->avatar_url ?? '/images/default-avatar.png' }}" 
                                     alt="{{ $article->featuredCraftsman->name }}"
                                     class="w-16 h-16 rounded-full object-cover">
                                <div>
                                    <p class="text-sm text-purple-600 font-medium">Interviu cu</p>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $article->featuredCraftsman->name }}</h3>
                                    @if($article->featuredCraftsman->category)
                                        <p class="text-gray-600">{{ $article->featuredCraftsman->category->name }}</p>
                                    @endif
                                </div>
                                <a href="{{ route('specialist.show', $article->featuredCraftsman->slug) }}" 
                                   class="ml-auto px-4 py-2 bg-purple-600 text-white rounded-lg text-sm hover:bg-purple-700 transition-colors">
                                    Vezi profilul
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Excerpt -->
                    @if($article->excerpt)
                        <div class="text-lg text-gray-700 font-medium mb-6 pb-6 border-b border-gray-200">
                            {{ $article->excerpt }}
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="prose prose-lg max-w-none">
                        {!! $article->content !!}
                    </div>

                    <!-- Tags -->
                    @if($article->tags && count($article->tags) > 0)
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-gray-600 font-medium">Etichete:</span>
                                @foreach($article->tags as $tag)
                                    <a href="{{ route('articole', ['tag' => $tag]) }}"
                                       class="inline-block px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-primary-600 hover:text-white transition-colors">
                                        {{ $tag }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Share -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex items-center gap-4">
                            <span class="text-gray-600 font-medium">Distribuie:</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                               target="_blank" rel="noopener"
                               class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($article->title) }}" 
                               target="_blank" rel="noopener"
                               class="w-10 h-10 bg-sky-500 text-white rounded-full flex items-center justify-center hover:bg-sky-600 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                </svg>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($article->title . ' - ' . request()->url()) }}" 
                               target="_blank" rel="noopener"
                               class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center hover:bg-green-600 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Related Articles -->
                @if($relatedArticles->count() > 0)
                    <div class="mt-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Articole similare</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($relatedArticles as $related)
                                <a href="{{ route('articole.show', $related->slug) }}" class="bg-white rounded-lg shadow-sm overflow-hidden group hover:shadow-md transition-shadow">
                                    @if($related->featured_image)
                                        <div class="aspect-video overflow-hidden">
                                            <img src="{{ $related->featured_image_url }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        </div>
                                    @else
                                        <div class="aspect-video bg-gray-100 flex items-center justify-center">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <h3 class="font-semibold text-gray-900 group-hover:text-primary-600 transition-colors line-clamp-2">
                                            {{ $related->title }}
                                        </h3>
                                        <p class="text-sm text-gray-500 mt-1">{{ $related->published_at->format('d M Y') }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:w-80">
                <!-- Author Card -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-4">Autor</h3>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-semibold text-lg">
                            {{ strtoupper(substr($article->author->name ?? 'A', 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">{{ $article->author->name ?? 'Admin' }}</div>
                            <div class="text-sm text-gray-500">Meseriași.ro</div>
                        </div>
                    </div>
                </div>

                <!-- Q&A CTA -->
                <div class="bg-gradient-to-br from-primary to-primary/80 rounded-lg shadow-sm p-6 text-white">
                    <h3 class="text-lg font-semibold mb-2">Ai întrebări?</h3>
                    <p class="text-sm opacity-90 mb-4">
                        Pune o întrebare legată de acest subiect și primește răspuns de la experți.
                    </p>
                    <a href="{{ route('intrebari.pune') }}" 
                       class="inline-block w-full text-center px-4 py-2 bg-white text-primary-600 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                        Pune o întrebare
                    </a>
                </div>
            </div>
        </div>
    </div>
</article>
@endsection
