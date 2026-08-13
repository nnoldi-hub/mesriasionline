@extends('layouts.app')

@section('title', 'Despre Noi - Omul Potrivit')

@section('content')
<div class="bg-white">
    <!-- Hero Section -->
    <section class="bg-linear-to-br from-primary-600 to-primary-700 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl md:text-5xl font-bold text-center mb-4">Despre Noi</h1>
            <p class="text-xl text-center text-secondary-100 max-w-3xl mx-auto">
                Conectam clientii cu cei mai buni meseriasi verificati din Romania
            </p>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="prose prose-lg max-w-none">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Cine suntem</h2>
                <p class="text-gray-600 mb-6">
                    Suntem o platforma moderna dedicata conectarii clientilor cu meseriasi profesionisti verificati. 
                    Misiunea noastra este sa facem procesul de gasire a unui meserias de incredere cat mai simplu si sigur.
                </p>

                <h2 class="text-3xl font-bold text-gray-900 mb-6 mt-12">Ce facem</h2>
                <p class="text-gray-600 mb-6">
                    Oferim o platforma unde meseriasii verificati isi pot prezenta serviciile, iar clientii pot gasi 
                    rapid profesionistii potriviti pentru nevoile lor. Fiecare meserias din platforma noastra este verificat 
                    si are recenzii reale de la clienti.
                </p>

                <div class="grid md:grid-cols-3 gap-8 my-12">
                    <div class="bg-secondary-50 p-6 rounded-lg">
                        <div class="w-12 h-12 bg-primary-600 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Verificare riguroasa</h3>
                        <p class="text-gray-600">Fiecare meserias este verificat inainte de a fi acceptat in platforma.</p>
                    </div>

                    <div class="bg-secondary-50 p-6 rounded-lg">
                        <div class="w-12 h-12 bg-primary-600 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Transparenta totala</h3>
                        <p class="text-gray-600">Preturi clare, recenzii reale si informatii complete despre fiecare meserias.</p>
                    </div>

                    <div class="bg-secondary-50 p-6 rounded-lg">
                        <div class="w-12 h-12 bg-primary-600 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Suport dedicat</h3>
                        <p class="text-gray-600">Echipa noastra este mereu disponibila sa te ajute cu orice intrebare.</p>
                    </div>
                </div>

                <h2 class="text-3xl font-bold text-gray-900 mb-6 mt-12">Valorile noastre</h2>
                <ul class="space-y-4 text-gray-600">
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-primary-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span><strong>Incredere:</strong> Construim relatii bazate pe transparenta si onestitate.</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-primary-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span><strong>Calitate:</strong> Promovam doar meseriasi cu experienta si recenzii pozitive.</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-primary-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span><strong>Simplitate:</strong> Facem procesul de cautare si contactare cat mai usor posibil.</span>
                    </li>
                </ul>

                <div class="bg-secondary-100 p-8 rounded-lg mt-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Devino parte din comunitatea noastra</h2>
                    <p class="text-gray-600 mb-6">
                        Esti meserias si vrei sa-ti dezvolti afacerea? Alatura-te platformei noastre si beneficiaza 
                        de vizibilitate crescuta si acces la mii de clienti potentiali.
                    </p>
                    <a href="{{ route('register') }}" class="inline-block bg-primary-600 hover:bg-primary-700 text-white font-semibold px-8 py-3 rounded-lg transition">
                        Devino Meserias
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
