@extends('layouts.admin')

@section('title', 'Setări Notificări')
@section('page-title', 'Setări Notificări')

@section('content')

{{-- SMTP Status Card --}}
<div class="mb-6 rounded-xl border {{ $smtpOk ? 'border-green-200 bg-green-50' : 'border-amber-200 bg-amber-50' }} p-5">
    <div class="flex items-start gap-4">
        <div class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center {{ $smtpOk ? 'bg-green-100' : 'bg-amber-100' }}">
            @if($smtpOk)
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            @else
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            @endif
        </div>
        <div class="flex-1">
            <h3 class="font-semibold {{ $smtpOk ? 'text-green-800' : 'text-amber-800' }} text-sm mb-1">
                Configurare Email (SMTP): {{ $smtpOk ? 'Activ' : 'Neconfiguratrat / Log mode' }}
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs {{ $smtpOk ? 'text-green-700' : 'text-amber-700' }}">
                <div>
                    <span class="font-medium uppercase tracking-wide opacity-70">Driver</span>
                    <p class="font-mono mt-0.5">{{ $mailDriver }}</p>
                </div>
                <div>
                    <span class="font-medium uppercase tracking-wide opacity-70">Host</span>
                    <p class="font-mono mt-0.5">{{ $mailHost ?: '—' }}</p>
                </div>
                <div>
                    <span class="font-medium uppercase tracking-wide opacity-70">Port</span>
                    <p class="font-mono mt-0.5">{{ $mailPort ?: '—' }}</p>
                </div>
                <div>
                    <span class="font-medium uppercase tracking-wide opacity-70">From</span>
                    <p class="font-mono mt-0.5">{{ $mailFrom ?: '—' }}</p>
                </div>
            </div>
            @if(!$smtpOk)
                <p class="mt-2 text-xs text-amber-700">
                    Emailurile sunt scrise în fișierul log (<code>storage/logs/laravel.log</code>), nu sunt trimise real.
                    Configurează <code>MAIL_MAILER=smtp</code> și credențialele în fișierul <code>.env</code>.
                </p>
            @endif
        </div>

        {{-- Test Email --}}
        <div class="shrink-0">
            <button type="button" onclick="document.getElementById('testEmailModal').classList.remove('hidden')"
                class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border {{ $smtpOk ? 'border-green-300 text-green-700 hover:bg-green-50' : 'border-amber-300 text-amber-700 hover:bg-amber-50' }} text-xs font-medium rounded-lg transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Test Email
            </button>
        </div>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="mb-4 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
@endif
@if(session('test_success'))
    <div class="mb-4 flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg text-sm">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('test_success') }}
    </div>
@endif
@if(session('test_error'))
    <div class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9a1 1 0 012 0v4a1 1 0 11-2 0V9zm1 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
        {{ session('test_error') }}
    </div>
@endif

