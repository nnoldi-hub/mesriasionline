@extends('layouts.onboarding')

@section('title', 'Pasul 1 — Date personale')

@section('content')
<div class="text-center mb-6">
    <h2 class="text-xl font-bold text-gray-900">Spune-ne cine ești</h2>
    <p class="text-sm text-gray-500 mt-1">Telefon, meserie și orașul tău</p>
</div>

<form method="POST" action="{{ route('onboarding.save', ['step' => 1]) }}" class="space-y-5">
    @csrf
    @method('PUT')

    <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
            Telefon <span class="text-red-500">*</span>
        </label>
        <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required autofocus
            placeholder="07xx xxx xxx"
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('phone') border-red-400 @enderror">
        @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
            Meseria principală <span class="text-red-500">*</span>
        </label>
        <select id="category_id" name="category_id" required
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('category_id') border-red-400 @enderror">
            <option value="">Alege meseria...</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id', $user->category_id) == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="location_id" class="block text-sm font-medium text-gray-700 mb-1">
            Orașul tău <span class="text-red-500">*</span>
        </label>
        <select id="location_id" name="location_id" required
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('location_id') border-red-400 @enderror">
            <option value="">Alege orașul...</option>
            @foreach($locations as $loc)
                <option value="{{ $loc->id }}" {{ old('location_id', $user->location_id) == $loc->id ? 'selected' : '' }}>
                    {{ $loc->name }}
                </option>
            @endforeach
        </select>
        @error('location_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <button type="submit"
        class="w-full py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors">
        Continuă →
    </button>
</form>
@endsection
