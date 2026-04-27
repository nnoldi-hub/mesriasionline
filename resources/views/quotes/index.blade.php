@php 
    $userRole = auth()->user()->role ?? 'guest';
    $layout = match($userRole) {
        'client' => 'layouts.client',
        'specialist' => 'layouts.craftsman',
        default => 'layouts.app'
    };
@endphp
@extends($layout)

@section('title', 'Cererile mele de ofertă')
@section('page-title', 'Cererile mele de ofertă')

@section('content')
@php $usesDashboardLayout = in_array($userRole, ['client', 'specialist']); @endphp
<div class="{{ $usesDashboardLayout ? '' : 'max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8' }}">
    <div class="flex justify-between items-center mb-6">
        @if(!$usesDashboardLayout)
        <h1 class="text-2xl font-bold text-gray-900">Cererile mele de ofertă</h1>
        @endif
        <a href="{{ route('quotes.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition {{ $usesDashboardLayout ? 'ml-auto' : '' }}">
            <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Cerere nouă
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    @if($quoteRequests->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Nicio cerere de ofertă</h3>
            <p class="text-gray-500 mb-4">Cere o ofertă de la un meseriaș pentru proiectul tău.</p>
            <a href="{{ route('home') }}" class="text-primary-600 hover:underline">Găsește un meseriaș</a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($quoteRequests as $request)
                <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $request->title }}</h3>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ $request->status_color }}-100 text-{{ $request->status_color }}-700">
                                    {{ $request->status_label }}
                                </span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ $request->urgency_color }}-100 text-{{ $request->urgency_color }}-700">
                                    {{ $request->urgency_label }}
                                </span>
                            </div>
                            
                            <p class="text-gray-600 text-sm mb-3">{{ Str::limit($request->description, 150) }}</p>
                            
                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ $request->craftsman->name }}
                                </span>
                                @if($request->preferred_date)
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $request->preferred_date->format('d.m.Y') }}
                                    </span>
                                @endif
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $request->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="ml-4 text-right">
                            @if($request->quotes->count() > 0)
                                <span class="text-sm font-medium text-green-600">
                                    {{ $request->quotes->count() }} {{ $request->quotes->count() == 1 ? 'ofertă' : 'oferte' }}
                                </span>
                            @endif
                            <div class="mt-2">
                                <a href="{{ route('quotes.show', $request) }}" class="text-primary-600 hover:underline text-sm">
                                    Vezi detalii →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $quoteRequests->links() }}
        </div>
    @endif
</div>
@endsection
