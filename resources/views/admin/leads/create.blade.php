@extends('layouts.dashboard')

@section('title', 'Adaugă Lead Manual')
@section('page-title', 'Adaugă Meseriaș Manual')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('header-actions')
<a href="{{ route('admin.leads.index') }}"
   class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 text-sm transition">
    ← Înapoi la listă
</a>
@endsection

@section('content')

<div class="max-w-2xl">

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <p class="text-sm text-gray-500 mb-6">
            Folosește acest formular pentru prospecți identificați de tine (Facebook, recomandări, teren) —
            ca să-i urmărești în același loc cu lead-urile din formularul public.
        </p>

        <form method="POST" action="{{ route('admin.leads.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nume complet *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="Ex: Ion Popescu"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Telefon *</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                           placeholder="07xx xxx xxx"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Oraș / zonă *</label>
                    <input type="text" name="city" value="{{ old('city') }}" required
                           placeholder="Ex: Cluj-Napoca"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Meserie *</label>
                    <select name="trade" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
                        <option value="">— Selectează —</option>
                        <option value="electrician" {{ old('trade') === 'electrician' ? 'selected' : '' }}>Electrician</option>
                        <option value="instalator"  {{ old('trade') === 'instalator' ? 'selected' : '' }}>Instalator</option>
                        <option value="tamplar"     {{ old('trade') === 'tamplar' ? 'selected' : '' }}>Tâmplar</option>
                        <option value="zugrav"      {{ old('trade') === 'zugrav' ? 'selected' : '' }}>Zugrav</option>
                        <option value="mecanic"     {{ old('trade') === 'mecanic' ? 'selected' : '' }}>Mecanic</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Ani de experiență *</label>
                    <select name="experience_range" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
                        <option value="">— Selectează —</option>
                        <option value="0-2" {{ old('experience_range') === '0-2' ? 'selected' : '' }}>0-2 ani</option>
                        <option value="3-5" {{ old('experience_range') === '3-5' ? 'selected' : '' }}>3-5 ani</option>
                        <option value="5+"  {{ old('experience_range') === '5+' ? 'selected' : '' }}>5+ ani</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Email (opțional)</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="email@exemplu.ro"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
                    <p class="text-xs text-gray-400 mt-1">Fără email poți totuși trimite link de activare pe WhatsApp.</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Status inițial *</label>
                    <select name="status" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
                        <option value="nou"       {{ old('status', 'nou') === 'nou' ? 'selected' : '' }}>Nou</option>
                        <option value="contactat" {{ old('status') === 'contactat' ? 'selected' : '' }}>Contactat</option>
                        <option value="invitat"   {{ old('status') === 'invitat' ? 'selected' : '' }}>Invitat</option>
                        <option value="respins"   {{ old('status') === 'respins' ? 'selected' : '' }}>Respins</option>
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Note interne</label>
                    <textarea name="admin_notes" rows="3" placeholder="Ex: găsit pe grupul Facebook X, sunat pe 12.08, revin marți..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 resize-none">{{ old('admin_notes') }}</textarea>
                </div>
            </div>

            <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 rounded-lg text-sm transition">
                Salvează lead
            </button>
        </form>
    </div>

</div>

@endsection
