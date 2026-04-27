@extends('layouts.app')

@section('title', 'Webhooks')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Webhooks</h1>
        <a href="{{ route('webhooks.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition">
            Adaugă Webhook Nou
        </a>
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

    @if(session('webhook_secret'))
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded mb-4">
            <strong>Secret Nou:</strong> <code class="bg-yellow-200 px-2 py-1 rounded">{{ session('webhook_secret') }}</code>
            <p class="text-sm mt-2">Salvează acest secret în siguranță! Nu va mai fi afișat din nou.</p>
        </div>
    @endif

    @if($webhooks->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">Nu există webhooks</h3>
            <p class="mt-1 text-gray-500">Începe prin a crea primul tău webhook pentru a primi notificări.</p>
            <a href="{{ route('webhooks.create') }}" class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition">
                Creează Webhook
            </a>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nume</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">URL</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Evenimente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statistici</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acțiuni</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($webhooks as $webhook)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $webhook->name }}</div>
                                @if($webhook->last_triggered_at)
                                    <div class="text-xs text-gray-500">
                                        Ultima trimitere: {{ $webhook->last_triggered_at->diffForHumans() }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 truncate max-w-xs">{{ $webhook->url }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ count($webhook->events) }} evenimente</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($webhook->is_active)
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Activ
                                    </span>
                                @else
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Inactiv
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center space-x-2">
                                    <span class="text-green-600">✓ {{ $webhook->success_count }}</span>
                                    <span class="text-red-600">✗ {{ $webhook->failure_count }}</span>
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $webhook->webhook_deliveries_count }} total
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('webhooks.show', $webhook) }}" class="text-blue-600 hover:text-blue-800">Detalii</a>
                                    <a href="{{ route('webhooks.edit', $webhook) }}" class="text-yellow-600 hover:text-yellow-800">Editează</a>
                                    <form action="{{ route('webhooks.destroy', $webhook) }}" method="POST" class="inline" onsubmit="return confirm('Sigur vrei să ștergi acest webhook?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Șterge</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $webhooks->links() }}
        </div>
    @endif
</div>
@endsection
