@extends('layouts.admin')

@section('title', 'Analytics')
@section('page-title', 'Analytics')

@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <p class="text-gray-500 text-sm">Statistici și analiză platformă</p>
    <div class="flex items-center gap-3">
        {{-- Period Filter --}}
        <div class="relative">
            <select id="periodFilter" onchange="changePeriod(this.value)"
                class="appearance-none pl-4 pr-10 py-2 border border-gray-300 rounded-lg text-sm bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 cursor-pointer shadow-sm">
                <option value="7"  {{ $period == '7'   ? 'selected' : '' }}>Ultimele 7 zile</option>
                <option value="30" {{ $period == '30'  ? 'selected' : '' }}>Ultimele 30 zile</option>
                <option value="90" {{ $period == '90'  ? 'selected' : '' }}>Ultimele 90 zile</option>
                <option value="365"{{ $period == '365' ? 'selected' : '' }}>Ultimul an</option>
            </select>
            <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        {{-- Export --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition shadow-sm gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" @click.away="open = false" x-cloak
                class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-10">
                <a href="#" onclick="openExport('pdf')" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>
                    Export PDF
                </a>
                <a href="#" onclick="openExport('excel')" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>
                    Export Excel
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
    {{-- Vizite Totale --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_visits']) }}</p>
            <p class="text-sm text-gray-500">Vizite Totale</p>
        </div>
    </div>
    {{-- Vizitatori Unici --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['unique_visitors']) }}</p>
            <p class="text-sm text-gray-500">Vizitatori Unici</p>
        </div>
    </div>
    {{-- Înregistrări Noi --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['new_registrations']) }}</p>
            <p class="text-sm text-gray-500">Înregistrări Noi</p>
        </div>
    </div>
    {{-- Rată Conversie --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $funnelStats['conversion_rate'] }}%</p>
            <p class="text-sm text-gray-500">Rată Conversie</p>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    {{-- Trafic Chart (2/3) --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Trafic
        </h3>
        <div style="height:240px;position:relative">
            <canvas id="visitsChart"></canvas>
        </div>
    </div>
    {{-- Dispozitive (1/3) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            Dispozitive
        </h3>
        <div style="height:200px;position:relative">
            <canvas id="deviceChart"></canvas>
        </div>
    </div>
</div>

{{-- Funnel & User Stats --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
    {{-- Pâlnie Conversie --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            Pâlnie Conversie
        </h3>
        <div class="space-y-3">
            @php $colors = ['bg-blue-500','bg-indigo-500','bg-purple-500','bg-pink-500','bg-rose-500','bg-orange-500','bg-amber-500','bg-green-500']; @endphp
            @foreach($funnelStats['stages'] as $index => $stage)
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-700 font-medium">{{ $stage['name'] }}</span>
                    <span class="text-gray-400">{{ number_format($stage['count']) }} <span class="font-semibold text-gray-600">({{ $stage['percentage'] }}%)</span></span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5">
                    <div class="h-2.5 rounded-full {{ $colors[$index % count($colors)] }}" style="width: {{ max($stage['percentage'], 1) }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    {{-- Statistici Utilizatori --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Statistici Utilizatori
        </h3>
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-blue-50 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-blue-600">{{ number_format($userStats['total_craftsmen']) }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Meșteri</p>
            </div>
            <div class="bg-green-50 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-green-600">{{ number_format($userStats['total_clients']) }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Clienți</p>
            </div>
            <div class="bg-cyan-50 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-cyan-600">{{ number_format($userStats['active_craftsmen']) }}</p>
                <p class="text-xs text-gray-500 mt-1">Meșteri Activi (30 zile)</p>
            </div>
            <div class="bg-amber-50 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-amber-600">{{ number_format($userStats['verified_craftsmen']) }}</p>
                <p class="text-xs text-gray-500 mt-1">Meșteri Verificați</p>
            </div>
        </div>
    </div>
</div>

{{-- Conversii Chart & Surse Trafic --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
            </svg>
            Conversii
        </h3>
        <div style="height:220px;position:relative">
            <canvas id="conversionsChart"></canvas>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
            </svg>
            Surse Trafic
        </h3>
        <div class="space-y-3">
            @forelse($trafficSources as $source)
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-sm text-gray-700">
                    @if($source['source'] === 'google')
                        <span class="w-7 h-7 bg-red-50 rounded-lg flex items-center justify-center text-red-500 text-xs font-bold">G</span>
                    @elseif($source['source'] === 'facebook')
                        <span class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 text-xs font-bold">f</span>
                    @elseif($source['source'] === 'direct')
                        <span class="w-7 h-7 bg-gray-100 rounded-lg flex items-center justify-center text-gray-500">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        </span>
                    @else
                        <span class="w-7 h-7 bg-cyan-50 rounded-lg flex items-center justify-center text-cyan-500">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3"/></svg>
                        </span>
                    @endif
                    {{ ucfirst($source['source']) }}
                </div>
                <span class="text-sm font-semibold text-gray-700 bg-gray-100 px-2.5 py-0.5 rounded-full">{{ number_format($source['count']) }}</span>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">Nu există date</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Top Meșteri & Categorii --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
    {{-- Top Meșteri --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                Top 10 Meșteri
            </h3>
            <a href="{{ route('admin.craftsmen') }}" class="text-sm text-primary-600 hover:text-primary-800 font-medium">Vezi toți →</a>
        </div>
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase w-8">#</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Nume</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase">Recenzii</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase">Rating</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($topCraftsmen as $index => $craftsman)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 text-sm text-gray-400 font-mono">{{ $index + 1 }}</td>
                    <td class="px-5 py-3">
                        <a href="{{ route('admin.craftsmen.edit', $craftsman->id) }}" class="text-sm font-medium text-gray-900 hover:text-primary-600 transition">
                            {{ $craftsman->name }}
                        </a>
                    </td>
                    <td class="px-5 py-3 text-right text-sm text-gray-600">{{ $craftsman->reviews_count }}</td>
                    <td class="px-5 py-3 text-right">
                        <span class="inline-flex items-center gap-1 text-sm font-medium text-amber-600">
                            <svg class="w-3.5 h-3.5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            {{ number_format($craftsman->reviews_avg_rating ?? 0, 1) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-6 text-center text-sm text-gray-400">Nu există date</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{-- Top Categorii --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Top Categorii
            </h3>
            <a href="{{ route('admin.craftsmen') }}" class="text-sm text-primary-600 hover:text-primary-800 font-medium">Vezi toate →</a>
        </div>
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase w-8">#</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Categorie</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase">Meșteri</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($topCategories as $index => $category)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 text-sm text-gray-400 font-mono">{{ $index + 1 }}</td>
                    <td class="px-5 py-3 text-sm font-medium text-gray-900">{{ $category->name }}</td>
                    <td class="px-5 py-3 text-right">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                            {{ $category->users_count }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-5 py-6 text-center text-sm text-gray-400">Nu există date</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Recent Activity --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    {{-- Înregistrări Recente --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Înregistrări Recente
            </h3>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($recentRegistrations as $user)
            <div class="flex items-center justify-between px-5 py-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 text-xs font-bold shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $user->role === 'specialist' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $user->role === 'specialist' ? 'Meșter' : 'Client' }}
                    </span>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $user->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-sm text-gray-400">Nu există înregistrări recente</div>
            @endforelse
        </div>
    </div>
    {{-- Recenzii Recente --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                Recenzii Recente
            </h3>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($recentReviews as $review)
            <div class="px-5 py-3.5">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-sm font-medium text-gray-900">{{ $review->user->name ?? 'Anonim' }}</p>
                    <div class="flex items-center gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                </div>
                <p class="text-xs text-gray-400">pentru <span class="text-gray-600 font-medium">{{ $review->specialist->name ?? 'N/A' }}</span> • {{ $review->created_at->diffForHumans() }}</p>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-sm text-gray-400">Nu există recenzii recente</div>
            @endforelse
        </div>
    </div>
</div>

{{-- Export Modal --}}
<div id="exportModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <form id="exportForm" method="POST">
            @csrf
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900">Export Raport</h3>
                <button type="button" onclick="closeExport()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Data Început</label>
                    <input type="date" name="start_date"
                        class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                        value="{{ now()->subDays(30)->format('Y-m-d') }}" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Data Sfârșit</label>
                    <input type="date" name="end_date"
                        class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                        value="{{ now()->format('Y-m-d') }}" required>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100">
                <button type="button" onclick="closeExport()" class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Anulează</button>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Descarcă
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
function changePeriod(days) {
    window.location.href = '{{ route("admin.analytics.index") }}?period=' + days;
}

function openExport(format) {
    const form = document.getElementById('exportForm');
    form.action = format === 'pdf'
        ? '{{ route("admin.analytics.export-pdf") }}'
        : '{{ route("admin.analytics.export-excel") }}';
    const modal = document.getElementById('exportModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeExport() {
    const modal = document.getElementById('exportModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('exportModal').addEventListener('click', function(e) {
    if (e.target === this) closeExport();
});

// Visits Chart
const visitsCtx = document.getElementById('visitsChart').getContext('2d');
new Chart(visitsCtx, {
    type: 'line',
    data: @json($visitsChart),
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true, grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } }
    }
});

// Device Chart
const deviceData = @json($deviceBreakdown);
const deviceCtx = document.getElementById('deviceChart').getContext('2d');
new Chart(deviceCtx, {
    type: 'doughnut',
    data: {
        labels: deviceData.map(d => d.device_type.charAt(0).toUpperCase() + d.device_type.slice(1)),
        datasets: [{ data: deviceData.map(d => d.count), backgroundColor: ['#3b82f6', '#10b981', '#f59e0b'], borderWidth: 0 }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } } },
        cutout: '65%'
    }
});

// Conversions Chart
const conversionsCtx = document.getElementById('conversionsChart').getContext('2d');
new Chart(conversionsCtx, {
    type: 'line',
    data: @json($conversionsChart),
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true, grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } }
    }
});
</script>
@endpush

@endsection
