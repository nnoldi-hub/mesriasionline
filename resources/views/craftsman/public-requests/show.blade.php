@extends('layouts.craftsman')

@section('title', 'Cerere: ' . $publicJobRequest->title)
@section('page-title', 'Detalii Cerere Client')

@section('content')
<div class="mb-4">
    <a href="{{ route('craftsman.public-requests.index') }}" class="text-primary-600 hover:underline flex items-center text-sm">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Înapoi la cereri
    </a>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Coloana stângă: detalii cerere --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Header cerere --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <div class="flex items-center space-x-2 mb-2">
                        @if($publicJobRequest->urgency === 'urgent')
                            <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-red-100 text-red-700">🔥 Urgent</span>
                        @elseif($publicJobRequest->urgency === 'this_week')
                            <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-yellow-100 text-yellow-700">⚡ Săptămâna aceasta</span>
                        @else
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600">Flexibil</span>
                        @endif
                        @if($publicJobRequest->status !== 'open')
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-200 text-gray-600">Închisă</span>
                        @endif
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $publicJobRequest->title }}</h2>
                    <p class="text-sm text-gray-400 mt-1">Postată {{ $publicJobRequest->created_at->diffForHumans() }}</p>
                </div>
            </div>

            <div class="prose prose-sm max-w-none text-gray-700 mt-4">
                <p>{{ $publicJobRequest->description }}</p>
            </div>
        </div>

        {{-- Detalii --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Detalii cerere</h3>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-gray-500">Categorie</dt>
                    <dd class="font-medium text-gray-800">{{ $publicJobRequest->category?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Locație</dt>
                    <dd class="font-medium text-gray-800">{{ $publicJobRequest->location_display }}</dd>
                </div>
                @if($publicJobRequest->preferred_date)
                    <div>
                        <dt class="text-gray-500">Data preferată</dt>
                        <dd class="font-medium text-gray-800">{{ $publicJobRequest->preferred_date->format('d.m.Y') }}</dd>
                    </div>
                @endif
                @if($publicJobRequest->budget_max)
                    <div>
                        <dt class="text-gray-500">Buget maxim</dt>
                        <dd class="font-medium text-green-700">{{ number_format($publicJobRequest->budget_max, 0, ',', '.') }} lei</dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>

    {{-- Coloana dreaptă: acțiuni --}}
    <div class="space-y-4">

        @if($myResponse && $myResponse->action === 'interested')
            {{-- Ai răspuns deja --}}
            <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center">
                <div class="text-3xl mb-2">✅</div>
                <h3 class="font-bold text-green-900 mb-1">Ai trimis oferta!</h3>
                <p class="text-sm text-green-700">Clientul a primit datele tale de contact pe email. Așteaptă să te sune.</p>
                @if($myResponse->message)
                    <div class="mt-3 text-left bg-white rounded-lg p-3 text-sm text-gray-700">
                        <p class="text-xs text-gray-400 mb-1">Mesajul tău:</p>
                        {{ $myResponse->message }}
                    </div>
                @endif
            </div>

        @elseif($publicJobRequest->status !== 'open')
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 text-center">
                <p class="text-gray-500 text-sm">Această cerere nu mai este activă.</p>
            </div>

        @else
            {{-- Formular ofertă --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Contactează clientul</h3>

                <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 mb-4 text-xs text-blue-800">
                    <strong>Cum funcționează:</strong> Când apesi „Sunt interesat", clientul primește email cu numărul tău de telefon și linkul profilului tău. El te va contacta.
                </div>

                <form action="{{ route('craftsman.public-requests.respond', $publicJobRequest->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="interested">

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mesaj pentru client (opțional)</label>
                        <textarea name="message" rows="3" maxlength="1000"
                            placeholder="Ex: Pot veni să evaluez lucrarea miercuri dimineață. Am 10 ani experiență în instalații electrice."
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('message') }}</textarea>
                    </div>

                    <button type="submit"
                        class="w-full text-white font-bold py-3 px-4 rounded-xl transition hover:opacity-90"
                        style="background-color: #C0392B;">
                        🙋 Sunt interesat — Contactează-mă
                    </button>
                </form>

                <form action="{{ route('craftsman.public-requests.respond', $publicJobRequest->id) }}" method="POST" class="mt-3">
                    @csrf
                    <input type="hidden" name="action" value="not_interested">
                    <button type="submit" class="w-full text-gray-500 text-sm py-2 rounded-xl hover:bg-gray-50 transition">
                        Nu mă interesează
                    </button>
                </form>
            </div>
        @endif

        {{-- Info meseriaș --}}
        <div class="bg-white rounded-xl shadow-sm p-6 text-sm">
            <h4 class="font-semibold text-gray-700 mb-3">Profilul tău</h4>
            <p class="text-gray-500 text-xs mb-2">Clientul va vedea aceste informații:</p>
            <div class="space-y-1 text-gray-700">
                <p><strong>Nume:</strong> {{ auth()->user()->name }}</p>
                <p><strong>Telefon:</strong> {{ auth()->user()->phone ?? '—' }}</p>
                <p><strong>Categorie:</strong> {{ auth()->user()->category?->name ?? '—' }}</p>
            </div>
            @if(!auth()->user()->phone)
                <a href="{{ route('craftsman.profile') }}" class="inline-block mt-3 text-xs text-blue-600 hover:underline">
                    ⚠️ Adaugă numărul de telefon în profil
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
