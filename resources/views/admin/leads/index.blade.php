@extends('layouts.dashboard')

@section('title', 'Lead-uri Recrutare Meseriași')
@section('page-title', 'Recrutare Meseriași')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('header-actions')
<div class="flex items-center gap-3">
    <a href="{{ route('admin.leads.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Adaugă manual
    </a>
    <a href="{{ route('recruitment.form') }}" target="_blank"
       class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
        </svg>
        Deschide formularul public
    </a>
</div>
@endsection

@section('content')

{{-- Flash messages --}}
@if(session('success'))
    <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        {{ session('error') }}
    </div>
@endif

{{-- Statistici per meserie --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    @php
        $tradeColors = [
            'electrician' => 'yellow',
            'instalator'  => 'blue',
            'tamplar'     => 'amber',
            'zugrav'      => 'pink',
            'mecanic'     => 'gray',
        ];
        $tradeLabels = [
            'electrician' => 'Electrician',
            'instalator'  => 'Instalator',
            'tamplar'     => 'Tâmplar',
            'zugrav'      => 'Zugrav',
            'mecanic'     => 'Mecanic',
        ];
    @endphp
    @foreach($tradeLabels as $key => $label)
        @php
            $data = $perTrade[$key] ?? null;
            $total = $data['total'] ?? 0;
            $converted = $data['converted'] ?? 0;
            $color = $tradeColors[$key];
        @endphp
        <a href="{{ route('admin.leads.index', ['trade' => $key]) }}"
           class="bg-white rounded-xl border border-gray-200 p-4 text-center hover:shadow-md transition">
            <div class="text-2xl font-bold text-gray-800">{{ $total }}</div>
            <div class="text-xs font-medium text-gray-600 mt-1">{{ $label }}</div>
            <div class="text-xs text-green-600 mt-1">{{ $converted }} conturi</div>
            <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2">
                <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $total > 0 ? min(100, ($converted/$total)*100) : 0 }}%"></div>
            </div>
            <div class="text-xs text-gray-400 mt-1">Țintă: 10</div>
        </a>
    @endforeach
</div>

{{-- Sumar general --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
        <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
        <div class="text-xs text-gray-500 mt-1">Total leads</div>
    </div>
    <div class="bg-blue-50 rounded-xl border border-blue-200 p-4 text-center">
        <div class="text-2xl font-bold text-blue-700">{{ $stats['new'] }}</div>
        <div class="text-xs text-gray-500 mt-1">Noi (de contactat)</div>
    </div>
    <div class="bg-purple-50 rounded-xl border border-purple-200 p-4 text-center">
        <div class="text-2xl font-bold text-purple-700">{{ $stats['invited'] }}</div>
        <div class="text-xs text-gray-500 mt-1">Invitați (cu email)</div>
    </div>
    <div class="bg-green-50 rounded-xl border border-green-200 p-4 text-center">
        <div class="text-2xl font-bold text-green-700">{{ $stats['converted'] }}</div>
        <div class="text-xs text-gray-500 mt-1">Conturi create</div>
    </div>
</div>

{{-- Filtre --}}
<form action="{{ route('admin.leads.index') }}" method="GET"
      class="bg-white rounded-xl border border-gray-200 p-4 mb-6 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Meserie</label>
        <select name="trade" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
            <option value="">Toate meseriile</option>
            @foreach($tradeLabels as $key => $label)
                <option value="{{ $key }}" {{ request('trade') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
            <option value="">Toate statusurile</option>
            <option value="nou"         {{ request('status') === 'nou' ? 'selected' : '' }}>Nou</option>
            <option value="contactat"   {{ request('status') === 'contactat' ? 'selected' : '' }}>Contactat</option>
            <option value="invitat"     {{ request('status') === 'invitat' ? 'selected' : '' }}>Invitat</option>
            <option value="inregistrat" {{ request('status') === 'inregistrat' ? 'selected' : '' }}>Înregistrat</option>
            <option value="respins"     {{ request('status') === 'respins' ? 'selected' : '' }}>Respins</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Oraș</label>
        <input type="text" name="city" value="{{ request('city') }}" placeholder="Ex: București"
               class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 w-36">
    </div>
    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm">
        Filtrează
    </button>
    @if(request('trade') || request('status') || request('city'))
        <a href="{{ route('admin.leads.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 text-sm">
            Resetează
        </a>
    @endif
</form>

{{-- Tabel leads --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Nume</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Meserie</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Oraș</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Telefon</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Email</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Data</th>
                    <th class="text-right px-4 py-3 font-medium text-gray-600">Acțiuni</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($leads as $lead)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-medium text-gray-900">
                            {{ $lead->name }}
                            @if($lead->profile_photo)
                                <span class="ml-1 text-xs text-green-600" title="Are poză">📷</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full">
                                {{ $lead->tradeLabel }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $lead->city }}</td>
                        <td class="px-4 py-3">
                            <a href="tel:{{ $lead->phone }}" class="text-primary-600 hover:underline font-medium">
                                {{ $lead->phone }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $lead->email ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $colors = [
                                    'nou'         => 'bg-blue-100 text-blue-800',
                                    'contactat'   => 'bg-yellow-100 text-yellow-800',
                                    'invitat'     => 'bg-purple-100 text-purple-800',
                                    'inregistrat' => 'bg-green-100 text-green-800',
                                    'respins'     => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $colors[$lead->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $lead->statusLabel }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ $lead->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.leads.show', $lead) }}"
                                   class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg transition">
                                    Detalii
                                </a>
                                @if($lead->email && $lead->status !== 'inregistrat')
                                    <form method="POST" action="{{ route('admin.leads.invite', $lead) }}">
                                        @csrf
                                        <button type="submit"
                                                class="text-xs bg-primary-100 hover:bg-primary-200 text-primary-700 px-3 py-1.5 rounded-lg transition">
                                            Invită
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                            Niciun lead găsit. Distribuie formularul de recrutare!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginare --}}
    @if($leads->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $leads->links() }}
        </div>
    @endif
</div>

@endsection
