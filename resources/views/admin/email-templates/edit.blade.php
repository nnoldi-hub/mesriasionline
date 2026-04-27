@extends('layouts.admin')

@section('title', 'Editare Template: ' . $template->name)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.email-templates.index') }}">Template-uri Email</a></li>
                    <li class="breadcrumb-item active">{{ $template->name }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 mt-2">Editare Template</h1>
        </div>
        <div>
            @if($template->is_default)
                <span class="badge bg-primary me-2">
                    <i class="fas fa-check"></i> Template Default
                </span>
            @endif
            <span class="badge {{ $template->is_active ? 'bg-success' : 'bg-secondary' }}">
                {{ $template->is_active ? 'Activ' : 'Inactiv' }}
            </span>
        </div>
    </div>

    <form action="{{ route('admin.email-templates.update', $template) }}" method="POST" id="templateForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            {{-- Formular --}}
            <div class="col-lg-7">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informații Template</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nume Template *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $template->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Slug: {{ $template->slug }}</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Categorie *</label>
                                    <select class="form-select @error('category') is-invalid @enderror" 
                                            id="category" name="category" required>
                                        <option value="">Selectează categoria</option>
                                        @foreach($categories as $key => $name)
                                            <option value="{{ $key }}" {{ old('category', $template->category) === $key ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="notification_type" class="form-label">Tip Notificare</label>
                                    <select class="form-select @error('notification_type') is-invalid @enderror" 
                                            id="notification_type" name="notification_type">
                                        <option value="">Fără legătură directă</option>
                                        @foreach($notificationTypes as $key => $name)
                                            <option value="{{ $key }}" {{ old('notification_type', $template->notification_type) === $key ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('notification_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Subiect Email *</label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" name="subject" value="{{ old('subject', $template->subject) }}" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="body" class="form-label">Conținut Email *</label>
                            <textarea class="form-control @error('body') is-invalid @enderror" 
                                      id="body" name="body" rows="15" required>{{ old('body', $template->body) }}</textarea>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Suportă Markdown simplu: <code># Titlu</code>, <code>**bold**</code>, <code>*italic*</code>, <code>[Text Buton](url)</code>
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Template Activ</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1"
                                           {{ old('is_default', $template->is_default) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">Template Default</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Înapoi
                    </a>
                    <div>
                        <button type="button" class="btn btn-outline-info me-2" id="previewBtn">
                            <i class="fas fa-eye me-1"></i> Previzualizare
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Actualizează Template
                        </button>
                    </div>
                </div>
            </div>

            {{-- Variabile disponibile --}}
            <div class="col-lg-5">
                <div class="card sticky-top" style="top: 80px;">
                    <div class="card-header">
                        <h5 class="mb-0">Variabile Disponibile</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">
                            Click pe o variabilă pentru a o insera în conținutul email-ului.
                        </p>

                        <div id="variablesContainer">
                            @foreach($availableVariables as $type => $variables)
                                <div class="variables-group {{ $template->notification_type !== $type ? 'd-none' : '' }}" data-type="{{ $type }}">
                                    <h6 class="border-bottom pb-2 mb-3">
                                        {{ $notificationTypes[$type] ?? $type }}
                                    </h6>
                                    <div class="row g-2">
                                        @foreach($variables as $var => $description)
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded variable-item" 
                                                     style="cursor: pointer;" data-variable="{{ $var }}">
                                                    <code class="text-primary">{{ $var }}</code>
                                                    <small class="text-muted">{{ $description }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                            <div id="noVariables" class="text-center text-muted py-4 {{ $template->notification_type ? 'd-none' : '' }}">
                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                <p>Selectează un tip de notificare pentru a vedea variabilele disponibile.</p>
                            </div>
                        </div>

                        <hr>

                        <h6>Sintaxă Markdown</h6>
                        <div class="small">
                            <div class="d-flex justify-content-between py-1">
                                <code># Titlu</code>
                                <span class="text-muted">Greeting</span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <code>**text**</code>
                                <span class="text-muted">Bold</span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <code>*text*</code>
                                <span class="text-muted">Italic</span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <code>[Text](url)</code>
                                <span class="text-muted">Buton</span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <code>-- Semnătură</code>
                                <span class="text-muted">Salutare</span>
                            </div>
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <small class="text-muted">Ultima modificare: {{ $template->updated_at->format('d.m.Y H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
                    <div id="preview-body" class="border rounded p-3 bg-white" style="min-height: 200px; white-space: pre-wrap;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const notificationTypeSelect = document.getElementById('notification_type');
    const variablesContainer = document.getElementById('variablesContainer');
    const noVariables = document.getElementById('noVariables');

    notificationTypeSelect.addEventListener('change', function() {
        const type = this.value;
        
        document.querySelectorAll('.variables-group').forEach(group => {
            group.classList.add('d-none');
        });

        if (type) {
            const group = variablesContainer.querySelector(`[data-type="${type}"]`);
            if (group) {
                group.classList.remove('d-none');
                noVariables.classList.add('d-none');
            }
        } else {
            noVariables.classList.remove('d-none');
        }
    });

    document.querySelectorAll('.variable-item').forEach(item => {
        item.addEventListener('click', function() {
            const variable = this.dataset.variable;
            const bodyField = document.getElementById('body');
            
            const start = bodyField.selectionStart;
            const end = bodyField.selectionEnd;
            const text = bodyField.value;
            
            bodyField.value = text.substring(0, start) + variable + text.substring(end);
            bodyField.focus();
            bodyField.selectionStart = bodyField.selectionEnd = start + variable.length;

            this.classList.add('bg-success', 'text-white');
            setTimeout(() => {
                this.classList.remove('bg-success', 'text-white');
            }, 300);
        });
    });

    document.getElementById('previewBtn').addEventListener('click', function() {
        const subject = document.getElementById('subject').value;
        const body = document.getElementById('body').value;
        const notificationType = document.getElementById('notification_type').value;

        fetch('{{ route("admin.email-templates.preview-live") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                subject: subject,
                body: body,
                notification_type: notificationType
            })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('preview-subject').textContent = data.subject;
            document.getElementById('preview-body').textContent = data.body;
            new bootstrap.Modal(document.getElementById('previewModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Eroare la previzualizare.');
        });
    });
});
</script>
@endpush
@endsection
