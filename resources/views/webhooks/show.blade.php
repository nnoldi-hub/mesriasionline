@extends('layouts.app')

@section('title', 'Detalii Webhook')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('webhooks.index') }}" class="text-blue-600 hover:text-blue-800">← Înapoi la Webhooks</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Webhook Info -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h1 class="text-3xl font-bold">{{ $webhook->name }}</h1>
                        <p class="text-gray-600 mt-1">ID: {{ $webhook->id }}</p>
                    </div>
                    <div class="flex space-x-2">
                        <form action="{{ route('webhooks', $webhook) }}/toggle-active" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-lg transition {{ $webhook->is_active ? 'bg-gray-200 hover:bg-gray-300 text-gray-800' : 'bg-green-500 hover:bg-green-600 text-white' }}">
                                {{ $webhook->is_active ? 'Dezactivează' : 'Activează' }}
                            </button>
                        </form>
                        <a href="{{ route('webhooks.edit', $webhook) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
                            Editează
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        @if($webhook->is_active)
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                Activ
                            </span>
                        @else
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                                Inactiv
                            </span>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Ultima trimitere</p>
                        <p class="font-medium">{{ $webhook->last_triggered_at ? $webhook->last_triggered_at->diffForHumans() : 'Niciodată' }}</p>
                    </div>
                </div>

                <div class="mb-6">
                    <p class="text-sm text-gray-600 mb-2">URL Endpoint</p>
                    <div class="flex items-center space-x-2">
                        <code class="flex-1 bg-gray-100 px-3 py-2 rounded text-sm">{{ $webhook->url }}</code>
                        <form action="{{ route('webhooks', $webhook) }}/test" method="POST">
                            @csrf
                            <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg text-sm transition">
                                Testează
                            </button>
                        </form>
                    </div>
                </div>

                <div>
                    <p class="text-sm text-gray-600 mb-2">Evenimente Monitorizate</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($webhook->events as $event)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                {{ \App\Models\Webhook::getAvailableEvents()[$event] ?? $event }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Delivery History -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Istoric Trimiteri</h2>

                @if($deliveries->isEmpty())
                    <p class="text-gray-500 text-center py-8">Nu există încă trimiteri pentru acest webhook.</p>
                @else
                    <div class="space-y-3">
                        @foreach($deliveries as $delivery)
                            <div class="border border-gray-200 rounded-lg p-4 {{ $delivery->success ? 'bg-green-50' : 'bg-red-50' }}">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-semibold">{{ \App\Models\Webhook::getAvailableEvents()[$delivery->event_type] ?? $delivery->event_type }}</span>
                                            @if($delivery->success)
                                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">Succes</span>
                                            @else
                                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-semibold">Eșuat</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600">{{ $delivery->created_at->format('d.m.Y H:i:s') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium">{{ $delivery->response_status ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-600">{{ $delivery->attempts }} încercări</p>
                                    </div>
                                </div>

                                @if($delivery->error_message)
                                    <div class="mt-2 p-2 bg-red-100 rounded text-sm text-red-800">
                                        <strong>Eroare:</strong> {{ $delivery->error_message }}
                                    </div>
                                @endif

                                @if(!$delivery->success)
                                    <form action="{{ route('webhook-deliveries', $delivery) }}/retry" method="POST" class="mt-2">
                                        @csrf
                                        <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                                            Încearcă din nou
                                        </button>
                                    </form>
                                @endif

                                <details class="mt-2">
                                    <summary class="text-sm text-gray-600 cursor-pointer hover:text-gray-900">Detalii Payload</summary>
                                    <pre class="mt-2 bg-gray-900 text-gray-100 p-3 rounded text-xs overflow-x-auto">{{ json_encode($delivery->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </details>
                            </div>
                        @endforeach>
                    </div>

                    <div class="mt-4">
                        {{ $deliveries->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Statistics Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-bold mb-4">Statistici (Ultimele 30 zile)</h2>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600">Total Trimiteri</p>
                        <p class="text-2xl font-bold">{{ $statistics['total_deliveries'] ?? 0 }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Rate Succes</p>
                        <div class="flex items-baseline space-x-2">
                            <p class="text-2xl font-bold text-green-600">{{ $statistics['success_rate'] ?? 0 }}%</p>
                        </div>
                        <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-green-500" style="width: {{ $statistics['success_rate'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-green-50 p-3 rounded">
                            <p class="text-xs text-gray-600">Succese</p>
                            <p class="text-xl font-bold text-green-600">{{ $webhook->success_count }}</p>
                        </div>
                        <div class="bg-red-50 p-3 rounded">
                            <p class="text-xs text-gray-600">Eșecuri</p>
                            <p class="text-xl font-bold text-red-600">{{ $webhook->failure_count }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 mb-2">Timp Mediu Răspuns</p>
                        <p class="text-lg font-semibold">{{ $statistics['avg_response_time'] ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="font-semibold mb-3">Informații Securitate</h3>
                <p class="text-sm text-gray-700 mb-3">
                    Acest webhook folosește HMAC SHA-256 pentru semnarea request-urilor.
                    Verifică header-ul <code class="bg-gray-200 px-1 rounded">X-Webhook-Signature</code>.
                </p>
                <a href="https://docs.meseriasi.ro/webhooks" target="_blank" class="text-sm text-blue-600 hover:text-blue-800">
                    Documentație →
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
