@extends('layouts.dashboard')

@section('title', 'Lead: ' . $lead->name)
@section('page-title', 'Detaliu Lead')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('header-actions')
<a href="{{ route('admin.leads.index') }}"
   class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 text-sm transition">
    ← Înapoi la liste
</a>
@endsection

@section('content')

{{-- Flash messages --}}
@if(session('success'))
    <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Coloana stânga — Date lead --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Card date de contact --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-base font-bold text-gray-800 mb-4">Date de contact</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <div class="text-xs text-gray-500 mb-0.5">Nume</div>
                    <div class="font-medium text-gray-900">{{ $lead->name }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-0.5">Meserie</div>
                    <div>
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full">
                            {{ $lead->tradeLabel }}
                        </span>
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-0.5">Telefon</div>
                    <a href="tel:{{ $lead->phone }}" class="font-medium text-primary-600 hover:underline">{{ $lead->phone }}</a>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-0.5">Email</div>
                    @if($lead->email)
                        <a href="mailto:{{ $lead->email }}" class="font-medium text-primary-600 hover:underline">{{ $lead->email }}</a>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-0.5">Oraș</div>
                    <div class="font-medium text-gray-900">{{ $lead->city }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-0.5">Experiență</div>
                    <div class="font-medium text-gray-900">{{ $lead->experience_range }} ani</div>
                </div>
                @if($lead->utm_source)
                <div class="col-span-2">
                    <div class="text-xs text-gray-500 mb-0.5">Sursă trafic</div>
                    <div class="font-medium text-gray-900">
                        {{ $lead->utm_source }}
                        @if($lead->utm_medium) / {{ $lead->utm_medium }} @endif
                        @if($lead->utm_campaign) / {{ $lead->utm_campaign }} @endif
                    </div>
                </div>
                @endif
                <div>
                    <div class="text-xs text-gray-500 mb-0.5">Înregistrat la</div>
                    <div class="font-medium text-gray-900">{{ $lead->created_at->format('d.m.Y H:i') }}</div>
                </div>
                @if($lead->invite_sent_at)
                <div>
                    <div class="text-xs text-gray-500 mb-0.5">Invitație trimisă la</div>
                    <div class="font-medium text-gray-900">{{ $lead->invite_sent_at->format('d.m.Y H:i') }}</div>
                </div>
                @endif
                @if($lead->account_created_at)
                <div>
                    <div class="text-xs text-gray-500 mb-0.5">Cont creat la</div>
                    <div class="font-medium text-green-700">{{ $lead->account_created_at->format('d.m.Y H:i') }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Poze --}}
        @if($lead->profile_photo || $lead->work_photo)
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-base font-bold text-gray-800 mb-4">Poze încărcate</h2>
            <div class="grid grid-cols-2 gap-4">
                @if($lead->profile_photo)
                    <div>
                        <div class="text-xs text-gray-500 mb-2">Poză profil</div>
                        <img src="{{ Storage::url($lead->profile_photo) }}"
                             alt="Profil" class="w-full h-40 object-cover rounded-xl border border-gray-200">
                    </div>
                @endif
                @if($lead->work_photo)
                    <div>
                        <div class="text-xs text-gray-500 mb-2">Poză lucrare</div>
                        <img src="{{ Storage::url($lead->work_photo) }}"
                             alt="Lucrare" class="w-full h-40 object-cover rounded-xl border border-gray-200">
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Link cont creat --}}
        @if($lead->user)
        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
            <p class="text-sm text-green-800 font-medium">
                ✅ Cont creat:
                <a href="{{ route('craftsman.show', $lead->user->slug) }}" target="_blank"
                   class="underline hover:no-underline">
                    {{ $lead->user->name }}
                </a>
            </p>
        </div>
        @endif

    </div>

    {{-- Coloana dreapta — Acțiuni admin --}}
    <div class="space-y-4">

        {{-- Status badge --}}
        @php
            $colors = [
                'nou'         => 'bg-blue-100 text-blue-800 border-blue-200',
                'contactat'   => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                'invitat'     => 'bg-purple-100 text-purple-800 border-purple-200',
                'inregistrat' => 'bg-green-100 text-green-800 border-green-200',
                'respins'     => 'bg-red-100 text-red-800 border-red-200',
            ];
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-xs text-gray-500 mb-2">Status curent</div>
            <span class="text-sm font-bold px-4 py-1.5 rounded-full border {{ $colors[$lead->status] ?? 'bg-gray-100 text-gray-700' }}">
                {{ $lead->statusLabel }}
            </span>
        </div>

        {{-- Buton trimite invitație email --}}
        @if($lead->email && $lead->status !== 'inregistrat')
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <h3 class="text-sm font-bold text-gray-700 mb-3">Invitație prin email</h3>
            <form method="POST" action="{{ route('admin.leads.invite', $lead) }}">
                @csrf
                <button type="submit"
                        class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 rounded-lg text-sm transition">
                    📧 Trimite invitație la {{ $lead->email }}
                </button>
            </form>
        </div>
        @endif

        {{-- Buton copiere link activare (pentru WhatsApp) --}}
        @if($lead->status !== 'inregistrat')
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <h3 class="text-sm font-bold text-gray-700 mb-3">Link activare (WhatsApp)</h3>
            <button onclick="copyActivationLink({{ $lead->id }})"
                    class="w-full bg-green-500 hover:bg-green-600 text-white font-medium py-2.5 rounded-lg text-sm transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                    <path d="M11.999 2C6.478 2 2 6.478 2 11.999c0 1.977.576 3.818 1.571 5.362L2 22l4.791-1.546A9.96 9.96 0 0011.999 22C17.522 22 22 17.522 22 11.999 22 6.478 17.522 2 11.999 2z"/>
                </svg>
                Copiază link activare
            </button>
            <p id="copy-feedback-{{ $lead->id }}" class="text-xs text-green-600 mt-2 hidden text-center">
                ✅ Link copiat! Trimite-l pe WhatsApp.
            </p>
        </div>
        @endif

        {{-- Schimbare status + note --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <h3 class="text-sm font-bold text-gray-700 mb-3">Actualizare status</h3>
            <form method="POST" action="{{ route('admin.leads.update', $lead) }}" class="space-y-3">
                @csrf
                @method('PATCH')
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
                    <option value="nou"         {{ $lead->status === 'nou' ? 'selected' : '' }}>Nou</option>
                    <option value="contactat"   {{ $lead->status === 'contactat' ? 'selected' : '' }}>Contactat</option>
                    <option value="invitat"     {{ $lead->status === 'invitat' ? 'selected' : '' }}>Invitat</option>
                    <option value="inregistrat" {{ $lead->status === 'inregistrat' ? 'selected' : '' }}>Înregistrat</option>
                    <option value="respins"     {{ $lead->status === 'respins' ? 'selected' : '' }}>Respins</option>
                </select>
                <textarea name="admin_notes" rows="3" placeholder="Note interne..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 resize-none">{{ old('admin_notes', $lead->admin_notes) }}</textarea>
                <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white font-medium py-2.5 rounded-lg text-sm transition">
                    Salvează
                </button>
            </form>
        </div>

        {{-- Ștergere --}}
        <div class="bg-white rounded-xl border border-red-100 p-4">
            <form method="POST" action="{{ route('admin.leads.destroy', $lead) }}"
                  onsubmit="return confirm('Ești sigur că vrei să ștergi acest lead? Acțiunea este ireversibilă.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full text-red-600 hover:bg-red-50 border border-red-200 font-medium py-2.5 rounded-lg text-sm transition">
                    🗑 Șterge lead
                </button>
            </form>
        </div>

    </div>
</div>

@push('scripts')
<script>
function copyActivationLink(leadId) {
    fetch(`/admin/leads/${leadId}/activation-link`)
        .then(r => r.json())
        .then(data => {
            if (data.link) {
                navigator.clipboard.writeText(data.link).then(() => {
                    const el = document.getElementById(`copy-feedback-${leadId}`);
                    if (el) { el.classList.remove('hidden'); setTimeout(() => el.classList.add('hidden'), 3000); }
                });
            } else {
                alert(data.error || 'Eroare la obținerea link-ului.');
            }
        })
        .catch(() => alert('Eroare la server.'));
}
</script>
@endpush

@endsection
