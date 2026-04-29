@extends('layouts.app')

@section('title', 'Cere oferte de la meseriași — Meseriași Online')
@section('meta_description', 'Completezi cererea în 1 minut și primești oferte de la meseriași verificați din zona ta.')

@section('content')
<div class="min-h-screen" style="background-color: #ECF0F1;">

    {{-- Hero --}}
    <section class="py-12" style="background: linear-gradient(135deg, #2980B9 0%, #1a5f8a 100%);">
        <div class="max-w-3xl mx-auto px-4 text-center text-white">
            <div class="text-5xl mb-4">🔨</div>
            <h1 class="text-3xl md:text-4xl font-extrabold mb-3" style="font-family: 'Rubik', sans-serif;">
                Ai nevoie de un meseriaș?
            </h1>
            <p class="text-lg text-blue-100 mb-2">
                Electrician, instalator, zugrav, reparații — orice ai nevoie.
            </p>
            <p class="text-blue-200 text-sm">
                Completezi cererea în 1 minut → meseriașii din zona ta primesc notificare → tu primești oferte.
            </p>
        </div>
    </section>

    {{-- Pași --}}
    <div class="max-w-3xl mx-auto px-4 -mt-4 mb-8">
        <div class="bg-white rounded-2xl shadow-sm p-5 grid grid-cols-3 gap-4 text-center">
            <div>
                <div class="text-2xl mb-1">📝</div>
                <p class="text-sm font-semibold text-gray-700">1. Descrii lucrarea</p>
                <p class="text-xs text-gray-400">~1 minut</p>
            </div>
            <div>
                <div class="text-2xl mb-1">📨</div>
                <p class="text-sm font-semibold text-gray-700">2. Meseriașii sunt notificați</p>
                <p class="text-xs text-gray-400">instant</p>
            </div>
            <div>
                <div class="text-2xl mb-1">✅</div>
                <p class="text-sm font-semibold text-gray-700">3. Primești oferte</p>
                <p class="text-xs text-gray-400">în ore</p>
            </div>
        </div>
    </div>

    {{-- Formular --}}
    <div class="max-w-3xl mx-auto px-4 pb-16">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Completează cererea</h2>

            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                    <ul class="text-sm text-red-600 space-y-1 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('public-request.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Datele tale --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Datele tale de contact</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nume <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                placeholder="Ion Popescu"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-400 @enderror">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telefon <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" required
                                placeholder="07xx xxx xxx"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-400 @enderror">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                placeholder="email@exemplu.ro"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-400 @enderror">
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                {{-- Lucrarea --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Detalii lucrare</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Categorie meseriaș <span class="text-red-500">*</span></label>
                            <select name="category_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('category_id') border-red-400 @enderror">
                                <option value="">Selectează categoria</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Locație</label>
                            <select name="location_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Selectează orașul</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                        {{ $loc->city }}{{ $loc->city !== $loc->county ? ' (' . $loc->county . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Dacă nu găsești orașul tău, lasă necompletat</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Titlu scurt <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                            placeholder="ex: Montaj prize și întrerupătoare în apartament"
                            maxlength="200"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-400 @enderror">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrie lucrarea <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="4" required
                            placeholder="Ex: Am nevoie de montaj 5 prize și 3 întrerupătoare în apartament 2 camere. Apartamentul este în bloc, etaj 3. Materialele le am deja."
                            maxlength="2000"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">Minim 20 caractere. Cu cât ești mai detaliat, cu atât primești oferte mai precise.</p>
                    </div>
                </div>

                <hr class="border-gray-100">

                {{-- Preferințe --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Preferințe (opționale)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Urgență</label>
                            <select name="urgency"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="flexible" {{ old('urgency', 'flexible') === 'flexible' ? 'selected' : '' }}>Flexibil</option>
                                <option value="this_week" {{ old('urgency') === 'this_week' ? 'selected' : '' }}>Săptămâna aceasta</option>
                                <option value="urgent" {{ old('urgency') === 'urgent' ? 'selected' : '' }}>Urgent (1-2 zile)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data preferată</label>
                            <input type="date" name="preferred_date" value="{{ old('preferred_date') }}"
                                min="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Buget maxim (lei)</label>
                            <input type="number" name="budget_max" value="{{ old('budget_max') }}"
                                min="0" step="100" placeholder="ex: 1500"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="pt-2">
                    <button type="submit"
                        class="w-full text-white font-bold py-4 px-8 rounded-xl text-lg transition hover:opacity-90 shadow-lg"
                        style="background-color: #C0392B;">
                        🔨 Trimite cererea — Gratuit
                    </button>
                    <p class="text-center text-xs text-gray-400 mt-3">
                        Cererea ta va fi trimisă meseriașilor cu abonament activ din zona ta. Nu ești obligat să accepti nicio ofertă.
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
