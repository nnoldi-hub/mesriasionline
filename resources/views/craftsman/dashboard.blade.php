@extends('layouts.craftsman')

@section('title', 'Dashboard Meseriaș')
@section('page-title', 'Dashboard Meseriaș')

@section('content')
@if(session('success'))
    <div class="mb-6 bg-success-50 border border-success-200 rounded-lg p-4">
        <div class="flex">
            <svg class="h-5 w-5 text-success-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div class="ml-3">
                <p class="text-sm text-success-700">{{ session('success') }}</p>
            </div>
        </div>
    </div>
@endif

{{-- Plan upgrade banner --}}
@if(!auth()->user()->isPro())
    <div class="mb-6 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-md">
        <div class="text-white">
            <p class="font-bold text-lg leading-tight">
                @if(auth()->user()->isStarter())
                    ⭐ Fă upgrade la Pro și obține oferte nelimitate!
                @else
                    🚀 Îți mai rămân <strong>{{ max(0, 3 - (auth()->user()->activeSubscription()?->quotes_used_this_month ?? 0)) }}</strong> oferte gratuite luna aceasta.
                @endif
            </p>
            <p class="text-emerald-100 text-sm mt-0.5">
                Planul Pro îți oferă vizibilitate maximă și clienți mai mulți.
            </p>
        </div>
        <a href="{{ route('plans.index') }}"
           class="flex-shrink-0 bg-white text-emerald-700 font-semibold text-sm px-5 py-2.5 rounded-lg hover:bg-emerald-50 transition-colors whitespace-nowrap">
            Vezi planurile →
        </a>
    </div>
@endif

@if(!auth()->user()->is_active)
    <div class="mb-6 bg-accent-50 border border-accent-200 rounded-lg p-4">
        <div class="flex">
            <svg class="h-5 w-5 text-accent-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-accent-800">Cont în curs de verificare</h3>
                <p class="mt-1 text-sm text-accent-700">
                    Contul tău este în curs de verificare de către administratorii noștri. 
                    Vei primi o notificare când contul va fi activat și vei putea primi comenzi.
                </p>
            </div>
        </div>
    </div>
@endif

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Servicii Active</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['active_services'] }}</p>
                <p class="text-sm text-gray-500 mt-1">din {{ $stats['total_services'] }} total</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Programări Totale</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_appointments'] }}</p>
                <p class="text-sm text-success-600 mt-1">{{ $stats['completed_appointments'] }} completate</p>
            </div>
            <div class="w-12 h-12 bg-success-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-success-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">În Așteptare</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['pending_appointments'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Programări noi</p>
            </div>
            <div class="w-12 h-12 bg-secondary-200 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Rating Mediu</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['average_rating'], 1) }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $stats['total_reviews'] }} recenzii</p>
            </div>
            <div class="w-12 h-12 bg-accent-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

{{-- Mesaje Necitite --}}
@php
    $unreadMsgCount = \App\Models\Conversation::where('craftsman_id', auth()->id())
        ->whereHas('messages', fn($q) => $q->where('sender_id', '!=', auth()->id())->whereNull('read_at'))
        ->count();
    $totalMsgCount = \App\Models\Conversation::where('craftsman_id', auth()->id())->count();
@endphp
<div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
    <a href="{{ route('messages.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-md transition block">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Mesaje primite</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalMsgCount }}</p>
                @if($unreadMsgCount > 0)
                    <p class="text-sm font-semibold mt-1" style="color:#C0392B;">{{ $unreadMsgCount }} necitite</p>
                @else
                    <p class="text-sm text-gray-500 mt-1">Toate citite</p>
                @endif
            </div>
            <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color:#FEF9C3;">
                <svg class="w-6 h-6" style="color:#D97706;" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/>
                    <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"/>
                </svg>
            </div>
        </div>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Appointments -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">Programări Recente</h2>
            <a href="{{ route('craftsman.appointments') }}" class="text-sm text-primary-600 hover:text-primary-700">Vezi toate</a>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($recent_appointments as $appointment)
                    <div class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-medium text-gray-900">{{ $appointment->client_name }}</p>
                                <p class="text-sm text-gray-500">{{ $appointment->service->name ?? 'N/A' }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($appointment->status === 'completed') bg-success-100 text-success-700
                                @elseif($appointment->status === 'pending') bg-accent-100 text-accent-800
                                @elseif($appointment->status === 'confirmed') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                            {{ $appointment->appointment_date->format('d.m.Y') }} la {{ $appointment->appointment_time }}
                        </div>
                        <p class="text-sm text-gray-500 mt-1">📞 {{ $appointment->client_phone }}</p>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Nu ai programări recente</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Reviews -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">Recenzii Recente</h2>
            <a href="{{ route('craftsman.reviews') }}" class="text-sm text-primary-600 hover:text-primary-700">Vezi toate</a>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($recent_reviews as $review)
                    <div class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-medium text-gray-900">{{ $review->client_name }}</p>
                                <div class="flex items-center mt-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-accent-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                            <span class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-700">{{ Str::limit($review->comment, 100) }}</p>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Nu ai recenzii încă</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
