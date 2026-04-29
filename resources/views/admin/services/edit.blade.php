@extends('layouts.dashboard')

@section('title', 'Editare Serviciu - Administrator')
@section('page-title', 'Editare Serviciu: {{ $service->name }}')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.services') }}" class="text-primary-600 hover:text-primary-800 flex items-center">
        <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
        </svg>
        Înapoi la lista de servicii
    </a>
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
@endif

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
    <form id="admin-service-form" action="{{ route('admin.services.update', $service->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nume serviciu -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nume serviciu</label>
                <input type="text" name="name" id="name" value="{{ old('name', $service->name) }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
            </div>

            <!-- Categorie -->
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Categorie</label>
                <select name="category_id" id="category_id" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                    <option value="">Selectează categoria</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $service->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Descriere -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Descriere scurtă</label>
                <div id="admin_svc_desc_editor" style="min-height:80px;" class="border border-gray-300 rounded-lg bg-white"></div>
                <textarea name="description" id="admin_svc_description" class="hidden">{{ old('description', $service->description) }}</textarea>
            </div>

            <!-- Descriere detaliată -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Descriere detaliată</label>
                <div id="admin_svc_detail_editor" style="min-height:140px;" class="border border-gray-300 rounded-lg bg-white"></div>
                <textarea name="detailed_description" id="admin_svc_detailed_description" class="hidden">{{ old('detailed_description', $service->detailed_description) }}</textarea>
            </div>

            <!-- Tip preț -->
            <div>
                <label for="pricing_type" class="block text-sm font-medium text-gray-700 mb-2">Tip preț</label>
                <select name="pricing_type" id="pricing_type" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                    <option value="fixed" {{ old('pricing_type', $service->pricing_type) === 'fixed' ? 'selected' : '' }}>Preț fix</option>
                    <option value="range" {{ old('pricing_type', $service->pricing_type) === 'range' ? 'selected' : '' }}>Interval de preț</option>
                    <option value="on_request" {{ old('pricing_type', $service->pricing_type) === 'on_request' ? 'selected' : '' }}>La cerere</option>
                </select>
            </div>

            <!-- Preț fix -->
            <div id="price_fixed_container">
                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Preț (RON)</label>
                <input type="number" name="price" id="price" value="{{ old('price', $service->price) }}" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
            </div>

            <!-- Interval preț -->
            <div id="price_range_container" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Interval preț (RON)</label>
                <div class="flex space-x-2">
                    <input type="number" name="min_price" id="min_price" value="{{ old('min_price', $service->min_price) }}" step="0.01" min="0" placeholder="Min"
                        class="w-1/2 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                    <input type="number" name="max_price" id="max_price" value="{{ old('max_price', $service->max_price) }}" step="0.01" min="0" placeholder="Max"
                        class="w-1/2 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                </div>
            </div>

            <!-- Durată -->
            <div>
                <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">Durată (minute)</label>
                <input type="number" name="duration" id="duration" value="{{ old('duration', $service->duration) }}" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
            </div>

            <!-- Interval durată -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sau interval durată (minute)</label>
                <div class="flex space-x-2">
                    <input type="number" name="min_duration" id="min_duration" value="{{ old('min_duration', $service->min_duration) }}" min="0" placeholder="Min"
                        class="w-1/2 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                    <input type="number" name="max_duration" id="max_duration" value="{{ old('max_duration', $service->max_duration) }}" min="0" placeholder="Max"
                        class="w-1/2 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                </div>
            </div>

            <!-- Opțiuni -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Opțiuni</label>
                <div class="flex flex-wrap gap-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $service->is_active) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="ml-2 text-sm text-gray-700">Serviciu activ</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_mobile" value="1" {{ old('is_mobile', $service->is_mobile) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="ml-2 text-sm text-gray-700">Deplasare la client</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="materials_included" value="1" {{ old('materials_included', $service->materials_included) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="ml-2 text-sm text-gray-700">Materiale incluse</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.services') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Anulează
            </a>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                Salvează modificările
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
@endpush

@push('head')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
var adminSvcDescQuill = new Quill('#admin_svc_desc_editor', {
    theme: 'snow',
    placeholder: 'Descriere scurtă serviciu...',
    modules: { toolbar: [['bold', 'italic'], [{ 'list': 'bullet' }], ['clean']] }
});
var adminSvcDetailQuill = new Quill('#admin_svc_detail_editor', {
    theme: 'snow',
    placeholder: 'Descriere detaliată...',
    modules: {
        toolbar: [
            [{ 'header': [2, 3, false] }],
            ['bold', 'italic'],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            ['clean']
        ]
    }
});
var adminSvcDescVal = document.getElementById('admin_svc_description').value;
if (adminSvcDescVal) { adminSvcDescQuill.clipboard.dangerouslyPasteHTML(adminSvcDescVal); setTimeout(function(){ adminSvcDescQuill.setSelection(null); }, 50); }
var adminSvcDetailVal = document.getElementById('admin_svc_detailed_description').value;
if (adminSvcDetailVal) { adminSvcDetailQuill.clipboard.dangerouslyPasteHTML(adminSvcDetailVal); setTimeout(function(){ adminSvcDetailQuill.setSelection(null); }, 50); }

adminSvcDescQuill.on('text-change', function() {
    var h = adminSvcDescQuill.root.innerHTML;
    document.getElementById('admin_svc_description').value = (h === '<p><br></p>') ? '' : h;
});
adminSvcDetailQuill.on('text-change', function() {
    var h = adminSvcDetailQuill.root.innerHTML;
    document.getElementById('admin_svc_detailed_description').value = (h === '<p><br></p>') ? '' : h;
});
document.getElementById('admin-service-form').addEventListener('submit', function () {
    var h1 = adminSvcDescQuill.root.innerHTML;
    var h2 = adminSvcDetailQuill.root.innerHTML;
    document.getElementById('admin_svc_description').value = (h1 === '<p><br></p>') ? '' : h1;
    document.getElementById('admin_svc_detailed_description').value = (h2 === '<p><br></p>') ? '' : h2;
});
</script>
@endpush
@endsection
