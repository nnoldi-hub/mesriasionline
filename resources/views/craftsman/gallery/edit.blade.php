@extends('layouts.craftsman')

@section('title', 'Editează Imagine')
@section('page-title', 'Editează Imagine')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('craftsman.gallery') }}" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h2 class="text-xl font-bold text-gray-800">Modifică Detalii Imagine</h2>
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

        <form action="{{ route('craftsman.gallery.update', $image->id) }}" method="POST" class="bg-white rounded-lg shadow p-6">
            @csrf
            @method('PUT')

            <!-- Image Preview -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Imagine</label>
                <div class="w-64 h-64 rounded-lg overflow-hidden bg-gray-100">
                    <img src="{{ asset('storage/' . $image->image_path) }}" 
                         alt="{{ $image->caption }}" 
                         class="w-full h-full object-cover">
                </div>
            </div>

            <!-- Caption -->
            <div class="mb-4">
                <label for="caption" class="block text-sm font-medium text-gray-700 mb-1">
                    Descriere
                </label>
                <input type="text" name="caption" id="caption" value="{{ old('caption', $image->caption) }}" 
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                       placeholder="Ex: Renovare baie completă">
            </div>

            <!-- Service -->
            <div class="mb-4">
                <label for="service_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Serviciu asociat
                </label>
                <select name="service_id" id="service_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                    <option value="">-- Selectează serviciul --</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ old('service_id', $image->service_id) == $service->id ? 'selected' : '' }}>
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
                        <option value="{{ $key }}" {{ old('category', $image->category) == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Type -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tip imagine <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="before_after" value="single" {{ old('before_after', $image->before_after) === 'single' ? 'checked' : '' }} 
                               class="text-primary focus:ring-primary">
                        <span class="text-gray-700">Simplă</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="before_after" value="before" {{ old('before_after', $image->before_after) === 'before' ? 'checked' : '' }}
                               class="text-primary focus:ring-primary">
                        <span class="text-gray-700">Înainte</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="before_after" value="after" {{ old('before_after', $image->before_after) === 'after' ? 'checked' : '' }}
                               class="text-primary focus:ring-primary">
                        <span class="text-gray-700">După</span>
                    </label>
                </div>
            </div>

            <!-- Featured -->
            <div class="mb-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $image->is_featured) ? 'checked' : '' }}
                           class="rounded text-primary focus:ring-primary">
                    <span class="text-gray-700">Afișează în secțiunea featured (galeria principală)</span>
                </label>
            </div>

            <div class="flex justify-between">
                <form action="{{ route('craftsman.gallery.delete', $image->id) }}" method="POST" 
                      onsubmit="return confirm('Sigur vrei să ștergi această imagine?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                        Șterge imaginea
                    </button>
                </form>

                <div class="flex gap-4">
                    <a href="{{ route('craftsman.gallery') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">
                        Anulează
                    </a>
                    <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2 px-6 rounded-lg">
                        Salvează
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
