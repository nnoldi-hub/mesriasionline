@extends('layouts.dashboard')

@section('title', 'Materiale Video - Administrator')
@section('page-title', 'Materiale Video')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('header-actions')
<a href="{{ route('admin.videos.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm font-medium">
    + Adaugă video
</a>
@endsection

@section('content')

@if(session('success'))
    <div class="mb-4 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
        {{ session('success') }}
    </div>
@endif

<p class="text-gray-500 text-sm mb-6">Videoclipurile active apar în ordine pe pagina principală, într-o secțiune cu player și miniaturi.</p>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Video</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titlu</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ordine</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acțiuni</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($videos as $video)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <img src="{{ $video->thumbnail_url }}" alt="" class="w-24 h-14 object-cover rounded-lg bg-gray-100">
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $video->title }}</div>
                        @if($video->description)
                            <div class="text-sm text-gray-500 max-w-xs truncate">{{ $video->description }}</div>
                        @endif
                        <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $video->youtube_id }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <form action="{{ route('admin.videos.toggle-status', $video->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="px-2 py-0.5 text-xs rounded-full {{ $video->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $video->is_active ? 'Activ' : 'Inactiv' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-1">
                            <form action="{{ route('admin.videos.move-up', $video->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="p-1 text-gray-400 hover:text-gray-700" title="Mută mai sus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                </button>
                            </form>
                            <form action="{{ route('admin.videos.move-down', $video->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="p-1 text-gray-400 hover:text-gray-700" title="Mută mai jos">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.videos.edit', $video->id) }}" class="text-blue-600 hover:text-blue-800" title="Editează">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('admin.videos.destroy', $video->id) }}" method="POST"
                                  onsubmit="return confirm('Ștergi definitiv acest videoclip din listă?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Șterge">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        Nu ai adăugat încă niciun material video.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection
