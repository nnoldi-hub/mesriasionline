@extends('layouts.onboarding')

@section('title', 'Pasul 3 — Poză profil')

@section('content')
<div class="text-center mb-6">
    <h2 class="text-xl font-bold text-gray-900">Adaugă o poză</h2>
    <p class="text-sm text-gray-500 mt-1">Profilul cu poză primește de 3x mai multe contacte</p>
</div>

<form method="POST" action="{{ route('onboarding.save', ['step' => 3]) }}" enctype="multipart/form-data" class="space-y-5">
    @csrf
    @method('PUT')

    {{-- Preview --}}
    <div class="flex flex-col items-center">
        <div class="relative w-28 h-28 rounded-full overflow-hidden bg-gray-100 border-2 border-dashed border-gray-300 mb-3 cursor-pointer"
             onclick="document.getElementById('profile_photo').click()">
            <img id="preview" src="{{ $user->profile_photo ? Storage::url($user->profile_photo) : '' }}"
                 alt=""
                 class="{{ $user->profile_photo ? 'block' : 'hidden' }} w-full h-full object-cover">
            <div id="placeholder" class="{{ $user->profile_photo ? 'hidden' : 'flex' }} w-full h-full flex-col items-center justify-center text-gray-400">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-xs mt-1">Adaugă poză</span>
            </div>
        </div>
        <button type="button" onclick="document.getElementById('profile_photo').click()"
            class="text-sm text-primary-600 hover:underline">
            Alege fotografie
        </button>
    </div>

    <input type="file" id="profile_photo" name="profile_photo" accept="image/*" class="hidden"
           onchange="previewPhoto(this)">

    @error('profile_photo')<p class="text-center text-xs text-red-600">{{ $message }}</p>@enderror

    <div class="text-xs text-gray-400 text-center">
        JPEG, PNG, WebP — max. 4 MB
    </div>

    <button type="submit"
        class="w-full py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors">
        Continuă →
    </button>

    <div class="text-center">
        <a href="{{ route('onboarding.step', ['step' => 4]) }}"
           class="text-sm text-gray-400 hover:text-gray-600 hover:underline">
            Sari peste acest pas →
        </a>
    </div>
</form>

<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.getElementById('preview');
            const ph  = document.getElementById('placeholder');
            img.src = e.target.result;
            img.classList.remove('hidden');
            ph.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
