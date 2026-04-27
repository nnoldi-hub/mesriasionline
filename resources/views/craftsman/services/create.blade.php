@extends('layouts.craftsman')

@section('title', 'Adaugă Serviciu')
@section('page-title', 'Adaugă Serviciu Nou')

@section('content')
<div class="mb-4">
    <a href="{{ route('craftsman.services') }}" class="text-primary-600 hover:text-primary-800 flex items-center">
        <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
        </svg>
        Înapoi la servicii
    </a>
</div>

@if($errors->any())
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('craftsman.services.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nume serviciu -->
            <div class="md:col-span-2">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nume serviciu <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    placeholder="Ex: Instalare centrală termică"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
            </div>

            <!-- Categorie -->
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Categorie</label>
                <select name="category_id" id="category_id" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                    <option value="">Categoria implicită din profil</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tip preț -->
            <div>
                <label for="pricing_type" class="block text-sm font-medium text-gray-700 mb-2">Tip preț <span class="text-red-500">*</span></label>
                <select name="pricing_type" id="pricing_type" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                    <option value="fixed" {{ old('pricing_type') === 'fixed' ? 'selected' : '' }}>Preț fix</option>
                    <option value="range" {{ old('pricing_type') === 'range' ? 'selected' : '' }}>Interval de preț</option>
                    <option value="on_request" {{ old('pricing_type') === 'on_request' ? 'selected' : '' }}>La cerere</option>
                </select>
            </div>

            <!-- Preț fix -->
            <div id="price_fixed_container">
                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Preț (RON)</label>
                <input type="number" name="price" id="price" value="{{ old('price') }}" step="1" min="0"
                    placeholder="Ex: 500"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
            </div>

            <!-- Interval preț -->
            <div id="price_range_container" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Interval preț (RON)</label>
                <div class="flex space-x-2">
                    <input type="number" name="min_price" id="min_price" value="{{ old('min_price') }}" step="1" min="0" placeholder="Min"
                        class="w-1/2 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                    <input type="number" name="max_price" id="max_price" value="{{ old('max_price') }}" step="1" min="0" placeholder="Max"
                        class="w-1/2 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                </div>
            </div>

            <!-- Durată -->
            <div>
                <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">Durată estimată (minute)</label>
                <input type="number" name="duration" id="duration" value="{{ old('duration') }}" min="0"
                    placeholder="Ex: 120"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
            </div>

            <!-- Descriere -->
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descriere scurtă</label>
                <textarea name="description" id="description" rows="2" 
                    placeholder="O scurtă descriere a serviciului"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">{{ old('description') }}</textarea>
            </div>

            <!-- Descriere detaliată -->
            <div class="md:col-span-2">
                <label for="detailed_description" class="block text-sm font-medium text-gray-700 mb-2">Descriere detaliată</label>
                <textarea name="detailed_description" id="detailed_description" rows="4" 
                    placeholder="Descriere completă a serviciului, ce include, materiale necesare, etc."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">{{ old('detailed_description') }}</textarea>
            </div>

            <!-- Opțiuni -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Opțiuni</label>
                <div class="flex flex-wrap gap-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1" checked
                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="ml-2 text-sm text-gray-700">Serviciu activ</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_mobile" value="1" {{ old('is_mobile') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="ml-2 text-sm text-gray-700">Mă deplasez la client</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="materials_included" value="1" {{ old('materials_included') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="ml-2 text-sm text-gray-700">Materiale incluse în preț</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('craftsman.services') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Anulează
            </a>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                Salvează Serviciul
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pricingType = document.getElementById('pricing_type');
    const priceFixedContainer = document.getElementById('price_fixed_container');
    const priceRangeContainer = document.getElementById('price_range_container');

    function togglePriceFields() {
        if (pricingType.value === 'range') {
            priceFixedContainer.classList.add('hidden');
            priceRangeContainer.classList.remove('hidden');
        } else if (pricingType.value === 'on_request') {
            priceFixedContainer.classList.add('hidden');
            priceRangeContainer.classList.add('hidden');
        } else {
            priceFixedContainer.classList.remove('hidden');
            priceRangeContainer.classList.add('hidden');
        }
    }

    pricingType.addEventListener('change', togglePriceFields);
    togglePriceFields();
});
</script>
@endsection
