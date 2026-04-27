@extends('layouts.dashboard')

@section('title', 'Recenzii - Administrator')
@section('page-title', 'Gestionare Recenzii')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('header-actions')
<form action="{{ route('admin.reviews') }}" method="GET" class="flex items-center space-x-2">
    <select name="is_approved" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
        <option value="">Toate recenziile</option>
        <option value="1" {{ request('is_approved') === '1' ? 'selected' : '' }}>Aprobate</option>
        <option value="0" {{ request('is_approved') === '0' ? 'selected' : '' }}>În așteptare</option>
    </select>
    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
        Filtrează
    </button>
</form>
@endsection

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
                        <p class="text-sm text-gray-600">Pentru: <a href="{{ route('craftsman.show', $review->specialist->slug) }}" target="_blank" class="text-primary-600 hover:underline">{{ $review->specialist->name }}</a></p>
                        <p class="text-sm text-gray-500">{{ $review->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-accent-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="px-3 py-1 text-xs font-medium rounded-full {{ $review->is_approved ? 'bg-success-100 text-success-700' : 'bg-accent-100 text-accent-800' }}">
                        {{ $review->is_approved ? 'Aprobată' : 'În așteptare' }}
                    </span>
                </div>
            </div>

            <p class="text-gray-700 mb-4">{{ $review->comment }}</p>

            @if($review->specialist_response)
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <p class="text-sm font-semibold text-gray-900 mb-1">Răspuns de la meseriaș:</p>
                    <p class="text-sm text-gray-700">{{ $review->specialist_response }}</p>
                </div>
            @endif

            @if(!$review->is_approved)
                <div class="flex space-x-2">
                    <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-success-400 text-white rounded-lg hover:bg-green-700">
                            Aprobă Recenzia
                        </button>
                    </form>
                </div>
            @endif
        </div>
    @empty
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nu există recenzii</h3>
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $reviews->links() }}
</div>
@endsection
