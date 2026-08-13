@extends('layouts.app')

@section('title', 'Cerere trimisă — Meseriași Online')

@section('content')
<div class="min-h-screen flex items-center justify-center" style="background-color: #ECF0F1;">
    <div class="max-w-lg mx-auto px-4 py-16 text-center">

        <div class="bg-white rounded-2xl shadow-xl p-10">
            {{-- Icon succes --}}
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Cererea a fost trimisă!</h1>
            <p class="text-gray-500 mb-6">
                Am notificat
                <strong class="text-gray-800">{{ $jobRequest->notified_craftsmen }}
                {{ $jobRequest->notified_craftsmen == 1 ? 'meseriaș' : 'meseriași' }}</strong>
                din zona ta.
            </p>

            {{-- Rezumat cerere --}}
            <div class="bg-gray-50 rounded-xl p-5 text-left mb-6 space-y-2">
                <div class="flex items-start space-x-2">
                    <span class="text-gray-400 text-sm w-24 shrink-0">Lucrare:</span>
                    <span class="text-gray-800 text-sm font-medium">{{ $jobRequest->title }}</span>
                </div>
                <div class="flex items-start space-x-2">
                    <span class="text-gray-400 text-sm w-24 shrink-0">Categorie:</span>
                    <span class="text-gray-800 text-sm">{{ $jobRequest->category?->name ?? '—' }}</span>
                </div>
                <div class="flex items-start space-x-2">
                    <span class="text-gray-400 text-sm w-24 shrink-0">Locație:</span>
                    <span class="text-gray-800 text-sm">{{ $jobRequest->location_display }}</span>
                </div>
                <div class="flex items-start space-x-2">
                    <span class="text-gray-400 text-sm w-24 shrink-0">Urgență:</span>
                    <span class="text-gray-800 text-sm">{{ $jobRequest->urgency_label }}</span>
                </div>
            </div>

            {{-- Status live --}}
            @if($jobRequest->notified_craftsmen > 0)
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-blue-900">
                            {{ $interestedCount }} / {{ \App\Models\PublicJobRequest::MAX_INTERESTED }} meseriași interesați
                        </span>
                        @if($jobRequest->status !== 'open')
                            <span class="text-xs font-bold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">Completă</span>
                        @endif
                    </div>
                    <div class="w-full bg-blue-100 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all"
                             style="width: {{ min(100, ($interestedCount / \App\Models\PublicJobRequest::MAX_INTERESTED) * 100) }}%"></div>
                    </div>
                    <p class="text-xs text-blue-700 mt-2">
                        Reîncarcă pagina asta oricând (link-ul e salvat, poți reveni la el) ca să vezi actualizări.
                    </p>
                </div>
            @endif

            <div class="space-y-3">
                @if($jobRequest->notified_craftsmen === 0)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-800">
                        <strong>Momentan nu există meseriași disponibili</strong> cu abonament activ în zona și categoria selectată.
                        Cererea ta a fost salvată — te vom contacta când un meseriaș devine disponibil.
                    </div>
                @else
                    <p class="text-sm text-gray-500">
                        Meseriașii te vor contacta la <strong>{{ $jobRequest->phone }}</strong> sau <strong>{{ $jobRequest->email }}</strong>.
                    </p>
                @endif

                <a href="{{ route('home') }}"
                    class="inline-block w-full text-white font-bold py-3 px-6 rounded-xl transition hover:opacity-90"
                    style="background-color: #2980B9;">
                    ← Înapoi la pagina principală
                </a>

                <a href="{{ route('public-request.create') }}"
                    class="inline-block w-full text-gray-700 font-medium py-3 px-6 rounded-xl border border-gray-300 hover:bg-gray-50 transition">
                    Trimite o altă cerere
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
