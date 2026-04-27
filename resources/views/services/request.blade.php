@extends('layouts.app')

@section('title', 'Solicitare mentenanță / întreținere imobil - Fixacasa')
@section('description', 'Trimite o solicitare pentru servicii de mentenanță sau întreținere imobil. Fixacasa te contactează cu o ofertă personalizată.')

@section('content')
<div class="max-w-lg mx-auto bg-white rounded-lg shadow p-8 mt-8">
    <h1 class="text-2xl font-bold mb-6">Solicitare mentenanță / întreținere imobil</h1>
    <div class="mb-4 p-3 bg-primary-50 border-l-4 border-primary-400 text-primary-900 rounded">
        Completează formularul de mai jos și echipa Fixacasa te va contacta cu o ofertă personalizată pentru imobilul tău.
    </div>
    <form method="POST" action="{{ route('service.request.submit') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nume</label>
            <input type="text" name="client_name" class="w-full px-4 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
            <input type="text" name="client_phone" class="w-full px-4 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Email (opțional)</label>
            <input type="email" name="client_email" class="w-full px-4 py-2 border rounded-lg">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tip serviciu</label>
            <select name="service_type" class="w-full px-4 py-2 border rounded-lg" required>
                <option value="intretinere">Întreținere imobil</option>
                <option value="mentenanta">Mentenanță tehnică</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Locație imobil</label>
            <input type="text" name="location" class="w-full px-4 py-2 border rounded-lg" required placeholder="Adresa sau zona">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Descriere solicitare / dependențe</label>
            <textarea name="message" class="w-full px-4 py-2 border rounded-lg" rows="4" required placeholder="Descrie ce ai nevoie, tipul imobilului, dependențe, urgență etc."></textarea>
        </div>
        <button type="submit" class="w-full bg-primary-600 text-white font-bold py-3 rounded-lg hover:bg-primary-700 transition">Trimite solicitarea</button>
    </form>
</div>
@endsection
