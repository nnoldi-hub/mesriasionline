<!DOCTYPE html>
<html lang="ro" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Înscrie-te gratuit ca meseriaș — meseriasionline.ro</title>
    <meta name="description" content="Primești lucrări direct pe telefon, fără comisioane ascunse. Înscrie-te în 30 de secunde.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">

{{-- Header simplu --}}
<header class="bg-white shadow-sm py-4 px-6">
    <div class="max-w-4xl mx-auto flex items-center justify-between">
        <a href="{{ route('home') }}" class="text-xl font-extrabold text-primary-600">
            meseriasionline.ro
        </a>
        <span class="text-sm text-gray-500">Înregistrare gratuită</span>
    </div>
</header>

<main class="max-w-2xl mx-auto px-4 py-10">

    {{-- Hero --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center bg-green-100 text-green-800 text-sm font-semibold px-4 py-2 rounded-full mb-4">
            ✅ Primele 10 locuri per meserie sunt gratuite
        </div>
        <h1 class="text-3xl font-extrabold text-gray-900 mb-3">
            Cauți mai mulți clienți<br>în orașul tău?
        </h1>
        <p class="text-lg text-gray-600">
            Înscrie-te gratuit și primești lucrări <strong>direct pe telefon</strong>,<br>
            fără comisioane ascunse. Completează în <strong>30 de secunde</strong>.
        </p>
    </div>

    {{-- Erori validare --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-6">
            <ul class="list-disc list-inside space-y-1 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('recruitment.store') }}" enctype="multipart/form-data"
          class="bg-white rounded-2xl shadow-xl p-6 md:p-8 space-y-6">
        @csrf

        {{-- Secțiunea 1 — Date rapide --}}
        <div>
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-7 h-7 bg-primary-600 text-white rounded-full flex items-center justify-center text-sm font-bold">1</span>
                Date rapide
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Nume --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nume complet *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="Ex: Ion Popescu"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition @error('name') border-red-400 @enderror">
                </div>

                {{-- Telefon --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefon *</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required
                           placeholder="07xx xxx xxx"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition @error('phone') border-red-400 @enderror">
                </div>

                {{-- Oraș --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Oraș / zonă de lucru *</label>
                    <input type="text" name="city" value="{{ old('city') }}" required
                           placeholder="Ex: București, Cluj-Napoca"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition @error('city') border-red-400 @enderror">
                </div>

                {{-- Meserie --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meseria ta *</label>
                    <select name="trade" required
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition @error('trade') border-red-400 @enderror">
                        <option value="">— Selectează meseria —</option>
                        @foreach($trades as $value => $label)
                            <option value="{{ $value }}" {{ (old('trade', $selectedTrade) === $value) ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Experiență --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ani de experiență *</label>
                    <select name="experience_range" required
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition @error('experience_range') border-red-400 @enderror">
                        <option value="">— Selectează —</option>
                        <option value="0-2"  {{ old('experience_range') === '0-2' ? 'selected' : '' }}>0–2 ani</option>
                        <option value="3-5"  {{ old('experience_range') === '3-5' ? 'selected' : '' }}>3–5 ani</option>
                        <option value="5+"   {{ old('experience_range') === '5+' ? 'selected' : '' }}>Peste 5 ani</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Beneficiu --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-blue-800 text-sm">
            🎯 <strong>Înregistrare gratuită.</strong> Primești clienți direct pe telefon, fără comisioane ascunse.
        </div>

        {{-- Secțiunea 2 — Email (opțional pentru cont) --}}
        <div>
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-7 h-7 bg-primary-600 text-white rounded-full flex items-center justify-center text-sm font-bold">2</span>
                Email (pentru activarea contului)
                <span class="text-xs font-normal text-gray-400 ml-1">opțional</span>
            </h2>
            <input type="email" name="email" value="{{ old('email') }}"
                   placeholder="email@exemplu.ro"
                   class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition @error('email') border-red-400 @enderror">
            <p class="text-xs text-gray-500 mt-1">Dacă adaugi emailul, îți trimitem link-ul de activare a contului complet.</p>
        </div>

        {{-- Secțiunea 3 — Upload-uri opționale --}}
        <div>
            <h2 class="text-lg font-bold text-gray-800 mb-1 flex items-center gap-2">
                <span class="w-7 h-7 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">3</span>
                Poze (opțional, dar cresc calitatea profilului)
            </h2>
            <p class="text-xs text-gray-500 mb-4">Meseriașii cu poze primesc cu 3× mai multe cereri.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Poză de profil</label>
                    <input type="file" name="profile_photo" accept="image/*"
                           class="w-full text-sm text-gray-600 border border-gray-300 rounded-xl px-3 py-2 cursor-pointer file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Poză cu o lucrare</label>
                    <input type="file" name="work_photo" accept="image/*"
                           class="w-full text-sm text-gray-600 border border-gray-300 rounded-xl px-3 py-2 cursor-pointer file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-4 rounded-xl text-lg transition shadow-md hover:shadow-lg">
            Rezervă-mi locul gratuit →
        </button>

        <p class="text-center text-xs text-gray-400">
            Prin trimiterea formularului ești de acord cu
            <a href="{{ route('terms') }}" target="_blank" class="underline hover:text-gray-600">Termenii și Condițiile</a>
            și
            <a href="{{ route('privacy') }}" target="_blank" class="underline hover:text-gray-600">Politica de Confidențialitate</a>.
        </p>
    </form>

    {{-- Social proof --}}
    <div class="mt-8 grid grid-cols-3 gap-4 text-center">
        <div class="bg-white rounded-xl p-4 shadow-sm">
            <div class="text-2xl font-extrabold text-primary-600">100%</div>
            <div class="text-xs text-gray-500 mt-1">Gratuit</div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm">
            <div class="text-2xl font-extrabold text-primary-600">0</div>
            <div class="text-xs text-gray-500 mt-1">Comisioane ascunse</div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm">
            <div class="text-2xl font-extrabold text-primary-600">30s</div>
            <div class="text-xs text-gray-500 mt-1">Timp completare</div>
        </div>
    </div>

</main>

<footer class="text-center py-6 text-xs text-gray-400">
    © {{ date('Y') }} meseriasionline.ro — Platforma meseriașilor de încredere
</footer>

</body>
</html>
