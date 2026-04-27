@extends('layouts.app')

@section('title', 'Rapoarte și Export')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="mb-4">
                <h1 class="h3 mb-1">📊 Rapoarte și Export</h1>
                <p class="text-muted">Generează și descarcă rapoarte detaliate despre activitatea ta</p>
            </div>

            <div class="row g-4">
                <!-- PDF Export Card -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-file-pdf me-2"></i>
                                Export PDF
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-4">
                                Generează un raport PDF complet cu toate statisticile tale: programări, 
                                oferte, recenzii și rate de conversie.
                            </p>
                            
                            <form action="{{ route('reports.craftsman.pdf') }}" method="POST">
                                @csrf
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <label class="form-label">Data Început</label>
                                        <input type="date" name="start_date" class="form-control" 
                                               value="{{ now()->subMonth()->format('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Data Sfârșit</label>
                                        <input type="date" name="end_date" class="form-control" 
                                               value="{{ now()->format('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="bi bi-download me-2"></i>
                                    Descarcă PDF
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Excel Export Card -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-file-excel me-2"></i>
                                Export Excel
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-4">
                                Exportă datele în format Excel pentru analiză detaliată. 
                                Alege tipul de date pe care vrei să le exporți.
                            </p>
                            
                            <form action="{{ route('reports.craftsman.excel') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Tip Raport</label>
                                    <select name="type" class="form-select" required>
                                        <option value="summary">Sumar General</option>
                                        <option value="appointments">Programări</option>
                                        <option value="quotes">Oferte</option>
                                        <option value="reviews">Recenzii</option>
                                    </select>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <label class="form-label">Data Început</label>
                                        <input type="date" name="start_date" class="form-control" 
                                               value="{{ now()->subMonth()->format('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Data Sfârșit</label>
                                        <input type="date" name="end_date" class="form-control" 
                                               value="{{ now()->format('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-download me-2"></i>
                                    Descarcă Excel
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">📈 Statistici Rapide (Ultima lună)</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4 text-center">
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h3 class="text-primary mb-1">{{ auth()->user()->appointments()->where('created_at', '>=', now()->subMonth())->count() }}</h3>
                                <small class="text-muted">Programări</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h3 class="text-success mb-1">{{ auth()->user()->quotesAsMeserias()->where('created_at', '>=', now()->subMonth())->count() }}</h3>
                                <small class="text-muted">Oferte Trimise</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h3 class="text-warning mb-1">{{ auth()->user()->reviewsReceived()->where('created_at', '>=', now()->subMonth())->count() }}</h3>
                                <small class="text-muted">Recenzii Noi</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h3 class="text-info mb-1">{{ auth()->user()->profileViews()->where('created_at', '>=', now()->subMonth())->count() }}</h3>
                                <small class="text-muted">Vizualizări Profil</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info -->
            <div class="alert alert-info mt-4">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Sfat:</strong> Rapoartele PDF sunt ideale pentru prezentări și arhivare, 
                în timp ce rapoartele Excel sunt perfecte pentru analiză detaliată în foi de calcul.
            </div>
        </div>
    </div>
</div>
@endsection
