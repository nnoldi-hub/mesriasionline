@extends('layouts.app')

@section('title', 'Mesaj nou')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('messages.index') }}" class="text-primary-600 hover:underline flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Înapoi la mesaje
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Trimite un mesaj</h1>

        @if($craftsman)
            <div class="bg-gray-50 rounded-lg p-4 mb-6 flex items-center space-x-4">
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                    <span class="text-primary-600 font-semibold text-lg">
                        {{ substr($craftsman->name, 0, 1) }}
                    </span>
                </div>
                <div>
                    <p class="font-medium text-gray-900">{{ $craftsman->name }}</p>
                    <p class="text-sm text-gray-500">{{ $craftsman->category?->name ?? 'Meseriaș' }}</p>
                </div>
            </div>
        @endif

        <form action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            @if($craftsman)
                <input type="hidden" name="craftsman_id" value="{{ $craftsman->id }}">
            @else
                <div class="mb-4">
                    <label for="craftsman_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Selectează meseriaș *
                    </label>
                    <select name="craftsman_id" id="craftsman_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">-- Alege un meseriaș --</option>
                        @php
                            $craftsmen = \App\Models\User::where('role', 'specialist')->where('is_active', true)->get();
                        @endphp
                        @foreach($craftsmen as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} - {{ $c->category?->name ?? 'Meseriaș' }}</option>
                        @endforeach
                    </select>
                    @error('craftsman_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div class="mb-4">
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">
                    Subiect (opțional)
                </label>
                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="Ex: Întrebare despre servicii">
                @error('subject')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
                    Mesaj *
                </label>
                <textarea name="message" id="message" rows="5" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="Scrie mesajul tău aici...">{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="attachment" class="block text-sm font-medium text-gray-700 mb-1">
                    Atașament (opțional)
                </label>
                <input type="file" name="attachment" id="attachment" accept="image/*,.pdf,.doc,.docx" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Formate acceptate: JPG, PNG, GIF, PDF, DOC, DOCX. Max 5MB.</p>
                @error('attachment')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-primary-600 text-white py-3 rounded-lg font-semibold hover:bg-primary-700 transition">
                Trimite mesajul
            </button>
        </form>
    </div>
</div>
@endsection
