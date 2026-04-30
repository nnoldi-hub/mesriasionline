@extends('layouts.admin')
@section('title', 'Bază de cunoștințe Chatbot')
@section('page-title', 'Bază de cunoștințe Chatbot')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <p class="text-gray-500 text-sm">Răspunsuri directe și linkuri corecte — chatbot-ul le folosește când detectează keyworduri</p>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.chatbot.knowledge.test') }}"
           class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg transition">
            🧪 Testează
        </a>
        <a href="{{ route('admin.chatbot.knowledge.create') }}"
           class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Adaugă intrare
        </a>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4 text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if($entries->isEmpty())
        <div class="p-12 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
            <p class="font-medium">Nicio intrare încă</p>
            <p class="text-xs mt-1">Adaugă răspunsuri predefinite pentru a antrena chatbot-ul</p>
        </div>
    @else
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Întrebare exemplu</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Keywords</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Buton CTA</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Prioritate</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($entries as $entry)
                <tr class="hover:bg-gray-50 transition {{ !$entry->is_active ? 'opacity-50' : '' }}">
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-900 truncate max-w-[200px]">{{ $entry->question_example }}</p>
                        <p class="text-xs text-gray-400 mt-0.5 truncate max-w-[200px]">{{ Str::limit($entry->answer, 60) }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-1 max-w-[180px]">
                            @foreach(array_slice(explode(',', $entry->keywords), 0, 3) as $kw)
                                <span class="inline-block bg-blue-50 text-blue-700 text-xs px-2 py-0.5 rounded-full">{{ trim($kw) }}</span>
                            @endforeach
                            @if(count(explode(',', $entry->keywords)) > 3)
                                <span class="text-xs text-gray-400">+{{ count(explode(',', $entry->keywords)) - 3 }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        @if($entry->cta_label)
                            <div>
                                <span class="inline-block bg-red-50 text-red-700 text-xs px-2 py-0.5 rounded font-medium">{{ $entry->cta_label }}</span>
                                <p class="text-xs text-gray-400 mt-0.5 truncate max-w-[140px]">{{ $entry->cta_url }}</p>
                            </div>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-block bg-gray-100 text-gray-600 text-xs font-bold px-2 py-0.5 rounded">{{ $entry->priority }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('admin.chatbot.knowledge.toggle', $entry) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full transition {{ $entry->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $entry->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $entry->is_active ? 'Activ' : 'Inactiv' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2 justify-end">
                            <a href="{{ route('admin.chatbot.knowledge.edit', $entry) }}"
                               class="text-xs text-blue-600 hover:text-blue-800 font-medium">Editează</a>
                            <form method="POST" action="{{ route('admin.chatbot.knowledge.destroy', $entry) }}"
                                  onsubmit="return confirm('Ștergi această intrare?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Șterge</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $entries->links() }}
        </div>
    @endif
</div>

{{-- Hint box --}}
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800">
    <p class="font-semibold mb-1">💡 Cum funcționează?</p>
    <p>Chatbot-ul verifică mai întâi baza de cunoștințe. Dacă mesajul utilizatorului conține unul din <strong>keywords</strong>, răspunde direct cu textul tău — fără AI. Folosit pentru linkuri corecte, prețuri, informații specifice platformei.</p>
    <p class="mt-1">Keywords separate prin virgulă: <code class="bg-blue-100 px-1 rounded">inscrie,meserias,devino meserias,vreau cont</code></p>
</div>

@endsection
