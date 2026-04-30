@extends('layouts.dashboard')

@section('title', 'Cerere: ' . $jobRequest->title)
@section('page-title', 'Detalii Cerere Client')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.public-job-requests.index') }}" class="text-primary-600 hover:underline flex items-center text-sm">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Înapoi la cereri
    </a>
</div>

@if(session('success'))
    <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Coloana stângă: detalii cerere --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Header --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        @if($jobRequest->urgency === 'urgent')
                            <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-red-100 text-red-700">🔥 Urgent</span>
                        @elseif($jobRequest->urgency === 'this_week')
                            <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-yellow-100 text-yellow-700">⚡ Săptămâna aceasta</span>
                        @else
                            <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-600">Flexibil</span>
                        @endif
                        @if($jobRequest->status === 'open')
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">● Deschisă</span>
                        @elseif($jobRequest->status === 'in_progress')
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">● În curs</span>
                        @else
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-200 text-gray-600">● Închisă</span>
                        @endif
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $jobRequest->title }}</h2>
                    <p class="text-xs text-gray-400 mt-1">Postată {{ $jobRequest->created_at->diffForHumans() }} · {{ $jobRequest->created_at->format('d.m.Y H:i') }}</p>
                </div>
            </div>

            <div class="prose prose-sm max-w-none text-gray-700 mt-4 bg-gray-50 rounded-lg p-4">
                {{ $jobRequest->description }}
            </div>
        </div>

        {{-- Detalii tehnice --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4 text-sm uppercase tracking-wide text-gray-500">Detalii cerere</h3>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-gray-500">Categorie</dt>
                    <dd class="font-medium text-gray-900 mt-0.5">{{ $jobRequest->category?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Locație</dt>
                    <dd class="font-medium text-gray-900 mt-0.5">{{ $jobRequest->location?->city ?? $jobRequest->city ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Dată preferată</dt>
                    <dd class="font-medium text-gray-900 mt-0.5">{{ $jobRequest->preferred_date?->format('d.m.Y') ?? 'Flexibil' }}</dd>
                </div>
                @if($jobRequest->budget_max)
                <div>
                    <dt class="text-gray-500">Buget maxim</dt>
                    <dd class="font-medium text-green-700 mt-0.5">{{ number_format($jobRequest->budget_max, 0, ',', '.') }} lei</dd>
                </div>
                @endif
                <div>
                    <dt class="text-gray-500">Meseriași notificați</dt>
                    <dd class="font-bold text-blue-700 mt-0.5 text-lg">{{ $jobRequest->notified_craftsmen }}</dd>
                </div>
            </dl>
        </div>

        {{-- Răspunsuri meseriași --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900">Răspunsuri Meseriași</h3>
                <span class="text-sm text-gray-500">{{ $responses->count() }} total</span>
            </div>
            @if($responses->isEmpty())
                <div class="px-6 py-8 text-center text-gray-400 text-sm">Niciun meseriaș nu a răspuns încă.</div>
            @else
                <ul class="divide-y divide-gray-100">
                    @foreach($responses as $resp)
                    <li class="px-6 py-4 flex items-start gap-4">
                        <div class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center shrink-0 text-sm font-bold text-gray-600">
                            {{ strtoupper(substr($resp->craftsman?->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-medium text-gray-900 text-sm">{{ $resp->craftsman?->name ?? 'Meseriaș șters' }}</span>
                                @if($resp->action === 'interested')
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">✓ Interesat — a trimis ofertă</span>
                                @elseif($resp->action === 'not_interested')
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-red-50 text-red-500">✗ Neinteresat</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-500">👁 Văzut</span>
                                @endif
                                <span class="text-xs text-gray-400">{{ $resp->updated_at->diffForHumans() }}</span>
                            </div>
                            @if($resp->message)
                                <p class="text-sm text-gray-600 mt-1 bg-gray-50 rounded p-2">{{ $resp->message }}</p>
                            @endif
                            @if($resp->craftsman)
                                <p class="text-xs text-gray-400 mt-1">{{ $resp->craftsman->phone }} · {{ $resp->craftsman->email }}</p>
                            @endif
                        </div>
                    </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Coloana dreaptă: client + acțiuni --}}
    <div class="space-y-5">

        {{-- Date client --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4 text-sm uppercase tracking-wide text-gray-500">Date Client</h3>
            <ul class="space-y-3 text-sm">
                <li class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span class="font-medium text-gray-900">{{ $jobRequest->name }}</span>
                </li>
                <li class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <a href="tel:{{ $jobRequest->phone }}" class="text-primary-600 hover:underline">{{ $jobRequest->phone }}</a>
                </li>
                <li class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <a href="mailto:{{ $jobRequest->email }}" class="text-primary-600 hover:underline break-all">{{ $jobRequest->email }}</a>
                </li>
            </ul>
        </div>

        {{-- Statistici rapide --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4 text-sm uppercase tracking-wide text-gray-500">Statistici</h3>
            <div class="space-y-3">
                @php
                    $interestedCount   = $responses->where('action', 'interested')->count();
                    $viewedCount       = $responses->where('action', 'viewed')->count();
                    $notInterestedCount= $responses->where('action', 'not_interested')->count();
                @endphp
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Notificați</span>
                    <span class="font-bold text-blue-700">{{ $jobRequest->notified_craftsmen }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Au văzut</span>
                    <span class="font-bold text-gray-700">{{ $responses->count() }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Au trimis ofertă</span>
                    <span class="font-bold text-green-700">{{ $interestedCount }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Neinteresați</span>
                    <span class="font-bold text-red-500">{{ $notInterestedCount }}</span>
                </div>
            </div>
        </div>

        {{-- Acțiuni --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4 text-sm uppercase tracking-wide text-gray-500">Acțiuni</h3>
            <form action="{{ route('admin.public-job-requests.toggle-status', $jobRequest->id) }}" method="POST">
                @csrf
                @method('PATCH')
                @if($jobRequest->status === 'open' || $jobRequest->status === 'in_progress')
                    <button type="submit" class="w-full py-2.5 px-4 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl transition">
                        Închide cererea
                    </button>
                @else
                    <button type="submit" class="w-full py-2.5 px-4 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition">
                        Redeschide cererea
                    </button>
                @endif
            </form>
        </div>
    </div>

</div>
@endsection
