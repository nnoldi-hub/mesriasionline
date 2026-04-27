@extends('layouts.craftsman')

@section('title', 'Încarcă Imagini')
@section('page-title', 'Încarcă Imagini')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('craftsman.gallery') }}" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h2 class="text-xl font-bold text-gray-800">Încarcă Imagini Noi</h2>
    </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('craftsman.gallery.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6">
            @csrf

            <!-- Image Upload -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Imagini <span class="text-red-500">*</span>
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary transition-colors cursor-pointer" id="dropzone">
                    <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden" required>
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-600 mb-2">Trage imaginile aici sau click pentru a selecta</p>
                    <p class="text-sm text-gray-500">Maximum 10 imagini, până la 5MB fiecare (JPG, PNG, WebP)</p>
                </div>
                <div id="preview" class="grid grid-cols-4 gap-2 mt-4"></div>
            </div>

            <!-- Caption -->
            <div class="mb-4">
                <label for="caption" class="block text-sm font-medium text-gray-700 mb-1">
                    Descriere (opțional)
                </label>
                <input type="text" name="caption" id="caption" value="{{ old('caption') }}" 
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                       placeholder="Ex: Renovare baie completă">
            </div>

            <!-- Service -->
            <div class="mb-4">
                <label for="service_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Serviciu asociat (opțional)
                </label>
                <select name="service_id" id="service_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                    <option value="">-- Selectează serviciul --</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Category -->
            <div class="mb-4">
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                    Categorie portofoliu
                </label>
                <select name="category" id="category" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                    <option value="">-- Selectează categoria --</option>
                    @foreach(\App\Models\Gallery::CATEGORIES as $key => $label)
                        <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Organizează-ți lucrările pe categorii pentru o prezentare mai bună.</p>
            </div>

            <!-- Type -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tip imagine <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="before_after" value="single" {{ old('before_after', 'single') === 'single' ? 'checked' : '' }} 
                               class="text-primary focus:ring-primary">
                        <span class="text-gray-700">Simplă</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="before_after" value="before" {{ old('before_after') === 'before' ? 'checked' : '' }}
                               class="text-primary focus:ring-primary">
                        <span class="text-gray-700">Înainte</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="before_after" value="after" {{ old('before_after') === 'after' ? 'checked' : '' }}
                               class="text-primary focus:ring-primary">
                        <span class="text-gray-700">După</span>
                    </label>
                </div>
            </div>

            <!-- Featured -->
            <div class="mb-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                           class="rounded text-primary focus:ring-primary">
                    <span class="text-gray-700">Afișează în secțiunea featured (galeria principală)</span>
                </label>
            </div>

            <div class="flex justify-end gap-4">
                <a href="{{ route('craftsman.gallery') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">
                    Anulează
                </a>
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2 px-6 rounded-lg">
                    Încarcă Imaginile
                </button>
            </div>
        </form>
    </div>

<script>
    const dropzone = document.getElementById('dropzone');
    const input = document.getElementById('images');
    const preview = document.getElementById('preview');

    dropzone.addEventListener('click', () => input.click());
    
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-primary', 'bg-primary/5');
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('border-primary', 'bg-primary/5');
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-primary', 'bg-primary/5');
        input.files = e.dataTransfer.files;
        showPreview();
    });

    input.addEventListener('change', showPreview);

    function showPreview() {
        preview.innerHTML = '';
        const files = input.files;
        
        for (let i = 0; i < Math.min(files.length, 10); i++) {
            const file = files[i];
            const reader = new FileReader();
            
            reader.onload = (e) => {
                const div = document.createElement('div');
                div.className = 'aspect-square rounded-lg overflow-hidden';
                div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                preview.appendChild(div);
            };
            
            reader.readAsDataURL(file);
        }

        if (files.length > 10) {
            const warning = document.createElement('div');
            warning.className = 'col-span-4 text-yellow-600 text-sm mt-2';
            warning.textContent = `Doar primele 10 imagini vor fi încărcate (ai selectat ${files.length})`;
            preview.appendChild(warning);
        }
    }
</script>
@endsection
