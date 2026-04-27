@extends('layouts.craftsman')

@section('title', 'Cerere: ' . $quoteRequest->title)
@section('page-title', 'Detalii Cerere Ofertă')

@section('content')
<div class="mb-6">
    <a href="{{ route('craftsman.quotes.index') }}" class="text-primary-600 hover:underline flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Înapoi la cereri
    </a>
</div>

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
        <p class="text-sm text-red-700">{{ session('error') }}</p>
    </div>
@endif

<!-- Request Details -->
<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <div class="flex items-start justify-between mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $quoteRequest->title }}</h1>
            <div class="flex items-center space-x-3 mt-2">
                <span class="px-3 py-1 text-sm font-medium rounded-full bg-{{ $quoteRequest->status_color }}-100 text-{{ $quoteRequest->status_color }}-700">
                    {{ $quoteRequest->status_label }}
                </span>
                <span class="px-3 py-1 text-sm font-medium rounded-full bg-{{ $quoteRequest->urgency_color }}-100 text-{{ $quoteRequest->urgency_color }}-700">
                    Urgență: {{ $quoteRequest->urgency_label }}
                </span>
            </div>
        </div>
    </div>

    <!-- Client Info -->
    <div class="bg-gray-50 rounded-lg p-4 mb-4">
        <h3 class="text-sm font-medium text-gray-700 mb-2">Client</h3>
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                <span class="text-primary-600 font-semibold">{{ substr($quoteRequest->client->name, 0, 1) }}</span>
            </div>
            <div>
                <p class="font-medium text-gray-900">{{ $quoteRequest->client->name }}</p>
                @if($quoteRequest->client->phone)
                    <p class="text-sm text-gray-500">{{ $quoteRequest->client->phone }}</p>
                @endif
            </div>
            <a href="{{ route('messages.create', ['craftsman' => $quoteRequest->client->id]) }}" class="ml-auto text-primary-600 hover:underline text-sm">
                💬 Trimite mesaj
            </a>
        </div>
    </div>

    <div class="prose max-w-none mb-6">
        <h3 class="text-sm font-medium text-gray-700 mb-2">Descriere</h3>
        <p class="text-gray-700 whitespace-pre-wrap bg-gray-50 p-4 rounded-lg">{{ $quoteRequest->description }}</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        @if($quoteRequest->location)
            <div>
                <span class="text-gray-500">Locație:</span>
                <p class="font-medium">{{ $quoteRequest->location }}</p>
            </div>
        @endif
        @if($quoteRequest->preferred_date)
            <div>
                <span class="text-gray-500">Data preferată:</span>
                <p class="font-medium">{{ $quoteRequest->preferred_date->format('d.m.Y') }}</p>
            </div>
        @endif
        @if($quoteRequest->preferred_time_label)
            <div>
                <span class="text-gray-500">Interval orar:</span>
                <p class="font-medium">{{ $quoteRequest->preferred_time_label }}</p>
            </div>
        @endif
        @if($quoteRequest->budget_min || $quoteRequest->budget_max)
            <div>
                <span class="text-gray-500">Buget client:</span>
                <p class="font-medium text-green-600">
                    @if($quoteRequest->budget_min && $quoteRequest->budget_max)
                        {{ number_format($quoteRequest->budget_min, 0, ',', '.') }} - {{ number_format($quoteRequest->budget_max, 0, ',', '.') }} lei
                    @elseif($quoteRequest->budget_max)
                        max. {{ number_format($quoteRequest->budget_max, 0, ',', '.') }} lei
                    @else
                        min. {{ number_format($quoteRequest->budget_min, 0, ',', '.') }} lei
                    @endif
                </p>
            </div>
        @endif
        <div>
            <span class="text-gray-500">Primită:</span>
            <p class="font-medium">{{ $quoteRequest->created_at->format('d.m.Y H:i') }}</p>
        </div>
        @if($quoteRequest->expires_at)
            <div>
                <span class="text-gray-500">Expiră:</span>
                <p class="font-medium {{ $quoteRequest->expires_at->isPast() ? 'text-red-600' : '' }}">
                    {{ $quoteRequest->expires_at->format('d.m.Y') }}
                </p>
            </div>
        @endif
    </div>

    @if($quoteRequest->images && count($quoteRequest->images) > 0)
        <div class="mt-6">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Imagini de la client:</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($quoteRequest->images as $image)
                    <a href="{{ asset('storage/' . $image) }}" target="_blank">
                        <img src="{{ asset('storage/' . $image) }}" alt="Imagine" class="w-24 h-24 object-cover rounded-lg border hover:opacity-75 transition">
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- My Quote or Quote Form -->
@if($myQuote)
    <!-- Show existing quote -->
    <div class="bg-white rounded-xl shadow-sm p-6 {{ $myQuote->status === 'accepted' ? 'border-2 border-green-500' : '' }}">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900">Oferta ta</h2>
            <span class="px-3 py-1 text-sm font-medium rounded-full bg-{{ $myQuote->status_color }}-100 text-{{ $myQuote->status_color }}-700">
                {{ $myQuote->status_label }}
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            <div>
                <span class="text-gray-500 text-sm">Preț oferit:</span>
                <p class="text-2xl font-bold text-green-600">{{ $myQuote->price_display }}</p>
            </div>
            @if($myQuote->duration_display)
                <div>
                    <span class="text-gray-500 text-sm">Durată estimată:</span>
                    <p class="font-medium">{{ $myQuote->duration_display }}</p>
                </div>
            @endif
            @if($myQuote->available_from)
                <div>
                    <span class="text-gray-500 text-sm">Disponibil din:</span>
                    <p class="font-medium">{{ $myQuote->available_from->format('d.m.Y') }}</p>
                </div>
            @endif
            @if($myQuote->valid_until)
                <div>
                    <span class="text-gray-500 text-sm">Valabilă până:</span>
                    <p class="font-medium">{{ $myQuote->valid_until->format('d.m.Y') }}</p>
                </div>
            @endif
        </div>

        <div class="bg-gray-50 p-4 rounded-lg mb-4">
            <p class="text-gray-700 whitespace-pre-wrap">{{ $myQuote->description }}</p>
        </div>

        @if($myQuote->materials_included)
            <div class="mb-4">
                <span class="text-gray-500 text-sm">Materiale incluse:</span>
                <p class="text-gray-700">{{ $myQuote->materials_included }}</p>
            </div>
        @endif

        @if($myQuote->status === 'pending')
            <div class="flex space-x-3">
                <a href="{{ route('craftsman.quotes.edit', [$quoteRequest, $myQuote]) }}" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                    Editează oferta
                </a>
                <form action="{{ route('craftsman.quotes.withdraw', [$quoteRequest, $myQuote]) }}" method="POST" onsubmit="return confirm('Sigur vrei să retragi oferta?')">
                    @csrf
                    <button type="submit" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                        Retrage oferta
                    </button>
                </form>
            </div>
        @endif

        @if($myQuote->status === 'accepted')
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-4">
                <h3 class="font-semibold text-green-900 mb-2">🎉 Oferta ta a fost acceptată!</h3>
                <p class="text-green-800 mb-3">Contactează clientul pentru a stabili detaliile:</p>
                <div class="flex space-x-3">
                    <a href="{{ route('messages.create', ['craftsman' => $quoteRequest->client->id]) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        💬 Trimite mesaj
                    </a>
                    @if($quoteRequest->client->phone)
                        <a href="tel:{{ $quoteRequest->client->phone }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            📞 {{ $quoteRequest->client->phone }}
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
@elseif($quoteRequest->canReceiveQuotes())
    <!-- Quote Form -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Trimite o ofertă</h2>

        <form action="{{ route('craftsman.quotes.store', $quoteRequest) }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                        Preț (lei) *
                    </label>
                    <input type="number" name="price" id="price" value="{{ old('price') }}" required min="1" step="0.01" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500" placeholder="Ex: 500">
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="price_max" class="block text-sm font-medium text-gray-700 mb-1">
                        Preț maxim (opțional, pentru interval)
                    </label>
                    <input type="number" name="price_max" id="price_max" value="{{ old('price_max') }}" min="1" step="0.01" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500" placeholder="Ex: 700">
                    @error('price_max')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Descriere ofertă *
                </label>
                <textarea name="description" id="description" rows="4" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500" placeholder="Descrie ce include oferta ta, condițiile, etc.">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="materials_included" class="block text-sm font-medium text-gray-700 mb-1">
                    Materiale incluse (opțional)
                </label>
                <textarea name="materials_included" id="materials_included" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500" placeholder="Lista materialelor incluse în preț...">{{ old('materials_included') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="estimated_duration_days" class="block text-sm font-medium text-gray-700 mb-1">
                        Durată estimată (zile)
                    </label>
                    <input type="number" name="estimated_duration_days" id="estimated_duration_days" value="{{ old('estimated_duration_days') }}" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label for="estimated_duration_hours" class="block text-sm font-medium text-gray-700 mb-1">
                        Durată estimată (ore)
                    </label>
                    <input type="number" name="estimated_duration_hours" id="estimated_duration_hours" value="{{ old('estimated_duration_hours') }}" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="available_from" class="block text-sm font-medium text-gray-700 mb-1">
                        Disponibil din
                    </label>
                    <input type="date" name="available_from" id="available_from" value="{{ old('available_from') }}" min="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-1">
                        Ofertă valabilă până la
                    </label>
                    <input type="date" name="valid_until" id="valid_until" value="{{ old('valid_until', date('Y-m-d', strtotime('+7 days'))) }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <button type="submit" class="w-full bg-primary-600 text-white py-3 rounded-lg font-semibold hover:bg-primary-700 transition">
                Trimite oferta
            </button>
        </form>
    </div>
@else
    <div class="bg-gray-50 rounded-xl p-6 text-center">
        <p class="text-gray-500">Această cerere nu mai poate primi oferte.</p>
    </div>
@endif
@endsection
