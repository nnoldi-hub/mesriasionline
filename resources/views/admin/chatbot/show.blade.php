@extends('layouts.admin')

@section('title', 'Conversație Chatbot #' . $conversation->id)
@section('page-title', 'Detaliu Conversație')

@section('content')

{{-- Breadcrumb + acțiuni --}}
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.chatbot.index') }}" class="hover:text-primary-600 transition">Chatbot</a>
        <span>/</span>
        <span class="text-gray-800 font-medium">Conversație #{{ $conversation->id }}</span>
    </div>
    <form method="POST" action="{{ route('admin.chatbot.destroy', $conversation) }}"
          onsubmit="return confirm('Ștergi definitiv această conversație?')">
        @csrf @method('DELETE')
        <button type="submit"
            class="inline-flex items-center gap-2 text-sm text-red-600 hover:text-red-800 border border-red-200 hover:border-red-400 px-3 py-1.5 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Șterge
        </button>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Coloana stânga: info conversație --}}
    <div class="space-y-4">
        {{-- Meta date --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Informații conversație</h3>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Data start</dt>
                    <dd class="text-gray-800 font-medium">{{ $conversation->created_at->format('d.m.Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Ultima activitate</dt>
                    <dd class="text-gray-800">{{ $conversation->last_activity_at?->format('d.m.Y H:i') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Utilizator</dt>
                    <dd>
                        @if($conversation->user)
                            <span class="text-gray-800 font-medium">{{ $conversation->user->name }}</span>
                            <div class="text-xs text-gray-400">{{ $conversation->user->email }}</div>
                        @else
                            <span class="text-gray-500">Vizitator anonim</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">IP</dt>
                    <dd class="text-gray-600 font-mono text-xs">{{ $conversation->ip_address ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Pagina sursă</dt>
                    <dd class="text-gray-600 text-xs break-all">{{ $conversation->page_url ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Browser</dt>
                    <dd class="text-gray-500 text-xs truncate" title="{{ $conversation->user_agent }}">
                        {{ Str::limit($conversation->user_agent, 60) }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Stats conversație --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Statistici</h3>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-blue-50 rounded-lg p-3 text-center">
                    <p class="text-xl font-bold text-blue-700">{{ $conversation->user_messages }}</p>
                    <p class="text-xs text-blue-500 mt-0.5">Mesaje user</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3 text-center">
                    <p class="text-xl font-bold text-gray-700">{{ $conversation->message_count }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Total mesaje</p>
                </div>
            </div>

            <div class="mt-3 space-y-2">
                {{-- Intenție --}}
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">Intenție</span>
                    @php
                        $intentLabels = [
                            'craftsman_register' => ['label' => 'Înreg. meseriaș', 'class' => 'bg-blue-100 text-blue-700'],
                            'client_request'     => ['label' => 'Cerere client',   'class' => 'bg-green-100 text-green-700'],
                            'pricing'            => ['label' => 'Prețuri',          'class' => 'bg-yellow-100 text-yellow-700'],
                            'info'               => ['label' => 'Informații',       'class' => 'bg-gray-100 text-gray-600'],
                            'support'            => ['label' => 'Suport',           'class' => 'bg-red-100 text-red-700'],
                            'other'              => ['label' => 'Altele',           'class' => 'bg-indigo-100 text-indigo-600'],
                            'unknown'            => ['label' => 'Necunoscut',       'class' => 'bg-gray-100 text-gray-500'],
                        ];
                        $ic = $intentLabels[$conversation->intent] ?? $intentLabels['unknown'];
                    @endphp
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $ic['class'] }}">
                        {{ $ic['label'] }}
                    </span>
                </div>

                {{-- Conversie --}}
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">Conversie CTA</span>
                    @if($conversation->converted)
                        <div class="text-right">
                            <span class="text-green-600 text-xs font-semibold">✓ Convertit</span>
                            @if($conversation->conversion_url)
                                <div class="text-xs text-gray-400 mt-0.5">{{ $conversation->conversion_url }}</div>
                            @endif
                        </div>
                    @else
                        <span class="text-gray-400 text-xs">Nu</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Coloana dreapta: mesajele --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">Transcript conversație</h3>
        </div>

        <div class="flex-1 overflow-y-auto p-5 space-y-4" style="max-height: 600px;">
            @forelse($conversation->messages as $msg)
                <div class="{{ $msg->role === 'user' ? 'flex justify-end' : 'flex gap-3' }}">
                    @if($msg->role === 'assistant')
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                    @endif
                    <div class="max-w-[80%]">
                        <div class="{{ $msg->role === 'user'
                                ? 'bg-blue-600 text-white rounded-2xl rounded-tr-sm'
                                : 'bg-gray-100 text-gray-800 rounded-2xl rounded-tl-sm' }} px-4 py-2.5">
                            <p class="text-sm leading-relaxed whitespace-pre-wrap">{{ $msg->content }}</p>
                        </div>
                        @if($msg->actions)
                            <div class="mt-1.5 flex flex-wrap gap-1.5">
                                @foreach($msg->actions as $action)
                                    <span class="inline-block text-xs bg-white border border-gray-200 text-gray-600 px-2.5 py-1 rounded-lg">
                                        {{ $action['label'] ?? '' }} → {{ $action['url'] ?? '' }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                        <p class="text-xs text-gray-400 mt-1 {{ $msg->role === 'user' ? 'text-right' : '' }}">
                            {{ $msg->created_at->format('H:i:s') }}
                            @if($msg->role === 'user')
                                · User
                            @else
                                · Asistent
                            @endif
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-8">Niciun mesaj salvat.</p>
            @endforelse
        </div>
    </div>
</div>

@endsection
