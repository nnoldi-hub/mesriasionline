@extends('layouts.dashboard')

@section('title', 'Setări Profil - Administrator')
@section('page-title', 'Setări Profil')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="max-w-xl mx-auto mt-8">
    <div class="bg-white rounded-xl shadow p-8">
        <h2 class="text-2xl font-bold mb-6">Editeaza profilul adminului</h2>
        @if(session('success'))
            <div class="mb-4 text-green-600">{{ session('success') }}</div>
        @endif
        <form method="POST" action="{{ route('admin.profile.update') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Adresa de email</label>
                <input type="email" name="email" value="{{ old('email', $admin->email) }}" class="w-full border rounded px-3 py-2" required>
                @error('email')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Parola noua</label>
                <input type="password" name="password" class="w-full border rounded px-3 py-2">
                @error('password')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Confirma parola noua</label>
                <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2">
            </div>
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded hover:bg-primary-700">Salveaza</button>
        </form>
    </div>
</div>
@endsection
