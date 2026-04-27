@extends('layouts.app')

@section('title', 'Rapoartele Mele')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="mb-4">
                <h1 class="h3 mb-1">📊 Rapoartele Mele</h1>
                <p class="text-muted">Generează rapoarte despre activitatea ta pe platformă</p>
            </div>

            <!-- PDF Export Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-file-pdf me-2"></i>
                        Export Raport PDF
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Generează un raport PDF cu toate programările, cererile de ofertă și 
                        recenziile tale din perioada selectată.
                    </p>
                    
                    <form action="{{ route('reports.client.pdf') }}" method="POST">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Data Început</label>
                                <input type="date" name="start_date" class="form-control" 
                                       value="{{ now()->subMonths(3)->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Data Sfârșit</label>
                                <input type="date" name="end_date" class="form-control" 
                                       value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-download me-2"></i>
                            Descarcă Raport PDF
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">📈 Sumar Activitate</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4 text-center">
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3">
                                <h3 class="text-primary mb-1">{{ auth()->user()->appointments()->count() }}</h3>
                                <small class="text-muted">Programări</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3">
                                <h3 class="text-success mb-1">{{ auth()->user()->quoteRequests()->count() }}</h3>
                                <small class="text-muted">Cereri Ofertă</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3">
                                <h3 class="text-warning mb-1">{{ auth()->user()->reviewsGiven()->count() }}</h3>
                                <small class="text-muted">Recenzii Date</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3">
                                <h3 class="text-info mb-1">{{ auth()->user()->messages()->count() }}</h3>
                                <small class="text-muted">Mesaje</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
