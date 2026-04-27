@extends('layouts.craftsman')

@section('title', 'Programările Mele')
@section('page-title', 'Programările Mele')

@section('header-actions')
<form action="{{ route('craftsman.appointments') }}" method="GET" class="flex items-center space-x-2">
    <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
        <option value="">Toate statusurile</option>
        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>În așteptare</option>
        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmate</option>
        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completate</option>
        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Anulate</option>
    </select>
    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
        Filtrează
    </button>
</form>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serviciu</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data & Ora</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sumă</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($appointments as $appointment)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $appointment->client_name }}</div>
                        <div class="text-sm text-gray-500">{{ $appointment->client_phone }}</div>
                        @if($appointment->client_email)
                            <div class="text-sm text-gray-500">{{ $appointment->client_email }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $appointment->service->name ?? 'N/A' }}</div>
                        @if($appointment->is_home_service && $appointment->client_address)
                            <div class="text-sm text-gray-500">📍 {{ Str::limit($appointment->client_address, 40) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $appointment->appointment_date->format('d.m.Y') }}</div>
                        <div class="text-sm text-gray-500">{{ $appointment->appointment_time }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $appointment->total_amount ? number_format($appointment->total_amount, 0) . ' RON' : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($appointment->status === 'completed') bg-success-100 text-success-700
                            @elseif($appointment->status === 'pending') bg-accent-100 text-accent-800
                            @elseif($appointment->status === 'confirmed') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        Nu ai programări
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

<div class="mt-6">
    {{ $appointments->links() }}
</div>
@endsection
