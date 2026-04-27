@extends('layouts.app')

@section('title', 'Creează Webhook')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('webhooks.index') }}" class="text-blue-600 hover:text-blue-800">← Înapoi la Webhooks</a>
    </div>

    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Creează Webhook Nou</h1>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-blue-900 mb-2">Ce sunt Webhooks?</h3>
            <p class="text-sm text-blue-800">
                Webhooks îți permit să primești notificări în timp real când anumite evenimente au loc pe platformă.
                Vei primi un POST request cu datele evenimentului la URL-ul configurat.
            </p>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('webhooks.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
            @csrf

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nume Webhook *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">Un nume descriptiv pentru a identifica webhook-ul</p>
            </div>

            <div class="mb-6">
                <label for="url" class="block text-sm font-medium text-gray-700 mb-2">URL Endpoint *</label>
                <input type="url" name="url" id="url" value="{{ old('url') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    placeholder="https://example.com/webhook">
                <p class="text-xs text-gray-500 mt-1">URL-ul unde vei primi notificările (trebuie să înceapă cu https://)</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Evenimente *</label>
                <p class="text-xs text-gray-500 mb-3">Selectează evenimentele pentru care vrei să primești notificări</p>
                
                <div class="space-y-2 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-4">
                    @foreach($availableEvents as $event => $label)
                        <div class="flex items-start">
                            <input type="checkbox" name="events[]" id="event_{{ $loop->index }}" value="{{ $event }}"
                                {{ in_array($event, old('events', [])) ? 'checked' : '' }}
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
                <label for="secret" class="block text-sm font-medium text-gray-700 mb-2">Secret (opțional)</label>
                <input type="text" name="secret" id="secret" value="{{ old('secret') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Lasă gol pentru generare automată">
                <p class="text-xs text-gray-500 mt-1">
                    Dacă lași acest câmp gol, va fi generat automat un secret sigur.
                    Secretul este folosit pentru a verifica autenticitatea notificărilor (HMAC SHA-256).
                </p>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-700">Activează webhook-ul imediat</span>
                </label>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('webhooks.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Anulează
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition">
                    Creează Webhook
                </button>
            </div>
        </form>

        <div class="mt-8 bg-gray-50 rounded-lg p-6">
            <h3 class="font-semibold text-gray-900 mb-3">Exemplu Payload</h3>
            <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code>{
  "event": "appointment.created",
  "timestamp": "2026-01-14T12:00:00+00:00",
  "data": {
    "id": 123,
    "specialist_id": 45,
    "client_name": "Ion Popescu",
    "appointment_date": "2026-01-20",
    "appointment_time": "14:00:00",
    "status": "pending"
  }
}</code></pre>
            <p class="text-sm text-gray-600 mt-3">
                <strong>Headers trimise:</strong> X-Webhook-Event, X-Webhook-ID, X-Webhook-Signature
            </p>
        </div>
    </div>
</div>
@endsection
