@extends('layouts.craftsman')

@section('title', 'Serviciile Mele')
@section('page-title', 'Serviciile Mele')

@section('header-actions')
<a href="{{ route('craftsman.services.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 flex items-center">
    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
    </svg>
    Adaugă Serviciu
</a>
@endsection

@section('content')
@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serviciu</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preț</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durată</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Programări</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acțiuni</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($services as $service)
                <tr>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $service->name }}</div>
                        <div class="text-sm text-gray-500">{{ Str::limit($service->description, 60) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($service->price)
                            {{ number_format($service->price, 0) }} RON
                        @elseif($service->min_price && $service->max_price)
                            {{ number_format($service->min_price, 0) }} - {{ number_format($service->max_price, 0) }} RON
                        @else
                            La cerere
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $service->duration ? $service->duration . ' min' : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $service->appointments_count }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $service->is_active ? 'bg-success-100 text-success-700' : 'bg-gray-100 text-gray-800' }}">
                            {{ $service->is_active ? 'Activ' : 'Inactiv' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('craftsman.services.edit', $service->id) }}" class="text-primary-600 hover:text-primary-900">
                            Editează
                        </a>
                        <form action="{{ route('craftsman.services.toggle-status', $service->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="{{ $service->is_active ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900' }}">
                                {{ $service->is_active ? 'Dezactivează' : 'Activează' }}
                            </button>
                        </form>
                        @if($service->appointments_count == 0)
                            <form action="{{ route('craftsman.services.delete', $service->id) }}" method="POST" class="inline" onsubmit="return confirm('Ești sigur că vrei să ștergi acest serviciu?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    Șterge
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center">
                        <div class="text-gray-500 mb-4">Nu ai servicii configurate</div>
                        <a href="{{ route('craftsman.services.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                            </svg>
                            Adaugă primul tău serviciu
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

@if($services->hasPages())
<div class="mt-6">
    {{ $services->links() }}
</div>
@endif
@endsection
