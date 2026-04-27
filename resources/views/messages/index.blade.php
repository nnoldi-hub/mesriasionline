@php 
    $userRole = auth()->user()->role ?? 'guest';
    $layout = match($userRole) {
        'client' => 'layouts.client',
        'specialist' => 'layouts.craftsman',
        default => 'layouts.app'
    };
@endphp
@extends($layout)

@section('title', 'Mesajele mele')
@section('page-title', 'Mesajele mele')

@section('content')
@php $usesDashboardLayout = in_array($userRole, ['client', 'specialist']); @endphp
<div class="{{ $usesDashboardLayout ? '' : 'max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8' }}">
    <div class="flex justify-between items-center mb-6">
        @if(!$usesDashboardLayout)
        <h1 class="text-2xl font-bold text-gray-900">Mesajele mele</h1>
        @endif
        <a href="{{ route('messages.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition {{ $usesDashboardLayout ? 'ml-auto' : '' }}">
            <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Mesaj nou
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if($conversations->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Nicio conversație încă</h3>
            <p class="text-gray-500 mb-4">Începe o conversație cu un meseriaș pentru a discuta despre proiectul tău.</p>
            <a href="{{ route('home') }}" class="text-primary-600 hover:underline">Găsește un meseriaș</a>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <ul class="divide-y divide-gray-200">
                @foreach($conversations as $conversation)
                    <li>
                        <a href="{{ route('messages.show', $conversation) }}" class="block hover:bg-gray-50 transition p-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                                        <span class="text-primary-600 font-semibold text-lg">
                                            {{ substr($conversation->other_participant->name, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $conversation->other_participant->name }}
                                            @if($conversation->other_participant->is_verified)
                                                <svg class="w-4 h-4 inline-block text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                            @endif
                                        </p>
                                        <span class="text-xs text-gray-500">
                                            {{ $conversation->last_message_at?->diffForHumans() }}
                                        </span>
                                    </div>
                                    @if($conversation->latestMessage)
                                        <p class="text-sm text-gray-500 truncate">
                                            @if($conversation->latestMessage->sender_id === auth()->id())
                                                <span class="text-gray-400">Tu:</span>
                                            @endif
                                            {{ Str::limit($conversation->latestMessage->body, 50) }}
                                        </p>
                                    @endif
                                </div>
                                @if($conversation->unread_count > 0)
                                    <div class="flex-shrink-0">
                                        <span class="bg-primary-600 text-white text-xs font-medium px-2.5 py-0.5 rounded-full">
                                            {{ $conversation->unread_count }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="mt-6">
            {{ $conversations->links() }}
        </div>
    @endif
</div>
@endsection
