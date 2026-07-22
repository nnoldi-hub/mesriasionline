@php
    $video = $video ?? null;
@endphp

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Titlu</label>
    <input type="text" name="title" value="{{ old('title', $video->title ?? '') }}" required
        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('title') border-red-400 @enderror">
    @error('title')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Link YouTube</label>
    <input type="text" name="youtube_url" value="{{ old('youtube_url', $video?->youtube_id) }}" required
        placeholder="https://www.youtube.com/watch?v=..."
        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('youtube_url') border-red-400 @enderror">
    <p class="text-xs text-gray-400 mt-1">Acceptă linkuri youtube.com/watch, youtu.be sau youtube.com/shorts.</p>
    @error('youtube_url')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Descriere (opțional)</label>
    <textarea name="description" rows="3"
        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('description') border-red-400 @enderror">{{ old('description', $video->description ?? '') }}</textarea>
    @error('description')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>

<label class="flex items-center gap-2">
    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $video->is_active ?? true) ? 'checked' : '' }}
        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
    <span class="text-sm text-gray-700">Activ (vizibil pe pagina principală)</span>
</label>
