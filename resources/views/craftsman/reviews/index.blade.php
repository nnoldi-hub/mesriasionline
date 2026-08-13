@extends('layouts.craftsman')

@section('title', 'Recenziile Mele')
@section('page-title', 'Recenziile Mele')

@section('content')
<div class="space-y-6">
    @forelse($reviews as $review)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-secondary-200 rounded-full flex items-center justify-center text-primary-600 font-bold">
                        {{ strtoupper(substr($review->client_name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $review->client_name }}</h3>
                        <p class="text-sm text-gray-600">{{ $review->appointment?->service?->name ?? $review->quoteRequest?->title ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500">{{ $review->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                </div>
                <div class="flex items-center">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-accent-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
            </div>

            <p class="text-gray-700 mb-4">{{ $review->comment }}</p>

            @if($review->specialist_response)
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm font-semibold text-gray-900 mb-1">Răspunsul tău:</p>
                    <p class="text-sm text-gray-700">{{ $review->specialist_response }}</p>
                </div>
            @endif
        </div>
    @empty
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nu ai recenzii încă</h3>
            <p class="mt-1 text-sm text-gray-500">Recenziile vor apărea aici după ce clienții lasă feedback.</p>
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $reviews->links() }}
</div>
@endsection
