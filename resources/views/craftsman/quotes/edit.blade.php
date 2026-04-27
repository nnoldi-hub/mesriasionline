@extends('layouts.craftsman')

@section('title', 'Editează Oferta')
@section('page-title', 'Editează Oferta')

@section('content')
<div class="mb-6">
    <a href="{{ route('craftsman.quotes.show', $quoteRequest) }}" class="text-primary-600 hover:underline flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Înapoi la cerere
    </a>
</div>

<!-- Request Summary -->
<div class="bg-gray-50 rounded-xl p-4 mb-6">
    <h3 class="font-semibold text-gray-900 mb-2">{{ $quoteRequest->title }}</h3>
    <p class="text-sm text-gray-600">{{ Str::limit($quoteRequest->description, 200) }}</p>
    <div class="flex items-center mt-2 text-sm text-gray-500 space-x-4">
        <span>Client: {{ $quoteRequest->client->name }}</span>
        @if($quoteRequest->budget_max)
            <span class="text-green-600 font-medium">Buget: {{ number_format($quoteRequest->budget_max, 0, ',', '.') }} lei</span>
        @endif
    </div>
</div>

<!-- Edit Quote Form -->
<div class="bg-white rounded-xl shadow-sm p-6">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Editează oferta</h2>

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
            <ul class="list-disc list-inside text-sm text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('craftsman.quotes.update', [$quoteRequest, $quote]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                    Preț (lei) *
                </label>
                <input type="number" name="price" id="price" value="{{ old('price', $quote->price) }}" required min="1" step="0.01" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500" placeholder="Ex: 500">
                @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="price_max" class="block text-sm font-medium text-gray-700 mb-1">
                    Preț maxim (opțional, pentru interval)
                </label>
                <input type="number" name="price_max" id="price_max" value="{{ old('price_max', $quote->price_max) }}" min="1" step="0.01" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500" placeholder="Ex: 700">
                @error('price_max')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                Descriere ofertă *
            </label>
            <textarea name="description" id="description" rows="4" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500" placeholder="Descrie ce include oferta ta, condițiile, etc.">{{ old('description', $quote->description) }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="materials_included" class="block text-sm font-medium text-gray-700 mb-1">
                Materiale incluse (opțional)
            </label>
            <textarea name="materials_included" id="materials_included" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500" placeholder="Lista materialelor incluse în preț...">{{ old('materials_included', $quote->materials_included) }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="estimated_duration_days" class="block text-sm font-medium text-gray-700 mb-1">
                    Durată estimată (zile)
                </label>
                <input type="number" name="estimated_duration_days" id="estimated_duration_days" value="{{ old('estimated_duration_days', $quote->estimated_duration_days) }}" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label for="estimated_duration_hours" class="block text-sm font-medium text-gray-700 mb-1">
                    Durată estimată (ore)
                </label>
                <input type="number" name="estimated_duration_hours" id="estimated_duration_hours" value="{{ old('estimated_duration_hours', $quote->estimated_duration_hours) }}" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label for="available_from" class="block text-sm font-medium text-gray-700 mb-1">
                    Disponibil din
                </label>
                <input type="date" name="available_from" id="available_from" value="{{ old('available_from', $quote->available_from?->format('Y-m-d')) }}" min="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-1">
                    Ofertă valabilă până la
                </label>
                <input type="date" name="valid_until" id="valid_until" value="{{ old('valid_until', $quote->valid_until?->format('Y-m-d')) }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500">
            </div>
        </div>

        <div class="flex space-x-3">
            <button type="submit" class="flex-1 bg-primary-600 text-white py-3 rounded-lg font-semibold hover:bg-primary-700 transition">
                Salvează modificările
            </button>
            <a href="{{ route('craftsman.quotes.show', $quoteRequest) }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">
                Anulează
            </a>
        </div>
    </form>
</div>
@endsection
