@extends('layouts.craftsman')

@section('title', 'Analytics & Statistici')
@section('page-title', 'Analytics & Statistici')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<!-- Period Selector -->
<div class="mb-6 flex items-center justify-between">
    <form method="GET" class="flex items-center space-x-4">
        <label class="text-sm text-gray-600">Perioadă:</label>
        <select name="period" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500">
            <option value="7" {{ $period == '7' ? 'selected' : '' }}>Ultimele 7 zile</option>
            <option value="30" {{ $period == '30' ? 'selected' : '' }}>Ultimele 30 zile</option>
            <option value="90" {{ $period == '90' ? 'selected' : '' }}>Ultimele 90 zile</option>
        </select>
    </form>
    <a href="{{ route('craftsman.analytics.export', ['period' => $period]) }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        Exportă CSV
    </a>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Vizualizări profil</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['profile_views']) }}</p>
            </div>
            <div class="p-3 bg-blue-100 rounded-full">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            </div>
        </div>
        @if($growth['profile_views'] != 0)
        <p class="mt-2 text-sm {{ $growth['profile_views'] > 0 ? 'text-green-600' : 'text-red-600' }}">
            {{ $growth['profile_views'] > 0 ? '↑' : '↓' }} {{ abs($growth['profile_views']) }}% față de perioada anterioară
        </p>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Cereri ofertă</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['quote_requests']) }}</p>
            </div>
            <div class="p-3 bg-green-100 rounded-full">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
        </div>
        @if($growth['quote_requests'] != 0)
        <p class="mt-2 text-sm {{ $growth['quote_requests'] > 0 ? 'text-green-600' : 'text-red-600' }}">
            {{ $growth['quote_requests'] > 0 ? '↑' : '↓' }} {{ abs($growth['quote_requests']) }}% față de perioada anterioară
        </p>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Programări</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['bookings']) }}</p>
            </div>
            <div class="p-3 bg-purple-100 rounded-full">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
        @if($growth['bookings'] != 0)
        <p class="mt-2 text-sm {{ $growth['bookings'] > 0 ? 'text-green-600' : 'text-red-600' }}">
            {{ $growth['bookings'] > 0 ? '↑' : '↓' }} {{ abs($growth['bookings']) }}% față de perioada anterioară
        </p>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Mesaje primite</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['messages_received']) }}</p>
            </div>
            <div class="p-3 bg-yellow-100 rounded-full">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
            </div>
        </div>
        @if($growth['messages_received'] != 0)
        <p class="mt-2 text-sm {{ $growth['messages_received'] > 0 ? 'text-green-600' : 'text-red-600' }}">
            {{ $growth['messages_received'] > 0 ? '↑' : '↓' }} {{ abs($growth['messages_received']) }}% față de perioada anterioară
        </p>
        @endif
    </div>
</div>

<!-- Chart -->
<div class="bg-white rounded-xl shadow-sm p-6 mb-8">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Evoluție în timp</h2>
    <canvas id="analyticsChart" height="100"></canvas>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Traffic Sources -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Surse de trafic</h2>
        @if($trafficSources->isNotEmpty())
            <div class="space-y-3">
                @php
                    $totalViews = $trafficSources->sum('count');
                    $sourceLabels = [
                        'google' => ['Google', 'bg-blue-500'],
                        'facebook' => ['Facebook', 'bg-blue-600'],
                        'instagram' => ['Instagram', 'bg-pink-500'],
                        'direct' => ['Direct', 'bg-gray-500'],
                        'internal' => ['Intern', 'bg-green-500'],
                        'other' => ['Altele', 'bg-gray-400'],
                    ];
                @endphp
                @foreach($trafficSources as $source)
                    @php
                        $percent = $totalViews > 0 ? round(($source->count / $totalViews) * 100, 1) : 0;
                        $label = $sourceLabels[$source->source] ?? ['Necunoscut', 'bg-gray-300'];
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span>{{ $label[0] }}</span>
                            <span class="text-gray-500">{{ $source->count }} ({{ $percent }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="{{ $label[1] }} h-2 rounded-full" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-8">Nu există date pentru această perioadă.</p>
        @endif
    </div>

    <!-- Top Services -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Top servicii solicitate</h2>
        @if($topServices->isNotEmpty())
            <div class="space-y-3">
                @foreach($topServices as $service)
                    <div class="flex items-center justify-between py-2 border-b last:border-0">
                        <span class="font-medium">{{ $service->name }}</span>
                        <span class="bg-primary-100 text-primary-700 px-2 py-1 rounded text-sm">
                            {{ $service->appointments_count }} programări
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-8">Nu există date pentru această perioadă.</p>
        @endif
    </div>
</div>

<!-- Recent Reviews -->
@if($recentReviews->isNotEmpty())
<div class="mt-6 bg-white rounded-xl shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Recenzii recente</h2>
    <div class="space-y-4">
        @foreach($recentReviews as $review)
            <div class="border-b last:border-0 pb-4 last:pb-0">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-medium">{{ $review->name }}</span>
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                </div>
                <p class="text-gray-600 text-sm">{{ Str::limit($review->comment, 150) }}</p>
                <p class="text-gray-400 text-xs mt-1">{{ $review->created_at->diffForHumans() }}</p>
            </div>
        @endforeach
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
const chartData = @json($chartData);

const ctx = document.getElementById('analyticsChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: chartData.labels,
        datasets: chartData.datasets.map(dataset => ({
            ...dataset,
            tension: 0.4,
            fill: true,
        }))
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});
</script>
@endpush
