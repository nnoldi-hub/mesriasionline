@extends('layouts.admin')
@section('title', 'Editează intrare cunoștințe')
@section('page-title', 'Editează intrare cunoștințe')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('admin.chatbot.knowledge.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Înapoi la lista
    </a>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.chatbot.knowledge.update', $knowledge) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Întrebare exemplu <span class="text-red-500">*</span></label>
                <input type="text" name="question_example" value="{{ old('question_example', $knowledge->question_example) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 @error('question_example') border-red-400 @enderror">
                @error('question_example')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keywords (trigger) <span class="text-red-500">*</span></label>
                <input type="text" name="keywords" value="{{ old('keywords', $knowledge->keywords) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 @error('keywords') border-red-400 @enderror">
                @error('keywords')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                <p class="text-xs text-gray-400 mt-1">Separate prin virgulă.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Răspuns <span class="text-red-500">*</span></label>
                <textarea name="answer" rows="4"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 @error('answer') border-red-400 @enderror">{{ old('answer', $knowledge->answer) }}</textarea>
                @error('answer')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Eticheta buton CTA</label>
                    <input type="text" name="cta_label" value="{{ old('cta_label', $knowledge->cta_label) }}"
                           placeholder="👷 Înscrie-te ca meseriaș"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL buton CTA</label>
                    <input type="text" name="cta_url" value="{{ old('cta_url', $knowledge->cta_url) }}"
                           placeholder="/register?type=craftsman"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prioritate</label>
                    <input type="number" name="priority" value="{{ old('priority', $knowledge->priority) }}" min="0" max="100"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div class="flex items-end pb-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ $knowledge->is_active ? 'checked' : '' }} class="w-4 h-4 text-red-600 rounded">
                        <span class="text-sm text-gray-700">Activat</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-6 py-2 rounded-lg transition">
                    Salvează modificările
                </button>
                <a href="{{ route('admin.chatbot.knowledge.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-6 py-2 rounded-lg transition">
                    Anulează
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
