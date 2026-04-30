@extends('layouts.admin')

@section('title', 'Chatbot AI — Monitorizare')
@section('page-title', 'Chatbot AI')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <p class="text-gray-500 text-sm">Monitorizare conversații, intenții și rate de conversie</p>
    </div>
    <div class="flex items-center gap-3">
        <form method="GET" class="flex items-center gap-2">
            <select name="period" onchange="this.form.submit()"
                class="appearance-none pl-3 pr-8 py-2 border border-gray-300 rounded-lg text-sm bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 shadow-sm">
                <option value="7"  {{ $period == 7  ? 'selected' : '' }}>Ultimele 7 zile</option>
                <option value="14" {{ $period == 14 ? 'selected' : '' }}>Ultimele 14 zile</option>
                <option value="30" {{ $period == 30 ? 'selected' : '' }}>Ultimele 30 zile</option>
                <option value="90" {{ $period == 90 ? 'selected' : '' }}>Ultimele 90 zile</option>
            </select>
        </form>
    </div>
</div>

{{-- KPI Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    {{-- Total Conversații --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Conversații</p>
            <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $stats['today'] }} azi</p>
    </div>

    {{-- Conversii (click CTA) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Conversii CTA</p>
            <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-green-600">{{ number_format($stats['converted']) }}</p>
        <p class="text-xs text-gray-400 mt-1">din {{ number_format($stats['total']) }} conversații</p>
    </div>

    {{-- Rata conversie --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Rată conversie</p>
            <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-purple-600">{{ $stats['conversion_rate'] }}%</p>
        <p class="text-xs text-gray-400 mt-1">click pe buton CTA</p>
    </div>

    {{-- Mesaje medii --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Mesaje / conv.</p>
            <div class="w-9 h-9 bg-orange-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-orange-600">{{ $stats['avg_messages'] }}</p>
        <p class="text-xs text-gray-400 mt-1">mesaje per conversație</p>
    </div>
</div>

{{-- Grid: Intenții + Grafic zilnic --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    {{-- Distribuție intenții --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Intenții detectate
        </h3>

        @php
            $intentColors = [
                'craftsman_register' => ['bg' => 'bg-blue-500',   'light' => 'bg-blue-50',   'text' => 'text-blue-700'],
                'client_request'     => ['bg' => 'bg-green-500',  'light' => 'bg-green-50',  'text' => 'text-green-700'],
                'pricing'            => ['bg' => 'bg-yellow-500', 'light' => 'bg-yellow-50', 'text' => 'text-yellow-700'],
                'info'               => ['bg' => 'bg-gray-400',   'light' => 'bg-gray-50',   'text' => 'text-gray-600'],
                'support'            => ['bg' => 'bg-red-500',    'light' => 'bg-red-50',    'text' => 'text-red-700'],
                'other'              => ['bg' => 'bg-indigo-400', 'light' => 'bg-indigo-50', 'text' => 'text-indigo-600'],
                'unknown'            => ['bg' => 'bg-gray-300',   'light' => 'bg-gray-50',   'text' => 'text-gray-500'],
            ];
            $totalIntent = array_sum($stats['by_intent'] ?? []) ?: 1;
        @endphp

        <div class="space-y-3">
            @forelse($intentLabels as $key => $label)
                @php
                    $count = $stats['by_intent'][$key] ?? 0;
                    $pct   = $count > 0 ? round($count / $totalIntent * 100) : 0;
                    $c     = $intentColors[$key] ?? $intentColors['unknown'];
                @endphp
                @if($count > 0)
                <a href="{{ route('admin.chatbot.index', ['intent' => $key, 'period' => $period]) }}"
                   class="block group">
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-medium text-gray-700 group-hover:text-primary-600">{{ $label }}</span>
                        <span class="{{ $c['text'] }} font-bold">{{ $count }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="{{ $c['bg'] }} h-2 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                    </div>
                </a>
                @endif
            @empty
                <p class="text-sm text-gray-400 text-center py-4">Nicio conversație încă.</p>
            @endforelse
        </div>
    </div>

    {{-- Grafic activitate zilnică --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
            </svg>
            Activitate zilnică — ultimele {{ $period }} zile
        </h3>
        <canvas id="chatbotChart" height="120"></canvas>
    </div>
</div>

{{-- Filtre + Lista conversații --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    {{-- Header tabel --}}
    <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center gap-3">
        <h3 class="text-sm font-semibold text-gray-700">Conversații recente</h3>
        <div class="sm:ml-auto flex flex-wrap gap-2">
            <form method="GET" class="flex gap-2 flex-wrap">
                <input type="hidden" name="period" value="{{ $period }}">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Caută în mesaje..."
                    class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-300 w-48">
                <select name="intent" class="text-sm border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-300">
                    <option value="">Toate intențiile</option>
                    @foreach($intentLabels as $key => $label)
                        <option value="{{ $key }}" {{ request('intent') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <label class="flex items-center gap-1.5 text-sm text-gray-600 cursor-pointer">
                    <input type="checkbox" name="converted" value="1" {{ request('converted') ? 'checked' : '' }} class="rounded">
                    Doar convertite
                </label>
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white text-sm px-3 py-1.5 rounded-lg transition">
                    Filtrează
                </button>
                @if(request()->hasAny(['search', 'intent', 'converted']))
                    <a href="{{ route('admin.chatbot.index', ['period' => $period]) }}"
                       class="text-sm text-gray-500 hover:text-gray-700 px-2 py-1.5">Resetează</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Data</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Utilizator</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Intenție</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center">Mesaje</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center">Conversie</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Pagina sursă</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($conversations as $conv)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                        <div>{{ $conv->created_at->format('d.m.Y') }}</div>
                        <div class="text-xs text-gray-400">{{ $conv->created_at->format('H:i') }}</div>
                    </td>
                    <td class="px-4 py-3">
                        @if($conv->user)
                            <div class="font-medium text-gray-800">{{ $conv->user->name }}</div>
                            <div class="text-xs text-gray-400">{{ $conv->user->email }}</div>
                        @else
                            <span class="text-xs text-gray-400">Vizitator anonim</span>
                            <div class="text-xs text-gray-300">{{ $conv->ip_address }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $c = $intentColors[$conv->intent] ?? $intentColors['unknown'];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $c['light'] }} {{ $c['text'] }}">
                            {{ $intentLabels[$conv->intent] ?? 'Necunoscut' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-gray-700 font-medium">{{ $conv->user_messages }}</span>
                        <span class="text-gray-400 text-xs"> / {{ $conv->message_count }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($conv->converted)
                            <span class="inline-flex items-center gap-1 text-green-600 text-xs font-semibold">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Da
                            </span>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-400 max-w-[180px] truncate" title="{{ $conv->page_url }}">
                        {{ $conv->page_url ? parse_url($conv->page_url, PHP_URL_PATH) : '—' }}
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.chatbot.show', $conv) }}"
                           class="text-xs font-medium text-primary-600 hover:text-primary-800 transition">
                            Vezi →
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-gray-400 text-sm">
                        Nicio conversație în perioada selectată.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginare --}}
    @if($conversations->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $conversations->links() }}
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    @php
        $labels   = [];
        $totals   = [];
        $converts = [];
        for ($i = $period - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[]   = now()->subDays($i)->format('d.m');
            $row        = $dailyData->get($date);
            $totals[]   = $row ? (int)$row->total : 0;
            $converts[] = $row ? (int)$row->conversions : 0;
        }
    @endphp

    new Chart(document.getElementById('chatbotChart'), {
        type: 'bar',
        data: {
            labels: @json($labels),
            datasets: [
                {
                    label: 'Conversații',
                    data: @json($totals),
                    backgroundColor: 'rgba(59, 130, 246, 0.15)',
                    borderColor: 'rgba(59, 130, 246, 0.7)',
                    borderWidth: 1.5,
                    borderRadius: 4,
                    order: 2,
                },
                {
                    label: 'Conversii CTA',
                    data: @json($converts),
                    backgroundColor: 'rgba(34, 197, 94, 0.7)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 1.5,
                    borderRadius: 4,
                    order: 1,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11 } } },
                tooltip: { mode: 'index' },
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } } },
            },
        },
    });
});
</script>
@endpush
