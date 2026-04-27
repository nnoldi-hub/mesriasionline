@extends('layouts.onboarding')

@section('title', 'Devino Meseriaș — Înregistrare Rapidă')

@section('content')
<div class="text-center mb-6">
    <div class="w-14 h-14 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-3">
        <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
    </div>
    <h1 class="text-2xl font-extrabold text-gray-900">Creează cont gratuit</h1>
    <p class="text-sm text-gray-500 mt-1">Durează mai puțin de 3 minute. Fără card necesar.</p>
</div>

<form method="POST" action="{{ route('onboarding.quick-register.submit') }}" class="space-y-5">
    @csrf

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
            Nume complet <span class="text-red-500">*</span>
        </label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
            placeholder="ex: Ion Popescu"
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-400 @enderror">
        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
            Email <span class="text-red-500">*</span>
        </label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required
            placeholder="adresa@email.ro"
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('email') border-red-400 @enderror">
        @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
            Parolă <span class="text-red-500">*</span>
        </label>
        <input type="password" id="password" name="password" required
            placeholder="Minim 8 caractere"
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('password') border-red-400 @enderror">
        @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
            Confirmă parola <span class="text-red-500">*</span>
        </label>
        <input type="password" id="password_confirmation" name="password_confirmation" required
            placeholder="Repetă parola"
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
    </div>

    <button type="submit"
        class="w-full py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors">
        Continuă →
    </button>
</form>

<p class="text-center mt-4 text-xs text-gray-500">
    Prin înregistrare accepți <a href="{{ route('terms') }}" class="underline">Termenii și Condițiile</a>.
</p>
@endsection
