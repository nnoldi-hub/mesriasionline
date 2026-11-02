@extends('layouts.dashboard')

@section('title', 'De sunat azi')
@section('page-title', '📞 De sunat azi')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@use('App\Support\CallOutcome')
@use('App\Models\CraftsmanLead')

@php
    $me = auth()->user()?->name ?? 'echipa';
    $waText = fn ($lead) => "Bună ziua, sunt {$me} de la meseriasionline.ro, am vorbit adineauri la telefon. "
        . "Îmi puteți trimite aici 3–5 poze cu lucrări făcute de dvs. și vă fac eu profilul, gratuit. "
        . "Detalii: " . route('recruitment.form');
    $pct = $stats['goal'] ? min(100, (int) round($stats['calls'] / $stats['goal'] * 100)) : 0;
@endphp

@section('header-actions')
<div class="flex items-center gap-3">
    <a href="{{ route('admin.leads.guide') }}"
       class="inline-flex items-center gap-2 px-4 py-2 border border-primary-300 text-primary-700 rounded-lg hover:bg-primary-50 text-sm font-medium transition">
        💡 Ghid
    </a>
    <a href="{{ route('admin.leads.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 text-sm transition">
        Toate lead-urile
    </a>
</div>
@endsection

@section('content')

<style>[x-cloak]{display:none !important}</style>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm">{{ $errors->first() }}</div>
@endif

