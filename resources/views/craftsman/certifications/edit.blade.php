@extends('layouts.craftsman')

@section('title', 'Editează Certificare')
@section('page-title', 'Editează Certificare')

@section('content')
<div class="mb-6">
    <a href="{{ route('craftsman.certifications.index') }}" class="text-primary-600 hover:underline flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Înapoi la certificări
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm p-6">
    <form action="{{ route('craftsman.certifications.update', $certification) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titlu certificare *</label>
                <input type="text" name="title" id="title" value="{{ old('title', $certification->title) }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500" placeholder="Ex: Electrician autorizat ANRE">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="issuing_organization" class="block text-sm font-medium text-gray-700 mb-1">Organizația emitentă</label>
                <input type="text" name="issuing_organization" id="issuing_organization" value="{{ old('issuing_organization', $certification->issuing_organization) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500" placeholder="Ex: ANRE, ISCIR, etc.">
            </div>
            
            <div>
                <label for="credential_id" class="block text-sm font-medium text-gray-700 mb-1">Număr/ID certificat</label>
                <input type="text" name="credential_id" id="credential_id" value="{{ old('credential_id', $certification->credential_id) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500" placeholder="Ex: 12345/2024">
            </div>
            
            <div>
                <label for="issue_date" class="block text-sm font-medium text-gray-700 mb-1">Data emiterii</label>
                <input type="date" name="issue_date" id="issue_date" value="{{ old('issue_date', $certification->issue_date?->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500">
            </div>
            
            <div>
                <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-1">Data expirării</label>
                <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date', $certification->expiry_date?->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500">
                <p class="text-xs text-gray-500 mt-1">Lasă gol dacă certificarea nu expiră</p>
            </div>
            
            <div class="md:col-span-2">
                <label for="credential_url" class="block text-sm font-medium text-gray-700 mb-1">Link verificare online</label>
                <input type="url" name="credential_url" id="credential_url" value="{{ old('credential_url', $certification->credential_url) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500" placeholder="https://...">
            </div>
            
            @if($certification->document_path)
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Document curent</label>
                <div class="flex items-center space-x-4">
                    <a href="{{ asset('storage/' . $certification->document_path) }}" target="_blank" class="text-primary-600 hover:underline flex items-center">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Vezi documentul actual
                    </a>
                </div>
            </div>
            @endif
            
            <div class="md:col-span-2">
                <label for="document" class="block text-sm font-medium text-gray-700 mb-1">{{ $certification->document_path ? 'Înlocuiește document' : 'Document (PDF sau imagine)' }}</label>
                <input type="file" name="document" id="document" accept=".pdf,.jpg,.jpeg,.png" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500">
                <p class="text-xs text-gray-500 mt-1">Formate acceptate: PDF, JPG, PNG. Max. 5MB</p>
                @error('document')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="mt-6 flex space-x-3">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition">
                Actualizează certificarea
            </button>
            <a href="{{ route('craftsman.certifications.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                Anulează
            </a>
        </div>
    </form>
</div>

@if($certification->is_verified)
<div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
    <div class="flex items-center">
        <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
        </svg>
        <span class="text-green-700 font-medium">Această certificare a fost verificată de echipa noastră.</span>
    </div>
</div>
@else
<div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
    <div class="flex items-center">
        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="text-yellow-700">Certificarea este în curs de verificare. Te vom notifica când procesul este complet.</span>
    </div>
</div>
@endif
@endsection
