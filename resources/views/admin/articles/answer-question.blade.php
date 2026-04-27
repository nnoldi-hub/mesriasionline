@extends('layouts.dashboard')

@section('title', 'Răspunde la întrebare - Admin')
@section('page-title', 'Răspunde la întrebare')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.articles.questions') }}" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Răspunde la întrebare</h1>
            <p class="text-gray-600">Oferă un răspuns vizitatorului</p>
        </div>
    </div>

    <div class="max-w-3xl">
        <!-- Question Details -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center gap-2 mb-4">
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

            <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $question->title }}</h2>
            
            <p class="text-sm text-gray-500 mb-4">
                Întrebare de la <strong>{{ $question->author_name }}</strong> 
                ({{ $question->author_email }})
                • {{ $question->created_at->format('d.m.Y H:i') }}
            </p>

            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-700 whitespace-pre-wrap">{{ $question->question }}</p>
            </div>
        </div>

        <!-- Answer Form -->
        <form action="{{ route('admin.articles.store-answer', $question->id) }}" method="POST">
            @csrf

            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="mb-6">
                    <label for="answer" class="block text-sm font-medium text-gray-700 mb-2">
                        Răspunsul tău <span class="text-red-500">*</span>
                    </label>
                    <textarea name="answer" id="answer" rows="8" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                              placeholder="Scrie răspunsul aici...">{{ old('answer', $question->answer) }}</textarea>
                    @error('answer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_featured" id="is_featured" value="1"
                           {{ old('is_featured', $question->is_featured) ? 'checked' : '' }}
                           class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                    <label for="is_featured" class="ml-2 text-sm text-gray-700">
                        Marchează ca întrebare featured (va apărea în secțiunea principală)
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-4">
                <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    {{ $question->answer ? 'Actualizează răspunsul' : 'Trimite răspunsul' }}
                </button>
                <a href="{{ route('admin.articles.questions') }}" class="px-6 py-3 text-gray-600 hover:text-gray-800">
                    Anulează
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
