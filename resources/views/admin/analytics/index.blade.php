@extends('layouts.admin')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">📊 Analytics Dashboard</h1>
            <p class="text-muted mb-0">Statistici și analiză platformă</p>
        </div>
        <div class="d-flex gap-2">
            <!-- Period Filter -->
            <select class="form-select" id="periodFilter" onchange="changePeriod(this.value)">
                <option value="7" {{ $period == '7' ? 'selected' : '' }}>Ultimele 7 zile</option>
                <option value="30" {{ $period == '30' ? 'selected' : '' }}>Ultimele 30 zile</option>
                <option value="90" {{ $period == '90' ? 'selected' : '' }}>Ultimele 90 zile</option>
                <option value="365" {{ $period == '365' ? 'selected' : '' }}>Ultimul an</option>
            </select>
            
            <!-- Export Buttons -->
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i> Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#exportModal" data-format="pdf">
                            <i class="bi bi-file-pdf me-2"></i> Export PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#exportModal" data-format="excel">
                            <i class="bi bi-file-excel me-2"></i> Export Excel
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-eye text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h2 class="mb-0">{{ number_format($stats['total_visits']) }}</h2>
                            <p class="text-muted mb-0">Vizite Totale</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-people text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h2 class="mb-0">{{ number_format($stats['unique_visitors']) }}</h2>
                            <p class="text-muted mb-0">Vizitatori Unici</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-person-plus text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h2 class="mb-0">{{ number_format($stats['new_registrations']) }}</h2>
                            <p class="text-muted mb-0">Înregistrări Noi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-graph-up text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h2 class="mb-0">{{ $funnelStats['conversion_rate'] }}%</h2>
                            <p class="text-muted mb-0">Rată Conversie</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">📈 Trafic</h5>
                </div>
                <div class="card-body">
                    <canvas id="visitsChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">📱 Dispozitive</h5>
                </div>
                <div class="card-body">
                    <canvas id="deviceChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Funnel & Users Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">🔄 Pâlnie Conversie</h5>
                </div>
                <div class="card-body">
                    @foreach($funnelStats['stages'] as $index => $stage)
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span>{{ $stage['name'] }}</span>
                            <span class="text-muted">{{ number_format($stage['count']) }} ({{ $stage['percentage'] }}%)</span>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-{{ $index < 3 ? 'primary' : ($index < 5 ? 'info' : ($index < 7 ? 'success' : 'warning')) }}" 
                                 role="progressbar" 
                                 style="width: {{ $stage['percentage'] }}%">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">👥 Statistici Utilizatori</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <h3 class="text-primary mb-1">{{ number_format($userStats['total_craftsmen']) }}</h3>
                                <small class="text-muted">Total Meșteri</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <h3 class="text-success mb-1">{{ number_format($userStats['total_clients']) }}</h3>
                                <small class="text-muted">Total Clienți</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <h3 class="text-info mb-1">{{ number_format($userStats['active_craftsmen']) }}</h3>
                                <small class="text-muted">Meșteri Activi (30 zile)</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <h3 class="text-warning mb-1">{{ number_format($userStats['verified_craftsmen']) }}</h3>
                                <small class="text-muted">Meșteri Verificați</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Engagement & Traffic Sources -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">📊 Conversii</h5>
                </div>
                <div class="card-body">
                    <canvas id="conversionsChart" height="80"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">🌐 Surse Trafic</h5>
                </div>
                <div class="card-body">
                    @forelse($trafficSources as $source)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>
                            @if($source['source'] === 'google')
                                <i class="bi bi-google text-danger me-2"></i>
                            @elseif($source['source'] === 'facebook')
                                <i class="bi bi-facebook text-primary me-2"></i>
                            @elseif($source['source'] === 'direct')
                                <i class="bi bi-link-45deg text-secondary me-2"></i>
                            @else
                                <i class="bi bi-globe text-info me-2"></i>
                            @endif
                            {{ ucfirst($source['source']) }}
                        </span>
                        <span class="badge bg-secondary">{{ number_format($source['count']) }}</span>
                    </div>
                    @empty
                    <p class="text-muted text-center mb-0">Nu există date</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Top Craftsmen & Categories -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">⭐ Top 10 Meșteri</h5>
                    <a href="{{ route('admin.craftsmen') }}" class="btn btn-sm btn-outline-primary">
                        Vezi Toți
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nume</th>
                                    <th>Recenzii</th>
                                    <th>Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCraftsmen as $index => $craftsman)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <a href="{{ route('admin.craftsmen.edit', $craftsman->id) }}">
                                            {{ $craftsman->name }}
                                        </a>
                                    </td>
                                    <td>{{ $craftsman->reviews_count }}</td>
                                    <td>
                                        <span class="text-warning">⭐</span>
                                        {{ number_format($craftsman->reviews_avg_rating ?? 0, 1) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Nu există date</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📁 Top Categorii</h5>
                    <a href="{{ route('admin.craftsmen') }}" class="btn btn-sm btn-outline-primary">
                        Vezi Toate
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Categorie</th>
                                    <th>Meșteri</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCategories as $index => $category)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $category->users_count }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">Nu există date</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">🆕 Înregistrări Recente</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentRegistrations as $user)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $user->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $user->email }}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-{{ $user->role === 'specialist' ? 'success' : 'info' }}">
                                    {{ $user->role === 'specialist' ? 'Meșter' : 'Client' }}
                                </span>
                                <br>
                                <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center text-muted">Nu există înregistrări recente</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">⭐ Recenzii Recente</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentReviews as $review)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $review->user->name ?? 'Anonim' }}</strong>
                                <span class="text-warning">
                                    @for($i = 0; $i < $review->rating; $i++) ⭐ @endfor
                                </span>
                            </div>
                            <small class="text-muted">
                                pentru {{ $review->specialist->name ?? 'N/A' }} • {{ $review->created_at->diffForHumans() }}
                            </small>
                        </div>
                        @empty
                        <div class="list-group-item text-center text-muted">Nu există recenzii recente</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="exportForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Export Raport</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Data Început</label>
                        <input type="date" name="start_date" class="form-control" 
                               value="{{ now()->subDays(30)->format('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data Sfârșit</label>
                        <input type="date" name="end_date" class="form-control" 
                               value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-download me-1"></i> Descarcă
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Period change
function changePeriod(days) {
    window.location.href = '{{ route("admin.analytics.index") }}?period=' + days;
}

// Export modal
document.querySelectorAll('[data-format]').forEach(btn => {
    btn.addEventListener('click', function(e) {
        const format = this.dataset.format;
        const form = document.getElementById('exportForm');
        if (format === 'pdf') {
            form.action = '{{ route("admin.analytics.export-pdf") }}';
        } else {
            form.action = '{{ route("admin.analytics.export-excel") }}';
        }
    });
});

// Visits Chart
const visitsCtx = document.getElementById('visitsChart').getContext('2d');
new Chart(visitsCtx, {
    type: 'line',
    data: @json($visitsChart),
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Device Chart
const deviceCtx = document.getElementById('deviceChart').getContext('2d');
const deviceData = @json($deviceBreakdown);
new Chart(deviceCtx, {
    type: 'doughnut',
    data: {
        labels: deviceData.map(d => d.device_type.charAt(0).toUpperCase() + d.device_type.slice(1)),
        datasets: [{
            data: deviceData.map(d => d.count),
            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b'],
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
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
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endpush
