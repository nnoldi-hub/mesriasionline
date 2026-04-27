@extends('layouts.admin')

@section('title', 'Import Date')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Import Date</h1>
        <p class="mt-2 text-gray-600">Importă meseriași și servicii în masă din fișiere CSV</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Import Craftsmen -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <svg class="w-8 h-8 text-primary-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h2 class="text-xl font-semibold text-gray-900">Import Meseriași</h2>
            </div>
            
            <p class="text-sm text-gray-600 mb-4">
                Încarcă un fișier CSV cu lista de meseriași pentru import în masă.
            </p>
            
            <form id="import-craftsmen-form" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Fișier CSV
                    </label>
                    <input type="file" 
                           name="file" 
                           accept=".csv,.txt"
                           class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                           required>
                    <p class="mt-1 text-xs text-gray-500">
                        Format acceptat: CSV (max 5MB)
                    </p>
                </div>
                
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="skip_duplicates" checked class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="ml-2 text-sm text-gray-700">Sari peste email-uri duplicate</span>
                    </label>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" 
                            onclick="previewImport()"
                            class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition">
                        Previzualizare
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                        Importă
                    </button>
                </div>
            </form>
            
            <div class="mt-4 pt-4 border-t">
                <a href="{{ route('import.templates.craftsmen') }}" 
                   class="text-sm text-primary-600 hover:text-primary-700 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Descarcă template CSV
                </a>
            </div>
        </div>

        <!-- Import Services -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <svg class="w-8 h-8 text-primary-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <h2 class="text-xl font-semibold text-gray-900">Import Servicii</h2>
            </div>
            
            <p class="text-sm text-gray-600 mb-4">
                Încarcă un fișier CSV cu lista de servicii pentru adăugare în platformă.
            </p>
            
            <form id="import-services-form" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Fișier CSV
                    </label>
                    <input type="file" 
                           name="file" 
                           accept=".csv,.txt"
                           class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                           required>
                    <p class="mt-1 text-xs text-gray-500">
                        Format acceptat: CSV (max 2MB)
                    </p>
                </div>
                
                <button type="submit" 
                        class="w-full bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                    Importă Servicii
                </button>
            </form>
            
            <div class="mt-4 pt-4 border-t">
                <a href="{{ route('import.templates.services') }}" 
                   class="text-sm text-primary-600 hover:text-primary-700 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Descarcă template CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="preview-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-5xl shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-900">Previzualizare Import</h3>
                <button onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="preview-content"></div>
        </div>
    </div>

    <!-- Results Modal -->
    <div id="results-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-900">Rezultate Import</h3>
                <button onclick="closeResults()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="results-content"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewImport() {
    const form = document.getElementById('import-craftsmen-form');
    const formData = new FormData(form);
    
    fetch('{{ route('import.craftsmen.preview') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showPreview(data.preview);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Eroare la previzualizare');
    });
}

function showPreview(preview) {
    const html = `
        <div class="space-y-4">
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Total rânduri</p>
                    <p class="text-2xl font-bold text-blue-600">${preview.total}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Valide</p>
                    <p class="text-2xl font-bold text-green-600">${preview.valid}</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Erori</p>
                    <p class="text-2xl font-bold text-red-600">${preview.invalid}</p>
                </div>
            </div>
            
            ${preview.errorRows.length > 0 ? `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h4 class="font-semibold text-red-800 mb-2">Erori detectate:</h4>
                    <div class="space-y-2 max-h-60 overflow-y-auto">
                        ${preview.errorRows.map(error => `
                            <div class="text-sm">
                                <span class="font-medium">Rând ${error.row}:</span>
                                <span class="text-red-700">${error.errors.join(', ')}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            ` : ''}
            
            <div class="flex justify-end gap-3 pt-4">
                <button onclick="closePreview()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Închide
                </button>
                ${preview.valid > 0 ? `
                    <button onclick="closePreview(); document.getElementById('import-craftsmen-form').requestSubmit();" 
                            class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        Continuă Import (${preview.valid} rânduri)
                    </button>
                ` : ''}
            </div>
        </div>
    `;
    
    document.getElementById('preview-content').innerHTML = html;
    document.getElementById('preview-modal').classList.remove('hidden');
}

function closePreview() {
    document.getElementById('preview-modal').classList.add('hidden');
}

function closeResults() {
    document.getElementById('results-modal').classList.add('hidden');
    location.reload();
}

// Handle craftsmen import
document.getElementById('import-craftsmen-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route('import.craftsmen') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        showResults(data);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Eroare la import');
    });
});

// Handle services import
document.getElementById('import-services-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route('import.services') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        showResults(data);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Eroare la import');
    });
});

function showResults(data) {
    const html = `
        <div class="space-y-4">
            <div class="${data.success ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'} border rounded-lg p-4">
                <p class="font-semibold ${data.success ? 'text-green-800' : 'text-red-800'}">${data.message}</p>
            </div>
            
            ${data.stats ? `
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-green-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">Importate</p>
                        <p class="text-2xl font-bold text-green-600">${data.stats.imported}</p>
                    </div>
                    ${data.stats.skipped !== undefined ? `
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Sărite (duplicate)</p>
                            <p class="text-2xl font-bold text-yellow-600">${data.stats.skipped}</p>
                        </div>
                    ` : ''}
                    <div class="bg-red-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">Erori</p>
                        <p class="text-2xl font-bold text-red-600">${data.stats.errors}</p>
                    </div>
                </div>
            ` : ''}
            
            ${data.errors && data.errors.length > 0 ? `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 max-h-60 overflow-y-auto">
                    <h4 class="font-semibold text-red-800 mb-2">Erori:</h4>
                    ${data.errors.map(error => `
                        <div class="text-sm text-red-700 mb-1">
                            Rând ${error.row}: ${error.errors.join(', ')}
                        </div>
                    `).join('')}
                </div>
            ` : ''}
            
            <div class="flex justify-end pt-4">
                <button onclick="closeResults()" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    Închide
                </button>
            </div>
        </div>
    `;
    
    document.getElementById('results-content').innerHTML = html;
    document.getElementById('results-modal').classList.remove('hidden');
}
</script>
@endpush
@endsection
