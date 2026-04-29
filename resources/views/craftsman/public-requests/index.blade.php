@extends('layouts.craftsman')

@section('title', 'Cereri Publice Clienți')
@section('page-title', 'Cereri de la Clienți')

@section('content')

@if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
@endif

<div class="mb-6 flex items-center justify-between">
    <p class="text-gray-500 text-sm">
        Clienți din zona ta care caută meseriași în categoria <strong>{{ auth()->user()->category?->name ?? 'ta' }}</strong>.
        Fii primul care răspunde!
    </p>
</div>

@if($jobRequests->isEmpty())
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <div class="text-5xl mb-4">📭</div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Nicio cerere disponibilă momentan</h3>
        <p class="text-gray-500 text-sm">Când clienții din zona ta postează cereri, ele apar aici.</p>
    </div>
@else
    <div class="space-y-4">
        @foreach($jobRequests as $req)
            @php $hasResponded = in_array($req->id, $respondedIds); @endphp
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition
                {{ $req->urgency === 'urgent' ? 'border-l-4 border-red-500' : ($req->urgency === 'this_week' ? 'border-l-4 border-yellow-400' : '') }}">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2 mb-1 flex-wrap gap-1">
                            {{-- Urgență badge --}}
                            @if($req->urgency === 'urgent')
                                <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-red-100 text-red-700">🔥 Urgent</span>
                            @elseif($req->urgency === 'this_week')
                                <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-yellow-100 text-yellow-700">⚡ Săptămâna aceasta</span>
                            @else
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600">Flexibil</span>
                            @endif
                            @if($hasResponded)
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">✓ Ai răspuns</span>
                            @endif
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $req->title }}</h3>
                        <p class="text-sm text-gray-500 mb-3">{{ Str::limit($req->description, 150) }}</p>
                        <div class="flex items-center space-x-4 text-xs text-gray-400">
                            <span>📍 {{ $req->location_display }}</span>
                            @if($req->budget_max)
                                <span>💰 max {{ number_format($req->budget_max, 0, ',', '.') }} lei</span>
                            @endif
                            @if($req->preferred_date)
                                <span>📅 {{ $req->preferred_date->format('d.m.Y') }}</span>
                            @endif
                            <span>🕐 {{ $req->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="ml-4 shrink-0">
                        <a href="{{ route('craftsman.public-requests.show', $req->id) }}"
                            class="inline-block {{ $hasResponded ? 'bg-gray-100 text-gray-600' : 'text-white' }} font-semibold px-5 py-2.5 rounded-xl text-sm transition hover:opacity-90"
                            style="{{ $hasResponded ? '' : 'background-color: #C0392B;' }}">
                            {{ $hasResponded ? 'Vezi detalii' : 'Trimite ofertă' }}
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $jobRequests->links() }}
    </div>
@endif
@endsection
