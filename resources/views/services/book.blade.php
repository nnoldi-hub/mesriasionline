extends('layouts.app')

@section('title', 'Rezervă serviciu - ' . $service->name)

@section('content')
<div class="max-w-lg mx-auto bg-white rounded-lg shadow p-8 mt-8">
    <h1 class="text-2xl font-bold mb-6">Solicitare serviciu: {{ $service->name }}</h1>
    <div class="mb-4 p-3 bg-primary-50 border-l-4 border-primary-400 text-primary-900 rounded">
        Completează formularul și echipa Omul Potrivit va prelua solicitarea ta. Un administrator te va contacta pentru a te ajuta cu soluția potrivită. Nu se face rezervare directă la un meseriaș.
    </div>
    <form method="POST" action="{{ route('service.book.submit', ['service' => $service->id]) }}">
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
            <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
            <input type="date" name="appointment_date" class="w-full px-4 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Ora</label>
            <input type="time" name="appointment_time" class="w-full px-4 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Mesaj (opțional)</label>
            <textarea name="message" class="w-full px-4 py-2 border rounded-lg" rows="3"></textarea>
        </div>
        <button type="submit" class="w-full bg-primary-600 text-white font-bold py-3 rounded-lg hover:bg-primary-700 transition">Trimite cererea</button>
    </form>
</div>
@endsection
