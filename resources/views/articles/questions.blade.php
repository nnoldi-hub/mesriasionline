@extends('layouts.app')

@section('title', 'Întrebări și Răspunsuri - Meseriași.ro')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-primary-600 to-primary-700 text-white py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl">
            <h1 class="text-3xl md:text-4xl font-bold mb-4">Întrebări și Răspunsuri</h1>
            <p class="text-lg text-white/90">
                Găsește răspunsuri la întrebările tale sau pune o întrebare și primește sfaturi de la experți.
            </p>
        </div>
    </div>
</section>

<div class="container mx-auto px-4 py-8">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Main Content -->
        <div class="flex-1">
            <!-- Action Bar -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div class="flex flex-wrap items-center gap-4">
                    <form method="GET" action="{{ route('intrebari') }}" class="flex gap-2">
                        <select name="category" onchange="this.form.submit()"
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            <option value="">Toate categoriile</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="filter" onchange="this.form.submit()"
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            <option value="">Toate întrebările</option>
                            <option value="answered" {{ request('filter') == 'answered' ? 'selected' : '' }}>Cu răspuns</option>
                        </select>
                    </form>
                </div>
                <a href="{{ route('intrebari.pune') }}" 
                   class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Pune o întrebare
                </a>
            </div>

            <!-- Questions List -->
            <div class="space-y-4">
                @forelse($questions as $question)
                    <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start gap-4">
                            <!-- Status Icon -->
                            <div class="flex-shrink-0">
                                @if($question->status == 'answered')
                                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    @if($question->is_featured)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                            ⭐ Popular
                                        </span>
                                    @endif
                                    @if($question->category)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                            {{ $question->category->name }}
                                        </span>
                                    @endif
                                    <span class="text-xs text-gray-500">{{ $question->created_at->diffForHumans() }}</span>
                                </div>

                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $question->title }}</h3>
                                <p class="text-gray-600 mb-4">{{ Str::limit($question->question, 200) }}</p>

                                @if($question->answer)
                                    <div class="bg-green-50 border-l-4 border-green-400 rounded-r-lg p-4">
                                        <div class="flex items-center mb-2">
                                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-sm font-medium text-green-800">
                                                Răspuns de {{ $question->answeredBy->name ?? 'Expert' }}
                                            </span>
                                        </div>
                                        <p class="text-green-800">{{ Str::limit($question->answer, 300) }}</p>
                                    </div>
                                @else
                                    <p class="text-sm text-yellow-600 italic">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Așteaptă răspuns...
                                    </p>
                                @endif

                                <div class="flex items-center gap-4 mt-4 text-sm text-gray-500">
                                    <span>Întrebare de {{ $question->author_name }}</span>
                                    <span>{{ number_format($question->views_count) }} vizualizări</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nicio întrebare</h3>
                        <p class="text-gray-500 mb-4">Fii primul care pune o întrebare!</p>
                        <a href="{{ route('intrebari.pune') }}" 
                           class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                            Pune o întrebare
                        </a>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $questions->links() }}
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:w-80">
            <!-- Featured Questions -->
            @if($featuredQuestions->count() > 0)
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        ⭐ Întrebări populare
                    </h3>
                    <div class="space-y-4">
                        @foreach($featuredQuestions as $fq)
                            <div class="pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                                <h4 class="font-medium text-gray-900 text-sm mb-1">{{ Str::limit($fq->title, 60) }}</h4>
                                @if($fq->category)
                                    <span class="text-xs text-gray-500">{{ $fq->category->name }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Categories -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Categorii</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('intrebari') }}" 
                           class="flex items-center justify-between text-gray-600 hover:text-primary-600 transition-colors {{ !request('category') ? 'text-primary-600 font-medium' : '' }}">
                            <span>Toate</span>
                        </a>
                    </li>
                    @foreach($categories as $category)
                        <li>
                            <a href="{{ route('intrebari', ['category' => $category->slug]) }}" 
                               class="flex items-center justify-between text-gray-600 hover:text-primary-600 transition-colors {{ request('category') == $category->slug ? 'text-primary-600 font-medium' : '' }}">
                                <span>{{ $category->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- CTA -->
            <div class="bg-gradient-to-br from-primary to-primary/80 rounded-lg shadow-sm p-6 text-white">
                <h3 class="text-lg font-semibold mb-2">Nu găsești răspunsul?</h3>
                <p class="text-sm opacity-90 mb-4">
                    Pune propria ta întrebare și primește sfaturi personalizate.
                </p>
                <a href="{{ route('intrebari.pune') }}" 
                   class="inline-block w-full text-center px-4 py-2 bg-white text-primary-600 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                    Pune o întrebare
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
