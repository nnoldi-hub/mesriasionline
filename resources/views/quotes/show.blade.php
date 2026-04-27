@extends('layouts.app')

@section('title', 'Cerere: ' . $quoteRequest->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('quotes.index') }}" class="text-primary-600 hover:underline flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Înapoi la cereri
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
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
            @if($quoteRequest->status === 'pending')
                <form action="{{ route('quotes.cancel', $quoteRequest) }}" method="POST" onsubmit="return confirm('Sigur vrei să anulezi această cerere?')">
                    @csrf
                    <button type="submit" class="text-red-600 hover:underline text-sm">
                        Anulează cererea
                    </button>
                </form>
            @elseif($quoteRequest->status === 'accepted')
                <form action="{{ route('quotes.complete', $quoteRequest) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm">
                        ✓ Marchează ca finalizat
                    </button>
                </form>
            @endif
        </div>

        <div class="prose max-w-none mb-6">
            <p class="text-gray-700 whitespace-pre-wrap">{{ $quoteRequest->description }}</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Meseriaș:</span>
                <p class="font-medium">
                    <a href="{{ route('craftsman.show', $quoteRequest->craftsman->slug) }}" class="text-primary-600 hover:underline">
                        {{ $quoteRequest->craftsman->name }}
                    </a>
                </p>
            </div>
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
                    <span class="text-gray-500">Buget:</span>
                    <p class="font-medium">
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
                <span class="text-gray-500">Trimisă:</span>
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
                <h3 class="text-sm font-medium text-gray-700 mb-2">Imagini atașate:</h3>
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

    <!-- Quotes Received -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">
            Oferte primite
            @if($quoteRequest->quotes->count() > 0)
                <span class="text-sm font-normal text-gray-500">({{ $quoteRequest->quotes->count() }})</span>
            @endif
        </h2>

        @if($quoteRequest->quotes->isEmpty())
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-500">Încă nu ai primit nicio ofertă.</p>
                <p class="text-sm text-gray-400 mt-1">Meseriașul va fi notificat și va răspunde în curând.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($quoteRequest->quotes as $quote)
                    <div class="border rounded-lg p-4 {{ $quote->status === 'accepted' ? 'border-green-300 bg-green-50' : ($quote->status === 'rejected' ? 'border-gray-200 bg-gray-50 opacity-60' : 'border-gray-200') }}">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                    <span class="text-primary-600 font-semibold">
                                        {{ substr($quote->craftsman->name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $quote->craftsman->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $quote->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-green-600">{{ $quote->price_display }}</p>
                                @if($quote->duration_display)
                                    <p class="text-sm text-gray-500">Durată: {{ $quote->duration_display }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4">
                            <p class="text-gray-700 whitespace-pre-wrap">{{ $quote->description }}</p>
                        </div>

                        @if($quote->materials_included)
                            <div class="mt-3 p-3 bg-gray-100 rounded-lg">
                                <p class="text-sm font-medium text-gray-700">Materiale incluse:</p>
                                <p class="text-sm text-gray-600">{{ $quote->materials_included }}</p>
                            </div>
                        @endif

                        <div class="mt-4 flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                @if($quote->valid_until)
                                    Valabilă până la: {{ $quote->valid_until->format('d.m.Y') }}
                                @endif
                                @if($quote->available_from)
                                    | Disponibil din: {{ $quote->available_from->format('d.m.Y') }}
                                @endif
                            </div>

                            @if($quote->status === 'pending' && $quoteRequest->status !== 'accepted')
                                <div class="flex space-x-2">
                                    <form action="{{ route('quotes.accept', [$quoteRequest, $quote]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm">
                                            ✓ Accept oferta
                                        </button>
                                    </form>
                                    <form action="{{ route('quotes.reject', [$quoteRequest, $quote]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition text-sm">
                                            Refuz
                                        </button>
                                    </form>
                                </div>
                            @elseif($quote->status === 'accepted')
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">
                                    ✓ Ofertă acceptată
                                </span>
                            @elseif($quote->status === 'rejected')
                                <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-sm">
                                    Refuzată
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Contact Craftsman -->
    @if($quoteRequest->status === 'accepted')
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mt-6">
            <h3 class="font-semibold text-blue-900 mb-2">Ai acceptat oferta!</h3>
            <p class="text-blue-800 mb-4">Contactează meseriașul pentru a stabili detaliile:</p>
            <div class="flex space-x-3">
                <a href="{{ route('messages.create', ['craftsman' => $quoteRequest->craftsman->id]) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    💬 Trimite mesaj
                </a>
                @if($quoteRequest->craftsman->phone)
                    <a href="tel:{{ $quoteRequest->craftsman->phone }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        📞 {{ $quoteRequest->craftsman->phone }}
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
