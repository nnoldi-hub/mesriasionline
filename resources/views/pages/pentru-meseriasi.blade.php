@extends('layouts.app')

@section('title', 'Devino Meseriaș — Primești clienți reali în București')

@section('content')

{{-- Hero --}}
<section class="bg-gradient-to-br from-primary-700 to-primary-900 text-white py-20 px-4">
    <div class="max-w-4xl mx-auto text-center">
        <span class="inline-block bg-primary-500 bg-opacity-40 text-primary-100 text-xs font-semibold px-3 py-1 rounded-full mb-4 uppercase tracking-wide">
            Gratuit în primele 30 de zile
        </span>
        <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight mb-5">
            Primești clienți reali<br>din București.
        </h1>
        <p class="text-lg sm:text-xl text-primary-200 mb-8 max-w-2xl mx-auto">
            Fără comisioane ascunse. Fără bătăi de cap. Profilul tău activ în mai puțin de 3 minute.
        </p>
        <a href="{{ route('onboarding.quick-register') }}"
           class="inline-block bg-white text-primary-700 font-bold text-lg px-8 py-4 rounded-xl hover:bg-primary-50 transition-colors shadow-lg">
            Începe gratuit →
        </a>
        <p class="text-primary-300 text-sm mt-4">Nu necesită card de credit</p>
    </div>
</section>

{{-- Cum funcționează --}}
<section class="py-16 px-4 bg-white">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-900 mb-12">Cum funcționează?</h2>
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-8 text-center">
            @foreach([
                ['01', 'Creezi contul', 'Nume, email, parolă — gata în 30 secunde.'],
                ['02', 'Adaugi meseria', 'Specializare, oraș și primul tău serviciu.'],
                ['03', 'Încarci o poză', 'Profilul cu poză primește de 3× mai mulți clienți.'],
                ['04', 'Primești clienți', 'Profil public activ imediat, vizibil în căutări.'],
            ] as [$nr, $titlu, $desc])
            <div>
                <div class="w-12 h-12 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center text-lg font-extrabold mx-auto mb-3">
                    {{ $nr }}
                </div>
                <h3 class="font-semibold text-gray-900 mb-1">{{ $titlu }}</h3>
                <p class="text-sm text-gray-500">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Venituri --}}
<section class="py-16 px-4 bg-gray-50">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-900 mb-3">Cât câștigă un meseriaș mediu?</h2>
        <p class="text-center text-gray-500 mb-10">Estimări bazate pe piața din București:</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            @foreach([
                ['Electrician', '150–300 RON', 'per intervenție', 'text-yellow-600', 'bg-yellow-50'],
                ['Instalator', '200–500 RON', 'per lucrare', 'text-blue-600', 'bg-blue-50'],
                ['Zugrav', '15–25 RON', 'per m²', 'text-green-600', 'bg-green-50'],
            ] as [$rol, $pret, $unit, $color, $bg])
            <div class="{{ $bg }} rounded-2xl p-6 text-center">
                <p class="font-semibold text-gray-700 mb-2">{{ $rol }}</p>
                <p class="text-3xl font-extrabold {{ $color }} mb-1">{{ $pret }}</p>
                <p class="text-sm text-gray-500">{{ $unit }}</p>
            </div>
            @endforeach
        </div>
        <p class="text-center text-xs text-gray-400 mt-6">* Prețurile variază în funcție de complexitate și zonă geografică.</p>
    </div>
</section>

{{-- Testimoniale --}}
<section class="py-16 px-4 bg-white">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-900 mb-10">Ce spun meseriașii noștri</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            @foreach([
                ['Gheorghe M.', 'Electrician, București', '"Am primit primul client în prima zi după ce mi-am creat profilul. Platforma e simplă și funcționează."'],
                ['Mihai T.', 'Instalator, Ilfov', '"Nu mai pierd timp cu anunțuri pe OLX. Clienții vin direct la mine prin Omul Potrivit."'],
                ['Florin D.', 'Zugrav, Sector 3', '"Mi-am completat profilul în 5 minute și acum am 3-4 solicitări pe săptămână."'],
            ] as [$name, $spec, $quote])
            <div class="bg-gray-50 rounded-xl p-5">
                <p class="text-gray-600 text-sm italic mb-4">{{ $quote }}</p>
                <div>
                    <p class="font-semibold text-gray-900 text-sm">{{ $name }}</p>
                    <p class="text-xs text-gray-400">{{ $spec }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA Final --}}
<section class="py-16 px-4 bg-primary-700 text-white text-center">
    <div class="max-w-xl mx-auto">
        <h2 class="text-3xl font-extrabold mb-4">Gata să primești clienți noi?</h2>
        <p class="text-primary-200 mb-8">Creează-ți profilul gratuit acum. Fără card, fără obligații.</p>
        <a href="{{ route('onboarding.quick-register') }}"
           class="inline-block bg-white text-primary-700 font-bold text-lg px-8 py-4 rounded-xl hover:bg-primary-50 transition-colors shadow-lg">
            Începe gratuit →
        </a>
    </div>
</section>

@endsection
