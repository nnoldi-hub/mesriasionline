@extends('layouts.app')

@section('title', 'Conversație cu ' . $otherParticipant->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="bg-white rounded-t-xl shadow-sm p-4 border-b flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('messages.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                    <span class="text-primary-600 font-semibold">
                        {{ substr($otherParticipant->name, 0, 1) }}
                    </span>
                </div>
                <div>
                    <h1 class="font-semibold text-gray-900">
                        {{ $otherParticipant->name }}
                        @if($otherParticipant->is_verified)
                            <svg class="w-4 h-4 inline-block text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </h1>
                    @if($otherParticipant->role === 'specialist')
                        <p class="text-sm text-gray-500">{{ $otherParticipant->category?->name ?? 'Meseriaș' }}</p>
                    @endif
                </div>
            </div>
        </div>
        @if($otherParticipant->role === 'specialist')
            <a href="{{ route('craftsman.show', $otherParticipant->slug) }}" class="text-primary-600 hover:underline text-sm">
                Vezi profil
            </a>
        @endif
    </div>

    <!-- Messages -->
    <div class="bg-gray-50 p-4 h-96 overflow-y-auto" id="messages-container">
        @forelse($messages as $message)
            <div class="mb-4 flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-xs lg:max-w-md {{ $message->sender_id === auth()->id() ? 'bg-primary-600 text-white' : 'bg-white border' }} rounded-lg px-4 py-2 shadow-sm">
                    <p class="text-sm whitespace-pre-wrap">{{ $message->body }}</p>
                    @if($message->hasAttachment())
                        <div class="mt-2">
                            @if($message->isImageAttachment())
                                <img src="{{ $message->attachment_url }}" alt="Atașament" class="max-w-full rounded-lg">
                            @else
                                <a href="{{ $message->attachment_url }}" target="_blank" class="flex items-center space-x-2 text-sm {{ $message->sender_id === auth()->id() ? 'text-primary-100' : 'text-primary-600' }} hover:underline">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                    <span>Descarcă fișier</span>
                                </a>
                            @endif
                        </div>
                    @endif
                    <p class="text-xs {{ $message->sender_id === auth()->id() ? 'text-primary-200' : 'text-gray-400' }} mt-1">
                        {{ $message->created_at->format('d.m.Y H:i') }}
                        @if($message->sender_id === auth()->id() && $message->read_at)
                            <span class="ml-1">✓✓</span>
                        @endif
                    </p>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 py-8">
                <p>Niciun mesaj încă. Începe conversația!</p>
            </div>
        @endforelse
    </div>

    <!-- Reply Form -->
    <div class="bg-white rounded-b-xl shadow-sm p-4 border-t">
        <form action="{{ route('messages.reply', $conversation) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="flex items-end space-x-3">
                <div class="flex-1">
                    <textarea 
                        name="message" 
                        rows="2" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none"
                        placeholder="Scrie un mesaj..."
                        required
                    ></textarea>
                </div>
                <div class="flex items-center space-x-2">
                    <label class="cursor-pointer text-gray-500 hover:text-primary-600 transition p-2">
                        <input type="file" name="attachment" class="hidden" accept="image/*,.pdf,.doc,.docx">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                    </label>
                    <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </div>
            </div>
            @error('message')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Scroll to bottom of messages
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('messages-container');
        container.scrollTop = container.scrollHeight;
    });
</script>
@endpush
@endsection
