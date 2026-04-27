@extends('layouts.dashboard')

@section('title', 'Subscripții - Administrator')
@section('page-title', 'Gestionare Subscripții')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('header-actions')
<a href="{{ route('admin.transactions') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 flex items-center gap-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
    </svg>
    Tranzacții
</a>
@endsection

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-500">Subscripții active</p>
        <p class="text-3xl font-bold text-green-600 mt-1">{{ number_format($activeCount) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-500">Perioade de probă</p>
        <p class="text-3xl font-bold text-blue-600 mt-1">{{ number_format($trialCount) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-500">Venituri luna curentă</p>
        <p class="text-3xl font-bold text-primary-600 mt-1">{{ number_format($mrr, 0) }} RON</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-500">Venituri totale</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($totalRevenue, 0) }} RON</p>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form action="{{ route('admin.subscriptions') }}" method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Caută utilizator</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nume sau email..."
                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-600 focus:border-transparent w-48">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Plan</label>
            <select name="plan_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                <option value="">Toate planurile</option>
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}" {{ request('plan_id') == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                <option value="">Toate</option>
                <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Activ</option>
                <option value="trial"     {{ request('status') === 'trial'     ? 'selected' : '' }}>Probă</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Anulat</option>
                <option value="expired"   {{ request('status') === 'expired'   ? 'selected' : '' }}>Expirat</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm">
            Filtrează
        </button>
        @if(request()->anyFilled(['search','plan_id','status']))
            <a href="{{ route('admin.subscriptions') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm">
                Resetează
            </a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilizator</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Început</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiră</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referință plată</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Oferte folosite</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($subscriptions as $sub)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $sub->user->name ?? '-' }}</div>
                        <div class="text-xs text-gray-500">{{ $sub->user->email ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($sub->plan->slug === 'pro') bg-amber-100 text-amber-800
                            @elseif($sub->plan->slug === 'starter') bg-indigo-100 text-indigo-800
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ $sub->plan->name ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                            @if($sub->status === 'active') bg-green-100 text-green-800
                            @elseif($sub->status === 'trial') bg-blue-100 text-blue-800
                            @elseif($sub->status === 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ ucfirst($sub->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $sub->started_at?->format('d.m.Y') ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        @if($sub->ends_at)
                            <span class="{{ $sub->ends_at->isPast() ? 'text-red-600' : '' }}">
                                {{ $sub->ends_at->format('d.m.Y') }}
                            </span>
                        @else
                            <span class="text-gray-400">Nelimitat</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-500 font-mono max-w-xs truncate">
                        {{ $sub->payment_reference ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                        {{ $sub->quotes_used_this_month }}
                        @if($sub->plan && $sub->plan->max_quotes_per_month > 0)
                            <span class="text-gray-400">/ {{ $sub->plan->max_quotes_per_month }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        Nicio subscripție găsită.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

@if($subscriptions->hasPages())
    <div class="mt-4">
        {{ $subscriptions->links() }}
    </div>
@endif

@endsection
