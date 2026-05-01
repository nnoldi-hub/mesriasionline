<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Activează-ți contul — meseriasionline.ro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex flex-col items-center justify-center px-4 py-10">

<div class="bg-white rounded-2xl shadow-xl p-8 md:p-10 max-w-lg w-full">

    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900">Bun venit, {{ $lead->name }}!</h1>
        <p class="text-gray-500 mt-2 text-sm">
            Finalizează crearea contului tău de <strong>{{ $lead->tradeLabel }}</strong> pe meseriasionline.ro
        </p>
    </div>

    {{-- Date pre-completate (read-only) --}}
    <div class="bg-gray-50 rounded-xl p-4 mb-6 space-y-2 text-sm">
        <div class="flex justify-between">
            <span class="text-gray-500">Meserie</span>
            <span class="font-medium text-gray-800">{{ $lead->tradeLabel }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Oraș</span>
            <span class="font-medium text-gray-800">{{ $lead->city }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Telefon</span>
            <span class="font-medium text-gray-800">{{ $lead->phone }}</span>
        </div>
    </div>

    {{-- Erori --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-6">
            <ul class="list-disc list-inside space-y-1 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('recruitment.activate.store', ['token' => $token]) }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Adresă de email *</label>
            <input type="email" name="email" value="{{ old('email', $lead->email) }}" required
                   placeholder="email@exemplu.ro"
                   class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none transition @error('email') border-red-400 @enderror">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Parolă *</label>
            <input type="password" name="password" required
                   placeholder="Minim 8 caractere"
                   class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none transition @error('password') border-red-400 @enderror">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirmă parola *</label>
            <input type="password" name="password_confirmation" required
                   placeholder="Repetă parola"
                   class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none transition">
        </div>

        <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" name="terms" value="1" required
                   class="mt-1 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            <span class="text-sm text-gray-600">
                Sunt de acord să îmi creez cont pe meseriasionline.ro și accept
                <a href="{{ route('terms') }}" target="_blank" class="text-primary-600 underline">Termenii și Condițiile</a>.
            </span>
        </label>

        <button type="submit"
                class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-4 rounded-xl text-lg transition shadow-md">
            Creează contul meu →
        </button>
    </form>

</div>

</body>
</html>
