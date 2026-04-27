@extends('layouts.craftsman')

@section('title', 'Certificări & Diplome')
@section('page-title', 'Certificări & Diplome')

@section('content')
@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
@endif

<div class="mb-6 flex justify-between items-center">
    <p class="text-gray-600">Adaugă certificările și diplomele tale pentru a crește încrederea clienților.</p>
    <a href="{{ route('craftsman.certifications.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Adaugă certificare
    </a>
</div>

@if($certifications->isEmpty())
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Nicio certificare adăugată</h3>
        <p class="text-gray-500 mb-4">Adaugă certificările și diplomele tale pentru a crește încrederea clienților.</p>
        <a href="{{ route('craftsman.certifications.create') }}" class="inline-flex items-center text-primary-600 hover:underline">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Adaugă prima certificare
        </a>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($certifications as $cert)
            <div class="bg-white rounded-xl shadow-sm p-6 relative">
                @if($cert->is_verified)
                    <div class="absolute top-4 right-4">
                        <span class="bg-green-100 text-green-700 text-xs font-medium px-2 py-1 rounded-full flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Verificată
                        </span>
                    </div>
                @endif
                
                <div class="flex items-start mb-4">
                    <div class="p-3 bg-primary-100 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">{{ $cert->title }}</h3>
                        @if($cert->issuing_organization)
                            <p class="text-sm text-gray-500">{{ $cert->issuing_organization }}</p>
                        @endif
                    </div>
                </div>
                
                <div class="text-sm text-gray-600 space-y-1 mb-4">
                    @if($cert->issue_date)
                        <p>Emis: {{ $cert->issue_date->format('d.m.Y') }}</p>
                    @endif
                    @if($cert->expiry_date)
                        <p class="{{ $cert->isExpired() ? 'text-red-600' : ($cert->isExpiringSoon() ? 'text-yellow-600' : '') }}">
                            Expiră: {{ $cert->expiry_date->format('d.m.Y') }}
                            @if($cert->isExpired())
                                <span class="bg-red-100 text-red-700 text-xs px-1 rounded">Expirată</span>
                            @elseif($cert->isExpiringSoon())
                                <span class="bg-yellow-100 text-yellow-700 text-xs px-1 rounded">Expiră curând</span>
                            @endif
                        </p>
                    @endif
                    @if($cert->credential_id)
                        <p>ID: {{ $cert->credential_id }}</p>
                    @endif
                </div>
                
                <div class="flex items-center justify-between pt-4 border-t">
                    <div class="flex space-x-2">
                        @if($cert->document_path)
                            <a href="{{ asset('storage/' . $cert->document_path) }}" target="_blank" class="text-primary-600 hover:underline text-sm">
                                Vezi document
                            </a>
                        @endif
                        @if($cert->credential_url)
                            <a href="{{ $cert->credential_url }}" target="_blank" class="text-primary-600 hover:underline text-sm">
                                Verifică online
                            </a>
                        @endif
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('craftsman.certifications.edit', $cert) }}" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        <form action="{{ route('craftsman.certifications.destroy', $cert) }}" method="POST" class="inline" onsubmit="return confirm('Sigur vrei să ștergi această certificare?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
