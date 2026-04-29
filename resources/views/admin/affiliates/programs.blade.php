@extends('layouts.admin')

@section('title', 'Programe Afiliere')
@section('page-title', 'Programe Afiliere')

@section('content')

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
    <a href="{{ route('admin.affiliates.index') }}" class="hover:text-gray-700 transition">Afilieri</a>
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
    <span class="text-gray-900 font-medium">Programe</span>
</nav>

@if(session('success'))
    <div class="mb-4 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Lista Programe --}}
    <div class="xl:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Programe existente</h3>
                <span class="text-sm text-gray-400">{{ $programs->count() }} programe</span>
            </div>

            @if($programs->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($programs as $program)
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-semibold text-gray-900 text-sm">{{ $program->name }}</h4>
                                    <span class="text-xs font-mono text-gray-400">{{ $program->slug }}</span>
                                    @if($program->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1"></span>Activ
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1"></span>Inactiv
                                        </span>
                                    @endif
                                </div>
                                @if($program->description)
                                    <p class="text-sm text-gray-500 mb-3">{{ $program->description }}</p>
                                @endif
                                <div class="flex flex-wrap gap-3">
                                    <div class="flex items-center gap-1.5 text-xs text-gray-600 bg-blue-50 px-2.5 py-1 rounded-lg">
                                        <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Comision:
                                        <strong>{{ number_format($program->commission_value, 2) }}{{ $program->commission_type === 'percentage' ? '%' : ' lei' }}</strong>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-xs text-gray-600 bg-amber-50 px-2.5 py-1 rounded-lg">
                                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                        </svg>
                                        Minim plată: <strong>{{ number_format($program->min_payout, 2) }} lei</strong>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-xs text-gray-600 bg-purple-50 px-2.5 py-1 rounded-lg">
                                        <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Cookie: <strong>{{ $program->cookie_days }} zile</strong>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-xs text-gray-600 bg-gray-50 px-2.5 py-1 rounded-lg">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <strong>{{ $program->affiliates_count }}</strong> afiliați
                                    </div>
                                </div>
                            </div>
                            <button type="button"
                                onclick="editProgram({{ json_encode(['id' => $program->id, 'name' => $program->name, 'description' => $program->description, 'commission_type' => $program->commission_type, 'commission_value' => $program->commission_value, 'min_payout' => $program->min_payout, 'cookie_days' => $program->cookie_days, 'is_active' => $program->is_active]) }})"
                                class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition shrink-0" title="Editează">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-14 text-center">
                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-gray-900 font-semibold text-base mb-1">Nu există programe</h3>
                    <p class="text-gray-500 text-sm">Creează primul program de afiliere din formularul alăturat.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Formular Creare / Editare --}}
    <div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 id="formTitle" class="font-semibold text-gray-900 mb-5">Program Nou</h3>
            <form id="programForm" action="{{ route('admin.affiliates.programs.store') }}" method="POST">
                @csrf
                <span id="methodField"></span>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Nume <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="field_name" required
                            class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                            placeholder="ex: Program Standard">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Slug <span class="text-red-500">*</span></label>
                        <input type="text" name="slug" id="field_slug" required
                            class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 font-mono"
                            placeholder="ex: standard">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Descriere</label>
                        <textarea name="description" id="field_description" rows="2"
                            class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                            placeholder="Descriere opțională..."></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Tip Comision <span class="text-red-500">*</span></label>
                        <select name="commission_type" id="field_commission_type" required
                            class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                            <option value="percentage">Procent (%)</option>
                            <option value="fixed">Fix (lei)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Valoare Comision <span class="text-red-500">*</span></label>
                        <input type="number" name="commission_value" id="field_commission_value" step="0.01" min="0" required
                            class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                            placeholder="ex: 10">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Plată Minimă (lei) <span class="text-red-500">*</span></label>
                        <input type="number" name="min_payout" id="field_min_payout" step="0.01" min="0" required
                            class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                            placeholder="ex: 100">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Durată Cookie (zile) <span class="text-red-500">*</span></label>
                        <input type="number" name="cookie_days" id="field_cookie_days" min="1" max="365" required
                            class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                            placeholder="ex: 30">
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="field_is_active" value="1" checked
                            class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <label for="field_is_active" class="text-sm text-gray-700 font-medium">Program activ</label>
                    </div>
                </div>

                <div class="flex gap-2 mt-6">
                    <button type="submit"
                        class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span id="submitLabel">Creează Program</span>
                    </button>
                    <button type="button" id="cancelEdit" onclick="resetForm()" class="hidden px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                        Anulează
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function editProgram(data) {
    document.getElementById('formTitle').textContent = 'Editează Program';
    document.getElementById('submitLabel').textContent = 'Salvează Modificările';
    document.getElementById('cancelEdit').classList.remove('hidden');

    // Update form action to PUT
    document.getElementById('programForm').action = '{{ url("admin/affiliates/programs") }}/' + data.id;
    document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

    // Fill fields
    document.getElementById('field_name').value = data.name || '';
    document.getElementById('field_slug').value = ''; // slug can't change on edit
    document.getElementById('field_slug').disabled = true;
    document.getElementById('field_slug').placeholder = 'Nu se poate modifica';
    document.getElementById('field_description').value = data.description || '';
    document.getElementById('field_commission_type').value = data.commission_type || 'percentage';
    document.getElementById('field_commission_value').value = data.commission_value || '';
    document.getElementById('field_min_payout').value = data.min_payout || '';
    document.getElementById('field_cookie_days').value = data.cookie_days || '';
    document.getElementById('field_is_active').checked = !!data.is_active;

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetForm() {
    document.getElementById('formTitle').textContent = 'Program Nou';
    document.getElementById('submitLabel').textContent = 'Creează Program';
    document.getElementById('cancelEdit').classList.add('hidden');
    document.getElementById('programForm').action = '{{ route("admin.affiliates.programs.store") }}';
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('field_slug').disabled = false;
    document.getElementById('field_slug').placeholder = 'ex: standard';
    document.getElementById('programForm').reset();
}

// Auto-generate slug from name
document.getElementById('field_name').addEventListener('input', function() {
    const form = document.getElementById('programForm');
    if (form.action.includes('/programs') && !document.getElementById('methodField').innerHTML.includes('PUT')) {
        const slug = this.value.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
        document.getElementById('field_slug').value = slug;
    }
});
</script>
@endpush

@endsection
