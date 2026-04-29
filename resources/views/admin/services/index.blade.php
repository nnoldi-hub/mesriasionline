@extends('layouts.dashboard')

@section('title', 'Servicii - Administrator')
@section('page-title', 'Gestionare Servicii')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('header-actions')
<form action="{{ route('admin.services') }}" method="GET" class="flex items-center space-x-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Caută serviciu..." 
        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
    
    <select name="category_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
        <option value="">Toate categoriile</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>

    <select name="is_active" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
        <option value="">Toate statusurile</option>
        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
    </select>

    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
        Filtrează
    </button>
</form>
@endsection

@section('content')
@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serviciu</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meseriaș</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categorie</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preț</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durată</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acțiuni</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($services as $service)
                <tr>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $service->name }}</div>
                        <div class="text-sm text-gray-500">{{ Str::limit(strip_tags($service->description), 50) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-secondary-200 rounded-full flex items-center justify-center text-primary-600 font-bold text-xs">
                                {{ strtoupper(substr($service->user->name ?? 'N', 0, 1)) }}
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">{{ $service->user->name ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $service->category->name ?? $service->category ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($service->pricing_type === 'range' && $service->min_price && $service->max_price)
                            {{ number_format($service->min_price, 0) }} - {{ number_format($service->max_price, 0) }} RON
                        @elseif($service->price)
                            {{ number_format($service->price, 0) }} RON
                        @else
                            La cerere
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($service->min_duration && $service->max_duration)
                            {{ $service->min_duration }} - {{ $service->max_duration }} min
                        @elseif($service->duration)
                            {{ $service->duration }} min
                        @else
                            Variabil
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 text-xs font-medium rounded-full {{ $service->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-800' }}">
                            {{ $service->is_active ? 'Activ' : 'Inactiv' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('admin.services.edit', $service->id) }}" class="text-primary-600 hover:text-primary-900">
                            Editează
                        </a>
                        <form action="{{ route('admin.services.toggle-status', $service->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="{{ $service->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}">
                                {{ $service->is_active ? 'Dezactivează' : 'Activează' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                        Nu există servicii înregistrate.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

@if($services->hasPages())
    <div class="mt-4">
        {{ $services->withQueryString()->links() }}
    </div>
@endif
@endsection
