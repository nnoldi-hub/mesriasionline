@extends('layouts.app')

@section('title', 'Notificările mele')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Notificările mele</h1>
        <div class="flex space-x-3">
            @if(auth()->user()->unreadNotifications->count() > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-primary-600 hover:underline text-sm">
                        Marchează toate ca citite
                    </button>
                </form>
            @endif
            @if(auth()->user()->readNotifications->count() > 0)
                <form action="{{ route('notifications.destroy-read') }}" method="POST" onsubmit="return confirm('Sigur vrei să ștergi notificările citite?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline text-sm">
                        Șterge citite
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Push Notifications Card --}}
    @if(config('webpush.vapid.public_key'))
    <div class="bg-gradient-to-r from-primary-50 to-primary-100 rounded-xl shadow-sm p-6 mb-6 push-notification-controls">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-primary-600 rounded-full p-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Notificări Push în Browser</h3>
                    <p class="text-sm text-gray-600" id="push-status">Verificare status...</p>
                </div>
            </div>
            <div>
                <button type="button" id="push-subscribe-btn" class="d-none bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                    <i class="fas fa-bell me-1"></i> Activează
                </button>
                <button type="button" id="push-unsubscribe-btn" class="d-none bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-bell-slash me-1"></i> Dezactivează
                </button>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-3">
            Primești notificări instant în browser când ai mesaje noi, recenzii sau cereri de ofertă, chiar și când nu ești pe site.
        </p>
    </div>
    @endif

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if($notifications->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Nicio notificare</h3>
            <p class="text-gray-500">Vei primi notificări când ai mesaje noi, recenzii sau cereri de ofertă.</p>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <ul class="divide-y divide-gray-200">
                @foreach($notifications as $notification)
                    @php
                        $data = $notification->data;
                        $type = $data['type'] ?? 'general';
                        $isUnread = is_null($notification->read_at);
                        
                        $icon = match($type) {
                            'new_message' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>',
                            'new_quote_request' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>',
                            'quote_received' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                            'quote_accepted' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
                            'new_review' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>',
                            'new_appointment' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>',
                            default => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>',
                        };
                        
                        $bgColor = match($type) {
                            'new_message' => 'bg-blue-100 text-blue-600',
                            'new_quote_request' => 'bg-yellow-100 text-yellow-600',
                            'quote_received' => 'bg-green-100 text-green-600',
                            'quote_accepted' => 'bg-emerald-100 text-emerald-600',
                            'new_review' => 'bg-purple-100 text-purple-600',
                            'new_appointment' => 'bg-orange-100 text-orange-600',
                            default => 'bg-gray-100 text-gray-600',
                        };
                        
                        $title = match($type) {
                            'new_message' => 'Mesaj nou de la ' . ($data['sender_name'] ?? 'Cineva'),
                            'new_quote_request' => 'Cerere de ofertă: ' . ($data['title'] ?? ''),
                            'quote_received' => 'Ofertă primită de la ' . ($data['craftsman_name'] ?? 'Meseriaș'),
                            'quote_accepted' => '🎉 Oferta ta a fost acceptată!',
                            'new_review' => 'Recenzie nouă: ' . str_repeat('⭐', $data['rating'] ?? 5),
                            'new_appointment' => 'Programare nouă',
                            default => 'Notificare',
                        };
                    @endphp
                    
                    <li class="{{ $isUnread ? 'bg-primary-50' : '' }}">
                        <div class="p-4 flex items-start space-x-4">
                            <div class="flex-shrink-0 w-10 h-10 {{ $bgColor }} rounded-full flex items-center justify-center">
                                {!! $icon !!}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $title }}</p>
                                        @if(isset($data['preview']) || isset($data['comment']))
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ $data['preview'] ?? $data['comment'] ?? '' }}
                                            </p>
                                        @endif
                                        @if(isset($data['price']))
                                            <p class="text-sm text-green-600 font-medium mt-1">{{ $data['price'] }}</p>
                                        @endif
                                    </div>
                                    <span class="text-xs text-gray-500 whitespace-nowrap ml-2">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <div class="mt-2 flex items-center space-x-3">
                                    @if(isset($data['url']))
                                        <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-primary-600 hover:underline text-sm">
                                                {{ $isUnread ? 'Vezi și marchează ca citit' : 'Vezi' }}
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline text-sm">
                                            Șterge
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @if($isUnread)
                                <div class="flex-shrink-0">
                                    <span class="w-2 h-2 bg-primary-600 rounded-full block"></span>
                                </div>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