{{-- ─── Progres zilnic ─────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="text-2xl font-bold text-gray-900">{{ $stats['calls'] }} / {{ $stats['goal'] }}</div>
        <div class="text-xs text-gray-500">apeluri azi</div>
        <div class="h-2 bg-gray-100 rounded-full mt-2 overflow-hidden">
            <div class="h-full bg-green-500 rounded-full" style="width: {{ $pct }}%"></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="text-2xl font-bold text-gray-900">{{ $stats['talked'] }}</div>
        <div class="text-xs text-gray-500">conversații reale</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="text-2xl font-bold text-green-600">{{ $stats['accepted'] }}</div>
        <div class="text-xs text-gray-500">au acceptat azi</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="text-2xl font-bold text-gray-900">{{ $stats['accounts'] }}</div>
        <div class="text-xs text-gray-500">conturi create azi</div>
    </div>
</div>

{{-- ─── Adăugare rapidă din anunț ──────────────────────────────────────── --}}
<form method="POST" action="{{ route('admin.leads.quick') }}" class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    @csrf
    <div class="text-sm font-semibold text-gray-800 mb-3">+ Adaugă rapid un număr din anunț</div>
    <div class="grid grid-cols-2 md:grid-cols-6 gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Telefon *</label>
            <input name="phone" value="{{ old('phone') }}" required placeholder="07xx xxx xxx"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Nume</label>
            <input name="name" value="{{ old('name') }}" placeholder="opțional"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Meserie *</label>
            <select name="trade" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                @foreach(CraftsmanLead::$tradeLabels as $k => $v)
                    <option value="{{ $k }}" @selected(old('trade') === $k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Oraș *</label>
            <input name="city" value="{{ old('city') }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Sursă *</label>
            <select name="source" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                @foreach(CallOutcome::SOURCES as $k => $v)
                    <option value="{{ $k }}" @selected(old('source') === $k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Link anunț</label>
            <input name="source_url" value="{{ old('source_url') }}" placeholder="https://..."
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
    </div>
    <button type="submit" class="mt-3 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm font-medium transition">
        Adaugă în lista de azi
    </button>
</form>

{{-- ─── Script apel ────────────────────────────────────────────────────── --}}
<details class="bg-white rounded-xl border border-gray-200 p-4 mb-6 text-sm text-gray-700">
    <summary class="cursor-pointer font-semibold text-gray-800">📋 Script apel (deschide-l în timp ce suni)</summary>
    <div class="mt-3 space-y-2">
        <p><strong>1. Deschidere:</strong> „Bună ziua, domnul ___? Sunt {{ $me }} de la meseriasionline.ro. Am văzut anunțul dvs. de ___ pe ___. Vă rețin un minut?”</p>
        <p><strong>2. De ce suni:</strong> „Facem o platformă unde oamenii din ___ caută meseriași. Suntem la început și caut 10 ___ serioși în zonă. Pentru ei e gratuit, iar profilul vi-l fac eu. Când vine o cerere din zonă, v-o trimit direct.”</p>
        <p><strong>3. Închidere:</strong> „Vă scriu acum pe WhatsApp și îmi trimiteți acolo câteva poze cu lucrări când aveți timp. E în regulă?”</p>
        <p class="text-gray-500 pt-2 border-t border-gray-100">Obiecții:</p>
        <p><em>„Câți clienți aveți?”</em> → „Suntem la început, de aceea e gratuit. Nu vă promit zeci de cereri luna asta, dar primele cereri din zonă merg la primii 10.”</p>
        <p><em>„N-am timp.”</em> → „Tocmai de aceea îl fac eu. De la dvs. îmi trebuie doar pozele.”</p>
        <p><em>„Am destul de lucru.”</em> → „Înseamnă că sunteți căutat. Profilul vă ajută în perioadele mai slabe și puteți alege doar lucrările care vă convin.”</p>
        <p><em>„Care e șmecheria? Cât costă?”</em> → „Acum nu costă nimic. Dacă vom introduce vreodată un abonament, vă anunț înainte și decideți atunci.”</p>
    </div>
</details>

@if($refusals->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 text-sm">
        <span class="font-semibold text-gray-800">Motive de refuz (30 de zile):</span>
        @foreach($refusals as $reason => $total)
            <span class="inline-block bg-gray-100 text-gray-700 rounded-full px-3 py-1 text-xs ml-1 mt-1">
                {{ CallOutcome::REFUSAL_REASONS[$reason] ?? $reason }}: {{ $total }}
            </span>
        @endforeach
    </div>
@endif

{{-- ─── Listele ────────────────────────────────────────────────────────── --}}
@foreach(['Restanțe și programate azi' => $due, 'Nesunați încă' => $fresh] as $title => $list)
    <h2 class="text-base font-bold text-gray-800 mt-8 mb-3">{{ $title }} ({{ $list->count() }})</h2>

    @forelse($list as $lead)
        <div class="bg-white rounded-xl border border-gray-200 p-4 mb-3 grid md:grid-cols-5 gap-4 {{ $lead->isOverdue() ? 'border-l-4 border-l-red-500' : '' }}">
            {{-- Date lead --}}
            <div class="md:col-span-2">
                <a href="{{ route('admin.leads.show', $lead) }}" class="font-bold text-gray-900 hover:text-primary-600">{{ $lead->name }}</a>
                <div class="flex flex-wrap gap-1 my-2">
                    <span class="bg-blue-50 text-blue-700 rounded-full px-2 py-0.5 text-xs">{{ $lead->trade_label }}</span>
                    <span class="bg-gray-100 text-gray-700 rounded-full px-2 py-0.5 text-xs">{{ $lead->city }}</span>
                    @if($lead->source)
                        <span class="bg-gray-100 text-gray-700 rounded-full px-2 py-0.5 text-xs">{{ CallOutcome::SOURCES[$lead->source] ?? $lead->source }}</span>
                    @elseif($lead->utm_source !== 'admin')
                        <span class="bg-gray-100 text-gray-700 rounded-full px-2 py-0.5 text-xs">Formular public</span>
                    @endif
                </div>
                <div class="text-xs text-gray-500">
                    {{ CallOutcome::label($lead->call_outcome) }} · {{ $lead->call_attempts }} încercări
                    @if($lead->next_action_at) · programat {{ $lead->next_action_at->format('d.m H:i') }}@endif
                </div>
                @if($lead->next_action_note)
                    <div class="text-xs text-gray-700 mt-1">➜ {{ $lead->next_action_note }}</div>
                @endif
                @if($lead->latestCall?->note)
                    <div class="text-xs text-gray-500 mt-1">Ultima notă: „{{ $lead->latestCall->note }}”</div>
                @endif
                <div class="flex flex-wrap gap-2 mt-3">
                    <a href="tel:{{ $lead->phone }}" class="px-3 py-1.5 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">📞 {{ $lead->phone }}</a>
                    <a href="{{ CallOutcome::whatsappUrl($lead->phone, $waText($lead)) }}" target="_blank" rel="noopener"
                       class="px-3 py-1.5 bg-green-500 text-white rounded-lg text-sm font-medium hover:bg-green-600">WhatsApp</a>
                    @if($lead->source_url)
                        <a href="{{ $lead->source_url }}" target="_blank" rel="noopener"
                           class="px-3 py-1.5 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Anunț</a>
                    @endif
                </div>
            </div>

            {{-- Rezultat apel --}}
            <form method="POST" action="{{ route('admin.leads.log', $lead) }}" class="md:col-span-3" x-data="{ outcome: '' }">
                @csrf
                <div class="flex flex-wrap gap-2 mb-3">
                    @foreach(CallOutcome::formOutcomes() as $k => $o)
                        <label class="cursor-pointer">
                            <input type="radio" name="outcome" value="{{ $k }}" x-model="outcome" required class="sr-only">
                            <span class="inline-block border rounded-lg px-3 py-1.5 text-xs transition"
                                  :class="outcome === '{{ $k }}' ? 'border-primary-600 bg-primary-50 text-primary-700 font-semibold' : 'border-gray-300 text-gray-700 hover:bg-gray-50'">
                                {{ $o['label'] }}
                            </span>
                        </label>
                    @endforeach
                </div>

                <div class="grid sm:grid-cols-2 gap-3 mb-3">
                    <div x-show="outcome === 'refuzat'" x-cloak>
                        <label class="block text-xs text-gray-500 mb-1">Motiv refuz *</label>
                        <select name="refusal_reason" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="">— alege —</option>
                            @foreach(CallOutcome::REFUSAL_REASONS as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="!['refuzat','numar_gresit','accepta'].includes(outcome)">
                        <label class="block text-xs text-gray-500 mb-1">
                            Revin pe <span x-show="outcome !== 'revino'">(gol = automat)</span><span x-show="outcome === 'revino'" x-cloak>*</span>
                        </label>
                        <input type="datetime-local" name="next_action_at" :required="outcome === 'revino'"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div x-show="!['refuzat','numar_gresit'].includes(outcome)">
                        <label class="block text-xs text-gray-500 mb-1">Ce fac atunci</label>
                        <input name="next_action_note" placeholder="ex: verific dacă a trimis pozele"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>

                <textarea name="note" rows="2" placeholder="Ce s-a discutat? Ce obiecție a ridicat?"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
                <button type="submit" class="mt-2 px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 text-sm font-medium transition">
                    Salvează apelul
                </button>
            </form>
        </div>
    @empty
        <div class="bg-white rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-500 text-center">
            Nimic aici. Adaugă numere noi din OLX, Publi24 sau grupurile de Facebook.
        </div>
    @endforelse
@endforeach

@endsection
