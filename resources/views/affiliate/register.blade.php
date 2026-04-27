@extends('layouts.app')

@section('title', 'Program Afiliere - Fixacasa')

@section('content')
<div class="max-w-4xl mx-auto py-12 px-4">
    <div class="text-center mb-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Programul de Afiliere Fixacasa</h1>
        <p class="text-lg text-gray-600">Câștigă bani recomandând Fixacasa prietenilor și cunoscuților tăi.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-4 mb-6">
            {{ session('info') }}
        </div>
    @endif

    <!-- Benefits -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">{{ $program?->commission_value ?? 10 }}% Comision</h3>
            <p class="text-sm text-gray-600">Primești comision pentru fiecare utilizator care se înregistrează prin link-ul tău.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">Simplu de Folosit</h3>
            <p class="text-sm text-gray-600">Primești un link unic pe care îl poți distribui oriunde.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">Plăți Rapide</h3>
            <p class="text-sm text-gray-600">Retrageri prin IBAN, PayPal sau Revolut de la {{ $program?->min_payout ?? 100 }} lei.</p>
        </div>
    </div>

    <!-- Registration Form -->
    <div class="bg-white rounded-xl shadow-sm p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Înscrie-te în Programul de Afiliere</h2>

        <form action="{{ route('affiliate.register') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Metodă de plată</label>
                    <select name="payment_method" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Selectează...</option>
                        <option value="iban" {{ old('payment_method') === 'iban' ? 'selected' : '' }}>Transfer bancar (IBAN)</option>
                        <option value="paypal" {{ old('payment_method') === 'paypal' ? 'selected' : '' }}>PayPal</option>
                        <option value="revolut" {{ old('payment_method') === 'revolut' ? 'selected' : '' }}>Revolut</option>
                    </select>
                    @error('payment_method')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Detalii plată</label>
                    <input type="text" name="payment_details" value="{{ old('payment_details') }}" required 
                        placeholder="IBAN, email PayPal sau @tag Revolut"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-primary-500 focus:border-primary-500">
                    @error('payment_details')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label class="flex items-start">
                    <input type="checkbox" name="terms_accepted" value="1" required class="mt-1 mr-3 rounded text-primary-600">
                    <span class="text-sm text-gray-600">
                        Accept <a href="{{ route('terms') }}" target="_blank" class="text-primary-600 hover:underline">termenii și condițiile</a> 
                        programului de afiliere Fixacasa.
                    </span>
                </label>
                @error('terms_accepted')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-primary-600 text-white py-3 rounded-lg font-semibold hover:bg-primary-700 transition">
                Înscrie-te acum
            </button>
        </form>
    </div>

    <!-- How it Works -->
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Cum Funcționează?</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="w-12 h-12 bg-primary-600 text-white rounded-full flex items-center justify-center mx-auto mb-3 text-xl font-bold">1</div>
                <h3 class="font-semibold text-gray-900 mb-1">Înscrie-te</h3>
                <p class="text-sm text-gray-600">Completează formularul de mai sus</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 bg-primary-600 text-white rounded-full flex items-center justify-center mx-auto mb-3 text-xl font-bold">2</div>
                <h3 class="font-semibold text-gray-900 mb-1">Primești link-ul</h3>
                <p class="text-sm text-gray-600">După aprobare, primești link-ul unic</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 bg-primary-600 text-white rounded-full flex items-center justify-center mx-auto mb-3 text-xl font-bold">3</div>
                <h3 class="font-semibold text-gray-900 mb-1">Distribuie</h3>
                <p class="text-sm text-gray-600">Partajează link-ul pe social media</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 bg-primary-600 text-white rounded-full flex items-center justify-center mx-auto mb-3 text-xl font-bold">4</div>
                <h3 class="font-semibold text-gray-900 mb-1">Câștigă</h3>
                <p class="text-sm text-gray-600">Primești comision pentru fiecare referral</p>
            </div>
        </div>
    </div>
</div>
@endsection
