@extends('layouts.app')

@section('title', 'Editează Webhook')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('webhooks.show', $webhook) }}" class="text-blue-600 hover:text-blue-800">← Înapoi la Detalii</a>
    </div>

    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Editează Webhook</h1>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('webhooks.update', $webhook) }}" method="POST" class="bg-white rounded-lg shadow p-6">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nume Webhook *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $webhook->name) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="mb-6">
                <label for="url" class="block text-sm font-medium text-gray-700 mb-2">URL Endpoint *</label>
                <input type="url" name="url" id="url" value="{{ old('url', $webhook->url) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Evenimente *</label>
                
                <div class="space-y-2 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-4">
                    @foreach($availableEvents as $event => $label)
                        <div class="flex items-start">
                            <input type="checkbox" name="events[]" id="event_{{ $loop->index }}" value="{{ $event }}"
                                {{ in_array($event, old('events', $webhook->events)) ? 'checked' : '' }}
                                class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="event_{{ $loop->index }}" class="ml-2 text-sm text-gray-700">
                                <span class="font-medium">{{ $label }}</span>
                                <span class="block text-xs text-gray-500">{{ $event }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $webhook->is_active) ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-700">Webhook activ</span>
                </label>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('webhooks.show', $webhook) }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Anulează
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition">
                    Salvează Modificări
                </button>
            </div>
        </form>

        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <h3 class="font-semibold text-yellow-900 mb-3">Gestionare Secret</h3>
            <p class="text-sm text-yellow-800 mb-4">
                Secretul nu poate fi modificat direct. Dacă ai nevoie să-l schimbi, poți genera unul nou.
                <strong>Atenție:</strong> Acest lucru va invalida secretul curent!
            </p>
            <form action="{{ route('webhooks', $webhook) }}/regenerate-secret" method="POST" 
                onsubmit="return confirm('Sigur vrei să regenerezi secretul? Secretul curent va fi invalidat.')">
                @csrf
                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm transition">
                    Regenerează Secret
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
