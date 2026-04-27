@extends('layouts.admin')

@section('title', 'Template-uri Email')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Template-uri Email</h1>
            <p class="text-muted mb-0">Personalizează email-urile trimise de platformă</p>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('admin.email-templates.seed-defaults') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-primary" onclick="return confirm('Aceasta va genera template-urile default. Continuați?')">
                    <i class="fas fa-magic me-1"></i> Generează Default
                </button>
            </form>
            <a href="{{ route('admin.email-templates.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Template Nou
            </a>
        </div>
    </div>

    {{-- Filtre --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Categorie</label>
                    <select name="category" class="form-select">
                        <option value="">Toate categoriile</option>
                        @foreach($categories as $key => $name)
                            <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tip Notificare</label>
                    <select name="notification_type" class="form-select">
                        <option value="">Toate tipurile</option>
                        @foreach($notificationTypes as $key => $name)
                            <option value="{{ $key }}" {{ request('notification_type') === $key ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Toate</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary me-2">
                        <i class="fas fa-filter me-1"></i> Filtrează
                    </button>
                    <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Lista template-uri --}}
    <div class="card">
        <div class="card-body p-0">
            @if($templates->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Nume</th>
                                <th>Subiect</th>
                                <th>Categorie</th>
                                <th>Tip Notificare</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Default</th>
                                <th class="text-end">Acțiuni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($templates as $template)
                                <tr>
                                    <td>
                                        <strong>{{ $template->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $template->slug }}</small>
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 250px;">
                                            {{ $template->subject }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $template->category_name }}</span>
                                    </td>
                                    <td>
                                        @if($template->notification_type)
                                            <span class="badge bg-info">{{ $template->notification_type_name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.email-templates.toggle-status', $template) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $template->is_active ? 'btn-success' : 'btn-outline-secondary' }}">
                                                {{ $template->is_active ? 'Activ' : 'Inactiv' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        @if($template->is_default)
                                            <span class="badge bg-primary">
                                                <i class="fas fa-check"></i> Default
                                            </span>
                                        @elseif($template->notification_type)
                                            <form action="{{ route('admin.email-templates.set-default', $template) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                    Setează default
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-info preview-btn" 
                                                    data-template-id="{{ $template->id }}"
                                                    data-preview-url="{{ route('admin.email-templates.preview', $template) }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="{{ route('admin.email-templates.edit', $template) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.email-templates.duplicate', $template) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-secondary">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.email-templates.destroy', $template) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" 
                                                        onclick="return confirm('Sigur doriți să ștergeți acest template?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3">
                    {{ $templates->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-envelope-open-text fa-3x text-muted mb-3"></i>
                    <h5>Nu există template-uri</h5>
                    <p class="text-muted">Creează primul template sau generează template-urile default.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <form action="{{ route('admin.email-templates.seed-defaults') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-magic me-1"></i> Generează Default
                            </button>
                        </form>
                        <a href="{{ route('admin.email-templates.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Creează Template
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Preview --}}
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Previzualizare Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Subiect:</label>
                    <div id="preview-subject" class="border rounded p-2 bg-light"></div>
                </div>
                <div>
                    <label class="form-label fw-bold">Conținut:</label>
                    <div id="preview-body" class="border rounded p-3 bg-white" style="min-height: 200px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.preview-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const url = this.dataset.previewUrl;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                document.getElementById('preview-subject').textContent = data.subject;
                document.getElementById('preview-body').innerHTML = data.body.replace(/\n/g, '<br>');
                new bootstrap.Modal(document.getElementById('previewModal')).show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Eroare la încărcarea previzualizării.');
            });
    });
});
</script>
@endpush
@endsection
