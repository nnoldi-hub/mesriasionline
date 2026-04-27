@extends('layouts.craftsman')

@section('title', 'Adaugă Certificare')
@section('page-title', 'Adaugă Certificare')

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
    <form action="{{ route('craftsman.certifications.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titlu certificare *</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500" placeholder="Ex: Electrician autorizat ANRE">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="issuing_organization" class="block text-sm font-medium text-gray-700 mb-1">Organizația emitentă</label>
                <input type="text" name="issuing_organization" id="issuing_organization" value="{{ old('issuing_organization') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500" placeholder="Ex: ANRE, ISCIR, etc.">
            </div>
            
            <div>
                <label for="credential_id" class="block text-sm font-medium text-gray-700 mb-1">Număr/ID certificat</label>
                <input type="text" name="credential_id" id="credential_id" value="{{ old('credential_id') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500" placeholder="Ex: 12345/2024">
            </div>
            
            <div>
                <label for="issue_date" class="block text-sm font-medium text-gray-700 mb-1">Data emiterii</label>
                <input type="date" name="issue_date" id="issue_date" value="{{ old('issue_date') }}" max="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500">
            </div>
            
            <div>
                <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-1">Data expirării</label>
                <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500">
                <p class="text-xs text-gray-500 mt-1">Lasă gol dacă certificarea nu expiră</p>
            </div>
            
            <div class="md:col-span-2">
                <label for="credential_url" class="block text-sm font-medium text-gray-700 mb-1">Link verificare online</label>
                <input type="url" name="credential_url" id="credential_url" value="{{ old('credential_url') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500" placeholder="https://...">
            </div>
            
            <div class="md:col-span-2">
                <label for="document" class="block text-sm font-medium text-gray-700 mb-1">Document (PDF sau imagine)</label>
                <input type="file" name="document" id="document" accept=".pdf,.jpg,.jpeg,.png" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500">
                <p class="text-xs text-gray-500 mt-1">Formate acceptate: PDF, JPG, PNG. Max. 5MB</p>
                @error('document')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="mt-6 flex space-x-3">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition">
                Salvează certificarea
            </button>
            <a href="{{ route('craftsman.certifications.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                Anulează
            </a>
        </div>
    </form>
</div>
@endsection
