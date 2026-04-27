@extends('layouts.app')

@section('title', 'Cerere ofertă nouă')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('quotes.index') }}" class="text-primary-600 hover:underline flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Înapoi la cereri
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Cere o ofertă</h1>

        @if($craftsman)
            <div class="bg-gray-50 rounded-lg p-4 mb-6 flex items-center space-x-4">
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                    <span class="text-primary-600 font-semibold text-lg">
                        {{ substr($craftsman->name, 0, 1) }}
                    </span>
                </div>
                <div>
                    <p class="font-medium text-gray-900">
                        {{ $craftsman->name }}
                        @if($craftsman->is_verified)
                            <svg class="w-4 h-4 inline-block text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </p>
                    <p class="text-sm text-gray-500">{{ $craftsman->category?->name ?? 'Meseriaș' }}</p>
                </div>
            </div>
        @endif

        <form action="{{ route('quotes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            @if($craftsman)
                <input type="hidden" name="craftsman_id" value="{{ $craftsman->id }}">
            @else
                <div class="mb-4">
                    <label for="craftsman_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Selectează meseriaș *
                    </label>
                    <select name="craftsman_id" id="craftsman_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">-- Alege un meseriaș --</option>
                        @php
                            $craftsmen = \App\Models\User::where('role', 'specialist')->where('is_active', true)->with('category')->get();
                        @endphp
                        @foreach($craftsmen as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} - {{ $c->category?->name ?? 'Meseriaș' }}</option>
                        @endforeach
                    </select>
                    @error('craftsman_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            @if($craftsman && $craftsman->services->count() > 0)
                <div class="mb-4">
                    <label for="service_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Serviciu (opțional)
                    </label>
                    <select name="service_id" id="service_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">-- General / Alt serviciu --</option>
                        @foreach($craftsman->services->where('is_active', true) as $s)
                            <option value="{{ $s->id }}" {{ $service && $service->id == $s->id ? 'selected' : '' }}>
                                {{ $s->name }} - de la {{ number_format($s->price, 0, ',', '.') }} lei
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                    Titlu cerere *
                </label>
                <input type="text" name="title" id="title" value="{{ old('title', $service?->name) }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="Ex: Montare priză în bucătărie">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Descriere detaliată *
                </label>
                <textarea name="description" id="description" rows="5" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="Descrie cât mai detaliat ce ai nevoie...">{{ old('description') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Include detalii despre dimensiuni, materiale dorite, starea actuală, etc.</p>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                        Locație
                    </label>
                    <input type="text" name="location" id="location" value="{{ old('location') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="Ex: București, Sector 3">
                    @error('location')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="urgency" class="block text-sm font-medium text-gray-700 mb-1">
                        Urgență *
                    </label>
                    <select name="urgency" id="urgency" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="low" {{ old('urgency') == 'low' ? 'selected' : '' }}>Scăzută - pot aștepta 2+ săptămâni</option>
                        <option value="normal" {{ old('urgency', 'normal') == 'normal' ? 'selected' : '' }}>Normală - în următoarea săptămână</option>
                        <option value="high" {{ old('urgency') == 'high' ? 'selected' : '' }}>Ridicată - în 2-3 zile</option>
                        <option value="urgent" {{ old('urgency') == 'urgent' ? 'selected' : '' }}>Urgentă - cât mai curând posibil</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="preferred_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Data preferată
                    </label>
                    <input type="date" name="preferred_date" id="preferred_date" value="{{ old('preferred_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    @error('preferred_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="preferred_time" class="block text-sm font-medium text-gray-700 mb-1">
                        Intervalul orar preferat
                    </label>
                    <select name="preferred_time" id="preferred_time" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">-- Orice interval --</option>
                        <option value="morning" {{ old('preferred_time') == 'morning' ? 'selected' : '' }}>Dimineața (8:00 - 12:00)</option>
                        <option value="afternoon" {{ old('preferred_time') == 'afternoon' ? 'selected' : '' }}>După-amiaza (12:00 - 18:00)</option>
                        <option value="evening" {{ old('preferred_time') == 'evening' ? 'selected' : '' }}>Seara (18:00 - 21:00)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="budget_min" class="block text-sm font-medium text-gray-700 mb-1">
                        Buget minim (lei)
                    </label>
                    <input type="number" name="budget_min" id="budget_min" value="{{ old('budget_min') }}" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="0">
                </div>
                <div>
                    <label for="budget_max" class="block text-sm font-medium text-gray-700 mb-1">
                        Buget maxim (lei)
                    </label>
                    <input type="number" name="budget_max" id="budget_max" value="{{ old('budget_max') }}" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="0">
                </div>
            </div>

            <div class="mb-6">
                <label for="images" class="block text-sm font-medium text-gray-700 mb-1">
                    Imagini (opțional)
                </label>
                <input type="file" name="images[]" id="images" multiple accept="image/*" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Poți adăuga până la 5 imagini. Formate acceptate: JPG, PNG. Max 5MB/imagine.</p>
                @error('images.*')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Google reCAPTCHA --}}
            <x-recaptcha />

            <button type="submit" class="w-full bg-primary-600 text-white py-3 rounded-lg font-semibold hover:bg-primary-700 transition">
                Trimite cererea de ofertă
            </button>
        </form>
    </div>
</div>
@endsection
