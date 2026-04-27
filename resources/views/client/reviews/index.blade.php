@extends('layouts.client')

@section('title', 'Recenziile Mele')
@section('page-title', 'Recenziile Mele')

@section('content')
<div class="bg-white rounded-lg shadow">
    <!-- Header -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Recenzii pe care le-ai acordat</h2>
                <p class="text-sm text-gray-600">Istoricul recenziilor tale pentru meseriașii cu care ai lucrat.</p>
            </div>
        </div>
    </div>

    <!-- Lista recenzii -->
    <div class="p-6">
        @php
            $reviews = \App\Models\Review::where('client_id', auth()->id())
                ->with(['specialist', 'service'])
                ->orderBy('created_at', 'desc')
                ->get();
        @endphp

        @if($reviews->count() > 0)
        <div class="space-y-6">
            @foreach($reviews as $review)
            <div class="border border-gray-200 rounded-lg p-5">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-start space-x-4">
                        <!-- Avatar meșter -->
                        <a href="{{ route('craftsman.show', $review->specialist->slug ?? $review->specialist_id) }}" class="block">
                            @if($review->specialist->profile_image)
                            <img src="{{ Storage::url($review->specialist->profile_image) }}" 
                                alt="{{ $review->specialist->name }}"
                                class="w-12 h-12 rounded-full object-cover">
                            @else
                            <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center text-primary-600 font-bold">
                                {{ strtoupper(substr($review->specialist->name ?? 'M', 0, 2)) }}
                            </div>
                            @endif
                        </a>
                        <div>
                            <a href="{{ route('craftsman.show', $review->specialist->slug ?? $review->specialist_id) }}" 
                                class="font-medium text-gray-900 hover:text-primary-600">
                                {{ $review->specialist->name ?? 'Meșter' }}
                            </a>
                            <p class="text-sm text-gray-600">{{ $review->service->name ?? 'Serviciu general' }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $review->created_at->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <!-- Rating stars -->
                        @for($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        @endfor
                    </div>
                </div>

                <!-- Comentariul recenziei -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-700">{{ $review->comment }}</p>
                </div>

                <!-- Răspuns meșter (dacă există) -->
                @if($review->specialist_response)
                <div class="mt-4 ml-6 border-l-2 border-primary-200 pl-4">
                    <p class="text-sm font-medium text-primary-700 mb-1">Răspuns de la meșter:</p>
                    <p class="text-sm text-gray-600">{{ $review->specialist_response }}</p>
                    @if($review->responded_at)
                    <p class="text-xs text-gray-400 mt-1">{{ $review->responded_at->format('d M Y') }}</p>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Nu ai lăsat nicio recenzie încă</h3>
            <p class="text-gray-600 mb-6">După ce finalizezi o lucrare cu un meșter, îl poți evalua aici.</p>
            <a href="{{ route('client.appointments.index') }}" 
                class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Vezi Programările
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Info box -->
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <div class="flex items-start">
        <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <h4 class="text-sm font-medium text-blue-800">De ce sunt importante recenziile?</h4>
            <p class="text-sm text-blue-700 mt-1">
                Recenziile tale îi ajută pe alți clienți să găsească meșteri de încredere. 
                Feedback-ul onest contribuie la îmbunătățirea calității serviciilor din comunitate.
            </p>
        </div>
    </div>
</div>
@endsection
