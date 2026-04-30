@extends('layouts.dashboard')

@section('title', 'Cereri Publice Clienți')
@section('page-title', 'Cereri Publice Clienți')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('header-actions')
<form action="{{ route('admin.public-job-requests.index') }}" method="GET" class="flex items-center gap-2 flex-wrap">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Caută după nume, titlu, telefon..."
           class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-600 focus:border-transparent w-64">
    <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-600">
        <option value="">Toate statusurile</option>
        <option value="open"        {{ request('status') === 'open'        ? 'selected' : '' }}>Deschise</option>
        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>În curs</option>
        <option value="closed"      {{ request('status') === 'closed'      ? 'selected' : '' }}>Închise</option>
    </select>
    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm">Filtrează</button>
    @if(request('search') || request('status'))
        <a href="{{ route('admin.public-job-requests.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 text-sm">Resetează</a>
    @endif
</form>
@endsection

@section('content')

@if(session('success'))
    <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- Sumar stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    @php
        $totalOpen     = \App\Models\PublicJobRequest::where('status', 'open')->count();
        $totalClosed   = \App\Models\PublicJobRequest::where('status', 'closed')->count();
        $totalAll      = \App\Models\PublicJobRequest::count();
        $noResponses   = \App\Models\PublicJobRequest::where('status', 'open')
                            ->whereDoesntHave('responses', fn($q) => $q->where('action', 'interested'))
                            ->count();
    @endphp
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
        <div class="text-2xl font-bold text-gray-900">{{ $totalAll }}</div>
        <div class="text-xs text-gray-500 mt-1">Total cereri</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-green-200 p-4 text-center">
        <div class="text-2xl font-bold text-green-700">{{ $totalOpen }}</div>
        <div class="text-xs text-gray-500 mt-1">Deschise</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-red-200 p-4 text-center">
        <div class="text-2xl font-bold text-red-600">{{ $noResponses }}</div>
        <div class="text-xs text-gray-500 mt-1">Fără răspuns</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
        <div class="text-2xl font-bold text-gray-500">{{ $totalClosed }}</div>
        <div class="text-xs text-gray-500 mt-1">Închise</div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cerere</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categorie / Locație</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Notificați</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Răspuns</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($jobRequests as $req)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">{{ $req->name }}</div>
                        <div class="text-xs text-gray-500">{{ $req->phone }}</div>
                        <div class="text-xs text-gray-400">{{ $req->email }}</div>
                    </td>
                    <td class="px-4 py-3 max-w-xs">
                        <div class="font-medium text-gray-800 truncate">{{ $req->title }}</div>
                        <div class="flex items-center gap-1 mt-1 flex-wrap">
                            @if($req->urgency === 'urgent')
                                <span class="px-1.5 py-0.5 text-xs font-bold rounded-full bg-red-100 text-red-700">🔥 Urgent</span>
                            @elseif($req->urgency === 'this_week')
                                <span class="px-1.5 py-0.5 text-xs font-bold rounded-full bg-yellow-100 text-yellow-700">⚡ Săpt. aceasta</span>
                            @else
                                <span class="px-1.5 py-0.5 text-xs rounded-full bg-gray-100 text-gray-600">Flexibil</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-gray-800">{{ $req->category?->name ?? '—' }}</div>
                        <div class="text-xs text-gray-500">{{ $req->location?->city ?? $req->city ?? '—' }}</div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 text-blue-700 font-bold text-sm">
                            {{ $req->notified_craftsmen }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($req->interested_count > 0)
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">
                                ✓ {{ $req->interested_count }} meseriaș{{ $req->interested_count > 1 ? 'i' : '' }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-red-50 text-red-500 text-xs font-medium">
                                ✗ Niciunul
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($req->status === 'open')
                            <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">Deschisă</span>
                        @elseif($req->status === 'in_progress')
                            <span class="px-2 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">În curs</span>
                        @else
                            <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-500 text-xs font-semibold">Închisă</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                        {{ $req->created_at->format('d.m.Y H:i') }}
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="{{ route('admin.public-job-requests.show', $req->id) }}"
                           class="text-primary-600 hover:text-primary-800 font-medium text-xs mr-3">Detalii</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                        <div class="text-4xl mb-2">📭</div>
                        <p class="font-medium">Nicio cerere găsită</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($jobRequests->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $jobRequests->links() }}
        </div>
    @endif
</div>
@endsection