{{-- Settings Form --}}
<form action="{{ route('admin.notifications.settings.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h3 class="font-semibold text-gray-900">Tipuri de Notificări</h3>
                <p class="text-xs text-gray-500 mt-0.5">Controlează care notificări sunt trimise și prin ce canale</p>
            </div>
            <button type="submit"
                class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Salvează Setările
            </button>
        </div>

        {{-- Table Header --}}
        <div class="hidden md:grid grid-cols-12 gap-4 px-6 py-3 bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
            <div class="col-span-4">Tipul Notificării</div>
            <div class="col-span-2 text-center">
                <div class="flex items-center justify-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                    Activ Global
                </div>
            </div>
            <div class="col-span-2 text-center">
                <div class="flex items-center justify-center gap-1">
                    <svg class="w-3.5 h-3.5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
                    Email
                </div>
            </div>
            <div class="col-span-2 text-center">
                <div class="flex items-center justify-center gap-1">
                    <svg class="w-3.5 h-3.5 text-purple-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>
                    In-App
                </div>
            </div>
            <div class="col-span-2 text-center">
                <div class="flex items-center justify-center gap-1">
                    <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Push
                </div>
            </div>
        </div>

        @php
            $types = [
                'new_quote_request' => ['icon' => '📋', 'color' => 'blue',   'recipient' => 'Meșter'],
                'quote_received'    => ['icon' => '💰', 'color' => 'amber',  'recipient' => 'Client'],
                'quote_accepted'    => ['icon' => '🎉', 'color' => 'green',  'recipient' => 'Meșter'],
                'new_appointment'   => ['icon' => '📅', 'color' => 'indigo', 'recipient' => 'Meșter'],
                'new_message'       => ['icon' => '💬', 'color' => 'violet', 'recipient' => 'Ambii'],
                'new_review'        => ['icon' => '⭐', 'color' => 'yellow', 'recipient' => 'Meșter'],
            ];
        @endphp

        <div class="divide-y divide-gray-100">
            @foreach($types as $type => $meta)
                @php $s = $settings[$type] ?? null; @endphp
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center px-6 py-4">
                    {{-- Name + description --}}
                    <div class="col-span-4">
                        <div class="flex items-center gap-2">
                            <span class="text-xl leading-none">{{ $meta['icon'] }}</span>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">{{ $s?->label ?? $type }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $s?->description }}</p>
                                <span class="inline-flex items-center mt-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-500">
                                    Destinatar: {{ $meta['recipient'] }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Global toggle --}}
                    <div class="col-span-2 flex md:justify-center items-center gap-2">
                        <span class="text-xs text-gray-500 md:hidden font-medium">Activ:</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox"
                                name="settings[{{ $type }}][is_enabled]"
                                value="1"
                                {{ ($s?->is_enabled ?? true) ? 'checked' : '' }}
                                onchange="toggleRow('{{ $type }}', this.checked)"
                                class="sr-only peer">
                            <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:bg-green-500 transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                        </label>
                    </div>

                    {{-- Email toggle --}}
                    <div id="channels-{{ $type }}" class="col-span-6 grid grid-cols-3 gap-4 {{ ($s?->is_enabled ?? true) ? '' : 'opacity-40 pointer-events-none' }}">
                        <div class="flex md:justify-center items-center gap-2">
                            <span class="text-xs text-gray-500 md:hidden font-medium">Email:</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                    name="settings[{{ $type }}][email_enabled]"
                                    value="1"
                                    {{ ($s?->email_enabled ?? true) ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:bg-blue-500 transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                            </label>
                        </div>

                        {{-- Database toggle --}}
                        <div class="flex md:justify-center items-center gap-2">
                            <span class="text-xs text-gray-500 md:hidden font-medium">In-App:</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                    name="settings[{{ $type }}][database_enabled]"
                                    value="1"
                                    {{ ($s?->database_enabled ?? true) ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:bg-purple-500 transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                            </label>
                        </div>

                        {{-- Push toggle --}}
                        <div class="flex md:justify-center items-center gap-2">
                            <span class="text-xs text-gray-500 md:hidden font-medium">Push:</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                    name="settings[{{ $type }}][push_enabled]"
                                    value="1"
                                    {{ ($s?->push_enabled ?? true) ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:bg-green-500 transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                            </label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end">
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Salvează Setările
            </button>
        </div>
    </div>
</form>

{{-- Info section --}}
<div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-4 h-4 text-blue-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
            <span class="text-xs font-semibold text-blue-700">Canal Email</span>
        </div>
        <p class="text-xs text-blue-600">Trimite email la adresa înregistrată a utilizatorului. Necesită configurare SMTP validă în <code>.env</code>.</p>
    </div>
    <div class="bg-purple-50 border border-purple-100 rounded-xl p-4">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-4 h-4 text-purple-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>
            <span class="text-xs font-semibold text-purple-700">Canal In-App (Database)</span>
        </div>
        <p class="text-xs text-purple-600">Salvează notificarea în baza de date. Vizibilă în clopoțelul din interfața utilizatorului, fără email.</p>
    </div>
    <div class="bg-green-50 border border-green-100 rounded-xl p-4">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <span class="text-xs font-semibold text-green-700">Canal Push (WebPush)</span>
        </div>
        <p class="text-xs text-green-600">Trimite notificare push în browser. Funcționează doar pentru utilizatorii care au acceptat permisiunea de push notifications.</p>
    </div>
</div>

{{-- Test Email Modal --}}
<div id="testEmailModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-semibold text-gray-900">Trimite Email de Test</h3>
            <button type="button" onclick="document.getElementById('testEmailModal').classList.add('hidden')" class="p-1.5 hover:bg-gray-100 rounded-lg">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('admin.notifications.test-email') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Adresă Email Destinatar
                </label>
                <input type="email" name="test_email" required
                    class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    placeholder="test@example.com"
                    value="{{ auth()->user()->email }}">
                <p class="mt-1 text-xs text-gray-400">Se va trimite un email simplu de test pentru a verifica funcționalitatea SMTP.</p>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Trimite Test
                </button>
                <button type="button" onclick="document.getElementById('testEmailModal').classList.add('hidden')"
                    class="px-4 py-2.5 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                    Anulează
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleRow(type, enabled) {
    const row = document.getElementById('channels-' + type);
    if (!row) return;
    if (enabled) {
        row.classList.remove('opacity-40', 'pointer-events-none');
    } else {
        row.classList.add('opacity-40', 'pointer-events-none');
    }
}
</script>
@endpush

@endsection
