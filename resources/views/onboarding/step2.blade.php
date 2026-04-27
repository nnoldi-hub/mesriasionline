@extends('layouts.onboarding')

@section('title', 'Pasul 2 — Primul serviciu')

@section('content')
<div class="text-center mb-6">
    <h2 class="text-xl font-bold text-gray-900">Ce serviciu oferi?</h2>
    <p class="text-sm text-gray-500 mt-1">Adaugă un serviciu — poți oricând adăuga mai multe din dashboard</p>
</div>

{{-- Sugestii rapide --}}
<div class="mb-5">
    <p class="text-xs font-medium text-gray-500 mb-2">Sugestii rapide:</p>
    <div class="flex flex-wrap gap-2" id="suggestions">
        @foreach(['Montaj priză', 'Instalare canalizare', 'Glet + lavabil', 'Montaj gresie', 'Reparații generale', 'Tablou electric'] as $sug)
            <button type="button" onclick="fillSuggestion('{{ $sug }}')"
                class="px-3 py-1 text-xs bg-gray-100 hover:bg-primary-50 hover:text-primary-700 border border-gray-200 hover:border-primary-300 rounded-full transition-colors">
                {{ $sug }}
            </button>
        @endforeach
    </div>
</div>

<form method="POST" action="{{ route('onboarding.save', ['step' => 2]) }}" class="space-y-5">
    @csrf
    @method('PUT')

    <div>
        <label for="service_name" class="block text-sm font-medium text-gray-700 mb-1">
            Denumire serviciu <span class="text-red-500">*</span>
        </label>
        <input type="text" id="service_name" name="service_name" value="{{ old('service_name') }}" required autofocus
            placeholder="ex: Montaj prize și întrerupătoare"
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('service_name') border-red-400 @enderror">
        @error('service_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="service_price" class="block text-sm font-medium text-gray-700 mb-1">
            Preț (RON) <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <input type="number" id="service_price" name="service_price" value="{{ old('service_price') }}" required
                min="0" step="0.01" placeholder="150"
                class="w-full px-4 py-2.5 pr-14 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('service_price') border-red-400 @enderror">
            <span class="absolute right-4 top-3 text-gray-400 text-sm">RON</span>
        </div>
        <p class="mt-1 text-xs text-gray-400">Poți pune un preț orientativ, modificabil oricând.</p>
        @error('service_price')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="service_desc" class="block text-sm font-medium text-gray-700 mb-1">
            Descriere scurtă <span class="text-gray-400">(opțional)</span>
        </label>
        <textarea id="service_desc" name="service_desc" rows="2"
            placeholder="ex: Include materiale, garanție 6 luni..."
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 resize-none">{{ old('service_desc') }}</textarea>
    </div>

    <button type="submit"
        class="w-full py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors">
        Continuă →
    </button>
</form>

<script>
function fillSuggestion(text) {
    document.getElementById('service_name').value = text;
    document.getElementById('service_name').focus();
}
</script>
@endsection
