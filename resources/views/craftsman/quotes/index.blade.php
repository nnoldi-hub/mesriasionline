@extends('layouts.craftsman')

@section('title', 'Cereri de Ofertă')
@section('page-title', 'Cereri de Ofertă')

@section('content')
@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex">
            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex">
            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div class="ml-3">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    </div>
@endif

<!-- Filters -->
<div class="mb-6 flex flex-wrap gap-4">
    <form method="GET" class="flex flex-wrap gap-4">
        <select name="status" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500" onchange="this.form.submit()">
            <option value="">Toate statusurile</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>În așteptare</option>
            <option value="quoted" {{ request('status') == 'quoted' ? 'selected' : '' }}>Ofertă trimisă</option>
            <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Acceptate</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Respinse</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Finalizate</option>
        </select>
        <select name="urgency" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500" onchange="this.form.submit()">
            <option value="">Toate urgențele</option>
            <option value="urgent" {{ request('urgency') == 'urgent' ? 'selected' : '' }}>Urgente</option>
            <option value="high" {{ request('urgency') == 'high' ? 'selected' : '' }}>Ridicate</option>
            <option value="normal" {{ request('urgency') == 'normal' ? 'selected' : '' }}>Normale</option>
            <option value="low" {{ request('urgency') == 'low' ? 'selected' : '' }}>Scăzute</option>
        </select>
    </form>
</div>

@if($quoteRequests->isEmpty())
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Nicio cerere de ofertă</h3>
        <p class="text-gray-500">Nu ai primit încă cereri de ofertă de la clienți.</p>
    </div>
@else
    <div class="space-y-4">
        @foreach($quoteRequests as $request)
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition {{ $request->status === 'pending' && !$request->my_quote ? 'border-l-4 border-yellow-400' : '' }}">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $request->title }}</h3>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ $request->status_color }}-100 text-{{ $request->status_color }}-700">
                                {{ $request->status_label }}
                            </span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ $request->urgency_color }}-100 text-{{ $request->urgency_color }}-700">
                                {{ $request->urgency_label }}
                            </span>
                            @if($request->status === 'pending' && !$request->my_quote)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700 animate-pulse">
                                    ⚡ Nouă
                                </span>
                            @endif
                        </div>
                        
                        <p class="text-gray-600 text-sm mb-3">{{ Str::limit($request->description, 150) }}</p>
                        
                        <div class="flex items-center flex-wrap gap-4 text-sm text-gray-500">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                {{ $request->client->name }}
                            </span>
                            @if($request->location)
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $request->location }}
                                </span>
                            @endif
                            @if($request->preferred_date)
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $request->preferred_date->format('d.m.Y') }}
                                </span>
                            @endif
                            @if($request->budget_max)
                                <span class="flex items-center text-green-600 font-medium">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Buget: {{ number_format($request->budget_max, 0, ',', '.') }} lei
                                </span>
                            @endif
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $request->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="ml-4 text-right">
                        @if($request->my_quote)
                            <span class="text-sm font-medium {{ $request->my_quote->status === 'accepted' ? 'text-green-600' : ($request->my_quote->status === 'rejected' ? 'text-red-600' : 'text-blue-600') }}">
                                @if($request->my_quote->status === 'accepted')
                                    ✓ Ofertă acceptată
                                @elseif($request->my_quote->status === 'rejected')
                                    ✗ Ofertă respinsă
                                @else
                                    Ofertă trimisă: {{ $request->my_quote->price_display }}
                                @endif
                            </span>
                        @endif
                        <div class="mt-2">
                            <a href="{{ route('craftsman.quotes.show', $request) }}" class="inline-flex items-center px-4 py-2 {{ $request->status === 'pending' && !$request->my_quote ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-primary-600 hover:bg-primary-700' }} text-white rounded-lg transition text-sm">
                                @if($request->status === 'pending' && !$request->my_quote)
                                    Trimite ofertă
                                @else
                                    Vezi detalii
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $quoteRequests->links() }}
    </div>
@endif
@endsection
