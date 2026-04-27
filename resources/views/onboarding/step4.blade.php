@extends('layouts.onboarding')

@section('title', 'Pasul 4 — Disponibilitate')

@section('content')
<div class="text-center mb-6">
    <h2 class="text-xl font-bold text-gray-900">Când ești disponibil?</h2>
    <p class="text-sm text-gray-500 mt-1">Programul implicit — modificabil oricând din dashboard</p>
</div>

<form method="POST" action="{{ route('onboarding.save', ['step' => 4]) }}" class="space-y-6">
    @csrf
    @method('PUT')

    {{-- Zile de lucru --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Zile de lucru <span class="text-red-500">*</span>
        </label>
        @php
            $days = [
                'Mon' => 'Luni',
                'Tue' => 'Marți',
                'Wed' => 'Miercuri',
                'Thu' => 'Joi',
                'Fri' => 'Vineri',
                'Sat' => 'Sâmbătă',
                'Sun' => 'Duminică',
            ];
            $defaultDays = old('work_days', ['Mon','Tue','Wed','Thu','Fri']);
        @endphp
        <div class="grid grid-cols-7 gap-1">
            @foreach($days as $key => $label)
                <label class="flex flex-col items-center cursor-pointer">
                    <input type="checkbox" name="work_days[]" value="{{ $key }}"
                        {{ in_array($key, $defaultDays) ? 'checked' : '' }}
                        class="sr-only peer">
                    <span class="w-9 h-9 flex items-center justify-center rounded-full text-xs font-medium border
                        border-gray-200 peer-checked:bg-primary-600 peer-checked:text-white peer-checked:border-primary-600
                        hover:border-primary-400 transition-colors">
                        {{ substr($label, 0, 2) }}
                    </span>
                    <span class="text-xs text-gray-400 mt-1 hidden sm:block">{{ substr($label, 0, 1) }}</span>
                </label>
            @endforeach
        </div>
        @error('work_days')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Ore --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="work_start" class="block text-sm font-medium text-gray-700 mb-1">
                Ora start <span class="text-red-500">*</span>
            </label>
            <input type="time" id="work_start" name="work_start" value="{{ old('work_start', '08:00') }}" required
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('work_start') border-red-400 @enderror">
            @error('work_start')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="work_end" class="block text-sm font-medium text-gray-700 mb-1">
                Ora final <span class="text-red-500">*</span>
            </label>
            <input type="time" id="work_end" name="work_end" value="{{ old('work_end', '18:00') }}" required
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('work_end') border-red-400 @enderror">
            @error('work_end')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    {{-- Weekend --}}
    <label class="flex items-center space-x-3 cursor-pointer">
        <input type="checkbox" name="available_weekends" value="1"
            {{ old('available_weekends') ? 'checked' : '' }}
            class="w-5 h-5 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
        <span class="text-sm text-gray-700">Disponibil și în weekend</span>
    </label>

    <button type="submit"
        class="w-full py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors">
        🎉 Finalizează profilul
    </button>
</form>
@endsection
