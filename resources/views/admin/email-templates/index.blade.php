@extends('layouts.admin')

@section('title', 'Template-uri Email')
@section('page-title', 'Template-uri Email')

@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-gray-500 text-sm mt-1">PersonalizeazÄƒ email-urile trimise automat de platformÄƒ</p>
    </div>
    <div class="flex items-center gap-3">
        <form action="{{ route('admin.email-templates.seed-defaults') }}" method="POST">
            @csrf
            <button type="submit"
                onclick="return confirm('Aceasta va genera template-urile default. ContinuaÈ›i?')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition shadow-sm">
                <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
                GenereazÄƒ Default
            </button>
        </form>
        <a href="{{ route('admin.email-templates.create') }}"
           class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Template Nou
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- Filtre --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Categorie</label>
            <select name="category" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                <option value="">Toate categoriile</option>
                @foreach($categories as $key => $name)
                    <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tip Notificare</label>
            <select name="notification_type" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                <option value="">Toate tipurile</option>
                @foreach($notificationTypes as $key => $name)
                    <option value="{{ $key }}" {{ request('notification_type') === $key ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[140px]">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                <option value="">Toate</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                FiltreazÄƒ
            </button>
            @if(request()->hasAny(['category', 'notification_type', 'status']))
                <a href="{{ route('admin.email-templates.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if($templates->count() > 0)
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Template</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Subiect</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Categorie</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tip</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Default</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">AcÈ›iuni</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($templates as $template)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <p class="font-semibold text-gray-900 text-sm">{{ $template->name }}</p>
                        <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $template->slug }}</p>
                    </td>
                    <td class="px-5 py-4 max-w-xs">
                        <p class="text-sm text-gray-600 truncate">{{ $template->subject }}</p>
                    </td>
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                            {{ $template->category_name }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        @if($template->notification_type)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                {{ $template->notification_type_name }}
                            </span>
                        @else
                            <span class="text-gray-300 text-sm">â€”</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-center">
                        <form action="{{ route('admin.email-templates.toggle-status', $template) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold transition
                                {{ $template->is_active
                                    ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                    : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $template->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $template->is_active ? 'Activ' : 'Inactiv' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-5 py-4 text-center">
                        @if($template->is_default)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-primary-100 text-primary-700">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Default
                            </span>
                        @elseif($template->notification_type)
                            <form action="{{ route('admin.email-templates.set-default', $template) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs text-primary-600 hover:text-primary-800 font-medium underline underline-offset-2 transition">
                                    SeteazÄƒ default
                                </button>
                            </form>
                        @else
                            <span class="text-gray-300 text-sm">â€”</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            {{-- Preview --}}
                            <button type="button"
                                class="preview-btn p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                data-template-id="{{ $template->id }}"
                                data-preview-url="{{ route('admin.email-templates.preview', $template) }}"
                                title="Previzualizare">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            {{-- Edit --}}
                            <a href="{{ route('admin.email-templates.edit', $template) }}"
                               class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                               title="Editare">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            {{-- Duplicate --}}
                            <form action="{{ route('admin.email-templates.duplicate', $template) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition"
                                    title="Duplicare">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </form>
                            {{-- Delete --}}
                            <form action="{{ route('admin.email-templates.destroy', $template) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    onclick="return confirm('Sigur doriÈ›i sÄƒ È™tergeÈ›i acest template?')"
                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                    title="È˜tergere">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($templates->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50">
                {{ $templates->withQueryString()->links() }}
            </div>
        @endif

    @else
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="text-gray-900 font-semibold text-lg mb-1">Nu existÄƒ template-uri</h3>
            <p class="text-gray-500 text-sm mb-6">CreeazÄƒ primul template sau genereazÄƒ template-urile default.</p>
            <div class="flex items-center gap-3">
                <form action="{{ route('admin.email-templates.seed-defaults') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                        <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                        GenereazÄƒ Default
                    </button>
                </form>
                <a href="{{ route('admin.email-templates.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    CreeazÄƒ Template
                </a>
            </div>
        </div>
    @endif
</div>

{{-- Modal Preview --}}
<div id="previewModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Previzualizare Email
            </h3>
            <button onclick="closePreview()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="overflow-y-auto p-6 space-y-4">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Subiect</p>
                <div id="preview-subject" class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-800 font-medium"></div>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">ConÈ›inut</p>
                <div id="preview-body" class="bg-white border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-700 min-h-[200px] leading-relaxed"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function closePreview() {
    const modal = document.getElementById('previewModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('previewModal').addEventListener('click', function(e) {
    if (e.target === this) closePreview();
});

document.querySelectorAll('.preview-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const url = this.dataset.previewUrl;
        fetch(url)
            .then(r => r.json())
            .then(data => {
                document.getElementById('preview-subject').textContent = data.subject;
                document.getElementById('preview-body').innerHTML = data.body.replace(/\n/g, '<br>');
                const modal = document.getElementById('previewModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            })
            .catch(() => alert('Eroare la Ã®ncÄƒrcarea previzualizÄƒrii.'));
    });
});
</script>
@endpush

@endsection
