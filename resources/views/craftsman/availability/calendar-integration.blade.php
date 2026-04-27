@extends('layouts.craftsman')

@section('title', 'Integrare Calendar')
@section('page-title', 'Integrare Calendar')

@section('content')
<div class="max-w-4xl">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-start gap-4 mb-6">
            <div class="p-3 bg-primary-100 rounded-lg">
                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">Sincronizare Calendar</h2>
                <p class="text-gray-600 mt-1">
                    Conectează-ți calendarul Google sau Outlook pentru a sincroniza automat programările. 
                    Astfel vei avea toate programările într-un singur loc.
                </p>
            </div>
        </div>

        <!-- Google Calendar -->
        <div class="border border-gray-200 rounded-lg p-6 mb-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 flex items-center justify-center">
                        <svg class="w-10 h-10" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Google Calendar</h3>
                        <p class="text-sm text-gray-600">
                            @if($googleConnected)
                                <span class="text-green-600 font-medium">✓ Conectat</span>
                            @else
                                Nu este conectat
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if($googleConnected)
                        <form action="{{ route('craftsman.calendar.google.sync') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-primary-600 hover:text-primary-700 border border-primary-600 rounded-lg hover:bg-primary-50">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Sincronizează
                            </button>
                        </form>
                        <form action="{{ route('craftsman.calendar.google.disconnect') }}" method="POST" class="inline" onsubmit="return confirm('Sigur vrei să deconectezi Google Calendar?')">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 border border-red-300 rounded-lg hover:bg-red-50">
                                Deconectează
                            </button>
                        </form>
                    @else
                        @if(config('services.google.client_id'))
                            <a href="{{ route('craftsman.calendar.google.connect') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                                Conectează
                            </a>
                        @else
                            <span class="text-sm text-gray-500">Nu este configurat</span>
                        @endif
                    @endif
                </div>
            </div>

            @if($googleConnected && count($googleCalendars) > 1)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <form action="{{ route('craftsman.calendar.google.update') }}" method="POST" class="flex items-center gap-4">
                        @csrf
                        @method('PUT')
                        <label for="calendar_id" class="text-sm font-medium text-gray-700">Calendar pentru programări:</label>
                        <select name="calendar_id" id="calendar_id" class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm">
                            @foreach($googleCalendars as $calendar)
                                <option value="{{ $calendar['id'] }}" {{ $craftsman->google_calendar_id === $calendar['id'] ? 'selected' : '' }}>
                                    {{ $calendar['name'] }} {{ $calendar['primary'] ? '(Principal)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700">
                            Salvează
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Outlook Calendar -->
        <div class="border border-gray-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 flex items-center justify-center">
                        <svg class="w-10 h-10" viewBox="0 0 24 24">
                            <path fill="#0078D4" d="M24 7.387v10.478c0 .23-.08.424-.238.576-.152.156-.35.234-.59.234h-8.906l-.403.403c-.16.16-.16.242 0 .403l.403.402v.704c0 .23-.079.424-.234.576-.16.156-.352.234-.586.234H7.37c-.234 0-.43-.078-.586-.234-.16-.152-.234-.347-.234-.576v-.703l.403-.403c.16-.16.16-.242 0-.403l-.403-.402H.89c-.242 0-.441-.078-.59-.234C.1 17.29.023 17.095.023 16.865V7.387c0-.234.078-.43.277-.59.152-.151.348-.23.59-.23h5.657V2.32c0-.234.078-.43.234-.586.156-.16.352-.234.586-.234h6.076c.234 0 .426.075.586.234.156.156.234.352.234.586v4.246h8.566c.24 0 .438.079.59.231.158.16.237.356.237.59h.004zM7.37 7.387v8.66h6.076v-8.66H7.37zm5.66-.82V2.727H7.78v3.84h5.25z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Outlook Calendar</h3>
                        <p class="text-sm text-gray-600">
                            @if($outlookConnected)
                                <span class="text-green-600 font-medium">✓ Conectat</span>
                            @else
                                Nu este conectat
                            @endif
                        </p>
                    </div>
                </div>
                <div>
                    @if($outlookConnected)
                        <form action="{{ route('craftsman.calendar.outlook.disconnect') }}" method="POST" class="inline" onsubmit="return confirm('Sigur vrei să deconectezi Outlook Calendar?')">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 border border-red-300 rounded-lg hover:bg-red-50">
                                Deconectează
                            </button>
                        </form>
                    @else
                        @if(config('services.microsoft.client_id'))
                            <a href="{{ route('craftsman.calendar.outlook.connect') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                                Conectează
                            </a>
                        @else
                            <span class="text-sm text-gray-500">Nu este configurat</span>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <h4 class="font-medium text-blue-900">Cum funcționează?</h4>
                <ul class="mt-2 text-sm text-blue-800 space-y-1">
                    <li>• Programările noi vor fi adăugate automat în calendarul tău</li>
                    <li>• Modificările programărilor se vor sincroniza automat</li>
                    <li>• Anulările vor fi reflectate în calendar</li>
                    <li>• Vei primi notificări direct în calendar cu 1 oră și 30 de minute înainte</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
