@extends('layouts.dashboard')

@section('title', 'Articol Nou - Admin')
@section('page-title', 'Articol Nou')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.articles.index') }}" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Articol Nou</h1>
            <p class="text-gray-600">Creează un articol, interviu sau ghid nou</p>
        </div>
    </div>

    <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" class="max-w-4xl">
        @csrf

        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <!-- Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Titlu <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-lg"
                       placeholder="Titlul articolului">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Type & Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tip conținut <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="type" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        <option value="article" {{ old('type') == 'article' ? 'selected' : '' }}>Articol</option>
                        <option value="interview" {{ old('type') == 'interview' ? 'selected' : '' }}>Interviu</option>
                        <option value="guide" {{ old('type') == 'guide' ? 'selected' : '' }}>Ghid practic</option>
                        <option value="news" {{ old('type') == 'news' ? 'selected' : '' }}>Știre</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Ciornă</option>
                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Publicat</option>
                    </select>
                </div>
            </div>

            <!-- Featured Craftsman (for interviews) -->
            <div class="mb-6" id="craftsman-section" style="display: none;">
                <label for="featured_craftsman_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Meșterul intervievat
                </label>
                <select name="featured_craftsman_id" id="featured_craftsman_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="">Selectează meșterul</option>
                    @foreach($craftsmen as $craftsman)
                        <option value="{{ $craftsman->id }}" {{ old('featured_craftsman_id') == $craftsman->id ? 'selected' : '' }}>
                            {{ $craftsman->name }} - {{ $craftsman->category->name ?? 'Fără categorie' }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-sm text-gray-500">Selectează meșterul pentru interviu</p>
            </div>

            <!-- Featured Image -->
            <div class="mb-6">
                <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">
                    Imagine principală
                </label>
                <div class="flex items-center justify-center w-full">
                    <label for="featured_image" class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6" id="upload-placeholder">
                            <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click pentru upload</span> sau drag and drop</p>
                            <p class="text-xs text-gray-500">PNG, JPG, WEBP (MAX. 5MB)</p>
                        </div>
                        <img id="image-preview" class="hidden max-h-44 rounded-lg" alt="Preview">
                        <input id="featured_image" name="featured_image" type="file" class="hidden" accept="image/*" />
                    </label>
                </div>
                @error('featured_image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Excerpt -->
            <div class="mb-6">
                <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">
                    Rezumat
                </label>
                <textarea name="excerpt" id="excerpt" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                          placeholder="O scurtă descriere a articolului (max 500 caractere)">{{ old('excerpt') }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Acest text va apărea în listele de articole</p>
                @error('excerpt')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Content -->
            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    Conținut <span class="text-red-500">*</span>
                </label>
                <textarea name="content" id="content" rows="15" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                          placeholder="Scrie conținutul articolului aici...">{{ old('content') }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Poți folosi HTML pentru formatare</p>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tags -->
            <div class="mb-6">
                <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                    Etichete (tags)
                </label>
                <input type="text" name="tags" id="tags" value="{{ old('tags') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                       placeholder="ex: renovări, sfaturi, instalații (separate prin virgulă)">
                <p class="mt-1 text-sm text-gray-500">Separă etichetele prin virgulă</p>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-4">
            <button type="submit" name="status" value="draft"
                    class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                Salvează ca ciornă
            </button>
            <button type="submit" name="status" value="published"
                    class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                Publică articolul
            </button>
            <a href="{{ route('admin.articles.index') }}" class="px-6 py-3 text-gray-600 hover:text-gray-800">
                Anulează
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Show/hide craftsman section based on type
    document.getElementById('type').addEventListener('change', function() {
        const craftsmanSection = document.getElementById('craftsman-section');
        if (this.value === 'interview') {
            craftsmanSection.style.display = 'block';
        } else {
            craftsmanSection.style.display = 'none';
        }
    });

    // Trigger on load
    if (document.getElementById('type').value === 'interview') {
        document.getElementById('craftsman-section').style.display = 'block';
    }

    // Image preview
    document.getElementById('featured_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('image-preview').classList.remove('hidden');
                document.getElementById('upload-placeholder').classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection
