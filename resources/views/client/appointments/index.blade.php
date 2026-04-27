@extends('layouts.client')

@section('title', 'Programările Mele')
@section('page-title', 'Programările Mele')

@section('content')
<div class="bg-white rounded-lg shadow">
    <!-- Header cu filtre -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Toate Programările</h2>
                <p class="text-sm text-gray-600">Vizualizează și gestionează programările tale cu meseriașii.</p>
            </div>
            <a href="{{ route('home') }}" 
                class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Caută Meșteri
            </a>
        </div>
    </div>

    <!-- Filtre tabs -->
    <div class="border-b border-gray-200">
        <nav class="flex px-6 space-x-6" aria-label="Tabs">
            <a href="?status=all" class="py-3 px-1 border-b-2 border-primary-600 text-primary-600 font-medium text-sm">
                Toate
            </a>
            <a href="?status=pending" class="py-3 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm">
                În așteptare
            </a>
            <a href="?status=confirmed" class="py-3 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm">
                Confirmate
            </a>
            <a href="?status=completed" class="py-3 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm">
                Finalizate
            </a>
        </nav>
    </div>

    <!-- Lista programări -->
    <div class="p-6">
        @php
            $appointments = \App\Models\Appointment::where('client_email', auth()->user()->email)
                ->with(['specialist', 'service'])
                ->orderBy('appointment_date', 'desc')
                ->orderBy('appointment_time', 'desc')
                ->get();
        @endphp

        @if($appointments->count() > 0)
        <div class="space-y-4">
            @foreach($appointments as $appointment)
            <div class="border border-gray-200 rounded-lg p-4 hover:border-primary-300 transition">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-4">
                        <!-- Avatar meșter -->
                        <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center text-primary-600 font-bold">
                            {{ strtoupper(substr($appointment->specialist->name ?? 'M', 0, 2)) }}
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">
                                {{ $appointment->specialist->name ?? 'Meșter' }}
                            </h4>
                            <p class="text-sm text-gray-600">{{ $appointment->service->name ?? 'Serviciu' }}</p>
                            <div class="flex items-center mt-2 text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y') }}, {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col items-end space-y-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($appointment->status === 'confirmed') bg-green-100 text-green-800
                            @elseif($appointment->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($appointment->status === 'completed') bg-blue-100 text-blue-800
                            @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            @switch($appointment->status)
                                @case('confirmed') Confirmată @break
                                @case('pending') În așteptare @break
                                @case('completed') Finalizată @break
                                @case('cancelled') Anulată @break
                                @default {{ ucfirst($appointment->status) }}
                            @endswitch
                        </span>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('messages.index') }}?user={{ $appointment->specialist_id }}" 
                                class="text-primary-600 hover:text-primary-800 text-sm">
                                Mesaj
                            </a>
                            @if($appointment->status === 'completed' && !$appointment->hasReview)
                            <a href="{{ route('client.reviews.create', ['appointment' => $appointment->id]) }}" 
                                class="text-green-600 hover:text-green-800 text-sm">
                                Lasă Recenzie
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Nu ai nicio programare</h3>
            <p class="text-gray-600 mb-6">Caută un meșter și programează o vizită!</p>
            <a href="{{ route('home') }}" 
                class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Caută Meșteri
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
