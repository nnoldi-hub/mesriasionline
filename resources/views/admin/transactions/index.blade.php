@extends('layouts.dashboard')

@section('title', 'Tranzacții - Administrator')
@section('page-title', 'Tranzacții Plăți')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('header-actions')
<a href="{{ route('admin.subscriptions') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 flex items-center gap-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
    </svg>
    Subscripții
</a>
@endsection

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-500">Venituri totale (completate)</p>
        <p class="text-3xl font-bold text-green-600 mt-1">{{ number_format($totalCompleted, 0) }} RON</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-500">Venituri azi</p>
        <p class="text-3xl font-bold text-primary-600 mt-1">{{ number_format($todayRevenue, 0) }} RON</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-500">Tranzacții eșuate</p>
        <p class="text-3xl font-bold text-red-600 mt-1">{{ number_format($totalFailed) }}</p>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form action="{{ route('admin.transactions') }}" method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Caută</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Utilizator sau session ID..."
                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-600 focus:border-transparent w-56">
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
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completat</option>
                <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>În așteptare</option>
                <option value="failed"    {{ request('status') === 'failed'    ? 'selected' : '' }}>Eșuat</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm">
            Filtrează
        </button>
        @if(request()->anyFilled(['search','plan_id','status']))
            <a href="{{ route('admin.transactions') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm">
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sumă</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stripe Session ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dată</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mesaj eroare</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($transactions as $tx)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $tx->user->name ?? '-' }}</div>
                        <div class="text-xs text-gray-500">{{ $tx->user->email ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($tx->plan && $tx->plan->slug === 'pro') bg-amber-100 text-amber-800
                            @elseif($tx->plan && $tx->plan->slug === 'starter') bg-indigo-100 text-indigo-800
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ $tx->plan->name ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                        {{ number_format($tx->amount, 2) }} {{ strtoupper($tx->currency ?? 'RON') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                            @if($tx->status === 'completed') bg-green-100 text-green-800
                            @elseif($tx->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($tx->status === 'failed') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ ucfirst($tx->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-500 font-mono max-w-xs truncate">
                        {{ $tx->stripe_session_id ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $tx->created_at->format('d.m.Y H:i') }}
                    </td>
                    <td class="px-6 py-4 text-xs text-red-600 max-w-xs truncate">
                        {{ $tx->failure_message ?? '' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        Nicio tranzacție găsită.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

@if($transactions->hasPages())
    <div class="mt-4">
        {{ $transactions->links() }}
    </div>
@endif

@endsection
