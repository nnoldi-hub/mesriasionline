@extends('layouts.app')

@section('title', 'Pune o întrebare - Meseriași.ro')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-primary to-primary/80 text-white py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl">
            <h1 class="text-3xl md:text-4xl font-bold mb-4">Pune o întrebare</h1>
            <p class="text-lg opacity-90">
                Ai nevoie de sfaturi? Pune o întrebare și experții noștri îți vor răspunde.
            </p>
        </div>
    </div>
</section>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm p-6 md:p-8">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 mb-6">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('intrebari.store') }}" method="POST">
                @csrf

                <!-- Name & Email -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="author_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Numele tău <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="author_name" id="author_name" value="{{ old('author_name') }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                               placeholder="ex: Ion Popescu">
                    </div>
                    <div>
                        <label for="author_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="author_email" id="author_email" value="{{ old('author_email') }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                               placeholder="ex: ion@email.com">
                        <p class="mt-1 text-sm text-gray-500">Nu va fi afișat public</p>
                    </div>
                </div>

                <!-- Category -->
                <div class="mb-6">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Categorie (opțional)
                    </label>
                    <select name="category_id" id="category_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        <option value="">Selectează o categorie</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Ajută-ne să direcționăm întrebarea către experții potriviți</p>
                </div>

                <!-- Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Subiectul întrebării <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="ex: Cum pot repara o țeavă spartă?">
                </div>

                <!-- Question -->
                <div class="mb-6">
                    <label for="question" class="block text-sm font-medium text-gray-700 mb-2">
                        Întrebarea ta <span class="text-red-500">*</span>
                    </label>
                    <textarea name="question" id="question" rows="6" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                              placeholder="Descrie situația ta în detaliu...">{{ old('question') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">
                        <span id="char-count">0</span>/2000 caractere
                    </p>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">Cum funcționează?</p>
                            <ol class="list-decimal list-inside space-y-1 text-blue-700">
                                <li>Întrebarea ta va fi verificată de echipa noastră</li>
                                <li>După aprobare, va fi publicată și vizibilă pentru toți</li>
                                <li>Vei primi răspuns de la experți în câteva zile</li>
                                <li>Te vom notifica pe email când primești răspuns</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex items-center gap-4">
                    <button type="submit" 
                            class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium">
                        Trimite întrebarea
                    </button>
                    <a href="{{ route('intrebari') }}" class="px-6 py-3 text-gray-600 hover:text-gray-800">
                        Anulează
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const questionInput = document.getElementById('question');
    const charCount = document.getElementById('char-count');
    
    function updateCount() {
        charCount.textContent = questionInput.value.length;
        if (questionInput.value.length > 2000) {
            charCount.classList.add('text-red-600');
        } else {
            charCount.classList.remove('text-red-600');
        }
    }
    
    questionInput.addEventListener('input', updateCount);
    updateCount();
</script>
@endpush
@endsection
