@extends('layouts.dashboard')

@section('title', 'Întrebări & Răspunsuri - Admin')
@section('page-title', 'Întrebări & Răspunsuri')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Întrebări & Răspunsuri</h1>
            <p class="text-gray-600">Gestionează întrebările de la vizitatori</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</div>
            <div class="text-sm text-gray-500">Total întrebări</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
            <div class="text-sm text-gray-500">În așteptare</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-green-600">{{ $stats['answered'] }}</div>
            <div class="text-sm text-gray-500">Cu răspuns</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-purple-600">{{ $stats['featured'] }}</div>
            <div class="text-sm text-gray-500">Featured</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4">
            <form method="GET" action="{{ route('admin.articles.questions') }}" class="flex flex-wrap gap-4">
                <div class="w-48">
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        <option value="">Toate statusurile</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>În așteptare</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Aprobat</option>
                        <option value="answered" {{ request('status') == 'answered' ? 'selected' : '' }}>Răspuns</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Respins</option>
                    </select>
                </div>
                <div class="w-48">
                    <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        <option value="">Toate categoriile</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Filtrează
                </button>
                @if(request()->hasAny(['status', 'category_id']))
                    <a href="{{ route('admin.articles.questions') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                        Resetează
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Questions List -->
    <div class="space-y-4">
        @forelse($questions as $question)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            @if($question->is_featured)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                    ⭐ Featured
                                </span>
                            @endif
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($question->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($question->status == 'approved') bg-blue-100 text-blue-800
                                @elseif($question->status == 'answered') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $question->status_label }}
                            </span>
                            @if($question->category)
                                <span class="text-sm text-gray-500">{{ $question->category->name }}</span>
                            @endif
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $question->title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            de <strong>{{ $question->author_name }}</strong> 
                            ({{ $question->author_email }})
                            • {{ $question->created_at->format('d.m.Y H:i') }}
                        </p>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <p class="text-gray-700">{{ $question->question }}</p>
                </div>

                @if($question->answer)
                    <div class="bg-green-50 border-l-4 border-green-400 rounded-lg p-4 mb-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-medium text-green-800">
                                Răspuns de {{ $question->answeredBy->name ?? 'Admin' }}
                                @if($question->answered_at)
                                    • {{ $question->answered_at->format('d.m.Y H:i') }}
                                @endif
                            </span>
                        </div>
                        <p class="text-green-800">{{ $question->answer }}</p>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex items-center gap-3 pt-4 border-t">
                    @if(!$question->answer)
                        <a href="{{ route('admin.articles.answer-question', $question->id) }}"
                           class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            Răspunde
                        </a>
                    @else
                        <a href="{{ route('admin.articles.answer-question', $question->id) }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                            Editează răspunsul
                        </a>
                    @endif

                    <form action="{{ route('admin.articles.toggle-question-featured', $question->id) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center px-3 py-2 text-sm {{ $question->is_featured ? 'text-yellow-600' : 'text-gray-500' }} hover:text-yellow-700">
                            ⭐ {{ $question->is_featured ? 'Scoate din featured' : 'Adaugă la featured' }}
                        </button>
                    </form>

                    @if($question->status == 'pending')
                        <form action="{{ route('admin.articles.update-question-status', $question->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                                Aprobă
                            </button>
                        </form>
                        <form action="{{ route('admin.articles.update-question-status', $question->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                                Respinge
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('admin.articles.delete-question', $question->id) }}" method="POST" class="ml-auto"
                          onsubmit="return confirm('Sigur vrei să ștergi această întrebare?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                            Șterge
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nicio întrebare</h3>
                <p class="text-gray-500">Nu există întrebări de la vizitatori momentan.</p>
            </div>
        @endforelse

        <div class="mt-6">
            {{ $questions->links() }}
        </div>
    </div>
</div>
@endsection
