@extends('layouts.dashboard')

@section('title', 'Dashboard Administrator')
@section('page-title', 'Dashboard Administrator')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('content')
<!-- Stats Grid - Layout orizontal -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
    <!-- Solicitari mentenanta/intretinere -->
    <a href="{{ route('admin.generic.requests') }}" class="bg-white rounded-xl shadow p-4 flex flex-col hover:shadow-lg hover:border-primary-300 border border-gray-200 transition group">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-medium text-gray-600">Solicitari mentenanta</p>
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 3a1 1 0 011-1h10a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V3zM4 9a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM15 8a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-blue-700 group-hover:text-primary-600">{{ $stats['pending_generic_requests'] ?? 0 }}</p>
    </a>

    <!-- Meseriași -->
    <a href="{{ route('admin.craftsmen') }}" class="bg-blue-600 rounded-xl shadow p-4 flex flex-col hover:shadow-lg transition group">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-medium text-white">Meseriași</p>
            <div class="w-10 h-10 bg-blue-800 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-white group-hover:text-primary-100">{{ $stats['total_craftsmen'] }}</p>
    </a>

    <!-- Recenzii -->
    <a href="{{ route('admin.reviews') }}" class="bg-green-500 rounded-xl shadow p-4 flex flex-col hover:shadow-lg transition group">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-medium text-white">Recenzii</p>
            <div class="w-10 h-10 bg-green-700 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-white group-hover:text-green-100">{{ $stats['total_reviews'] }}</p>
        @if($stats['pending_reviews'] > 0)
            <p class="text-xs text-green-100 mt-1">{{ $stats['pending_reviews'] }} de aprobat</p>
        @else
            <p class="text-xs text-green-100 mt-1">Toate aprobate</p>
        @endif
    </a>

    <!-- Servicii -->
    <a href="{{ route('admin.services') }}" class="bg-cyan-400 rounded-xl shadow p-4 flex flex-col hover:shadow-lg transition group">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-medium text-white">Servicii</p>
            <div class="w-10 h-10 bg-cyan-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-white group-hover:text-cyan-100">{{ $stats['total_services'] }}</p>
    </a>

    <!-- Programări -->
    <div class="bg-white rounded-xl shadow p-4 flex flex-col border border-gray-200">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-medium text-gray-600">Programări</p>
            <div class="w-10 h-10 bg-secondary-200 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_appointments'] }}</p>
        <p class="text-xs text-primary-600 mt-1">{{ $stats['pending_appointments'] }} în așteptare</p>
    </div>
</div>

<!-- Secțiuni detaliate - Layout vertical -->
<div class="flex flex-col gap-6 max-w-4xl mx-auto">
    <!-- Recent Craftsmen -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Meseriași Recenți</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($recent_craftsmen as $craftsman)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-secondary-200 rounded-full flex items-center justify-center text-primary-600 font-bold">
                                {{ strtoupper(substr($craftsman->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $craftsman->name }}</p>
                                <p class="text-sm text-gray-500">{{ $craftsman->category->name ?? 'N/A' }} • {{ $craftsman->location->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 text-xs font-medium rounded-full {{ $craftsman->is_active ? 'bg-success-100 text-success-700' : 'bg-gray-100 text-gray-800' }}">
                            {{ $craftsman->is_active ? 'Activ' : 'Inactiv' }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Nu există meseriași</p>
                @endforelse
            </div>
            <a href="{{ route('admin.craftsmen') }}" class="block text-center text-primary-600 hover:text-primary-700 font-medium mt-4">
                Vezi toți meseriașii →
            </a>
        </div>
    </div>

    <!-- Pending Reviews -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Recenzii de Aprobat</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($pending_reviews as $review)
                    <div class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <p class="font-medium text-gray-900">{{ $review->client_name }}</p>
                                <p class="text-sm text-gray-500">{{ $review->specialist->name }}</p>
                            </div>
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-accent-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                        </div>
                        <p class="text-sm text-gray-700 mb-2">{{ Str::limit($review->comment, 100) }}</p>
                        <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                                Aprobă recenzia
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Nu există recenzii de aprobat</p>
                @endforelse
            </div>
            <a href="{{ route('admin.reviews') }}" class="block text-center text-primary-600 hover:text-primary-700 font-medium mt-4">
                Vezi toate recenziile →
            </a>
        </div>
    </div>

    <!-- Recent Appointments -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Programări Recente</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meseriaș</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serviciu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recent_appointments as $appointment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $appointment->client_name }}</div>
                                <div class="text-sm text-gray-500">{{ $appointment->client_phone }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $appointment->specialist->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $appointment->service->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $appointment->appointment_date->format('d.m.Y') }} {{ $appointment->appointment_time }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($appointment->status === 'completed') bg-green-100 text-green-700
                                    @elseif($appointment->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($appointment->status === 'confirmed') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nu există programări</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
