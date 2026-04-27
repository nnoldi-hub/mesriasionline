@extends('layouts.app')

@section('title', 'Comparație Meseriași')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <nav class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                <a href="{{ route('home') }}" class="hover:text-primary-600">Acasă</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900">Comparație Meseriași</span>
            </nav>
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Comparație Meseriași</h1>
                    <p class="text-gray-600 mt-1">Compară {{ $craftsmen->count() }} meseriași selectați</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Înapoi la căutare
                    </a>
                    <button type="button" 
                            onclick="clearCompare()" 
                            class="inline-flex items-center px-4 py-2 bg-red-50 border border-red-200 rounded-lg text-red-700 hover:bg-red-100 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Șterge comparație
                    </button>
                </div>
            </div>
        </div>

        <!-- Comparison Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left p-4 bg-gray-50 font-medium text-gray-500 w-48 sticky left-0">
                                Criteriu
                            </th>
                            @foreach($craftsmen as $craftsman)
                            <th class="p-4 min-w-64 text-center">
                                <!-- Craftsman Card Header -->
                                <div class="flex flex-col items-center">
                                    <div class="relative mb-3">
                                        @if($craftsman->profile_photo)
                                            <img src="{{ asset('storage/' . $craftsman->profile_photo) }}" 
                                                 alt="{{ $craftsman->name }}"
                                                 class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-lg">
                                        @else
                                            <div class="w-20 h-20 rounded-full bg-primary-100 flex items-center justify-center border-4 border-white shadow-lg">
                                                <span class="text-2xl font-bold text-primary-600">{{ substr($craftsman->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        @if($craftsman->is_verified)
                                            <div class="absolute -bottom-1 -right-1 bg-blue-500 rounded-full p-1">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <h3 class="font-semibold text-gray-900 text-lg">{{ $craftsman->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $craftsman->specialization ?? $craftsman->category?->name }}</p>
                                    <a href="{{ route('craftsman.show', $craftsman->slug) }}" 
                                       class="mt-2 text-sm text-primary-600 hover:text-primary-700 font-medium">
                                        Vezi profil →
                                    </a>
                                </div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <!-- Rating -->
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 bg-gray-50 font-medium text-gray-700 sticky left-0">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    Rating
                                </div>
                            </td>
                            @foreach($craftsmen as $craftsman)
                            @php
                                $rating = $craftsman->reviews_avg_rating ?? 0;
                                $isMax = $rating == $comparisonData['maxRating'] && $rating > 0;
                            @endphp
                            <td class="p-4 text-center {{ $isMax ? 'bg-green-50' : '' }}">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="text-2xl font-bold {{ $isMax ? 'text-green-600' : 'text-gray-900' }}">
                                        {{ number_format($rating, 1) }}
                                    </span>
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($rating))
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @else
                                                <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                            </td>
                            @endforeach
                        </tr>

                        <!-- Reviews Count -->
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 bg-gray-50 font-medium text-gray-700 sticky left-0">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                    </svg>
                                    Recenzii
                                </div>
                            </td>
                            @foreach($craftsmen as $craftsman)
                            @php
                                $count = $craftsman->reviews_count ?? 0;
                                $isMax = $count == $comparisonData['maxReviews'] && $count > 0;
                            @endphp
                            <td class="p-4 text-center {{ $isMax ? 'bg-green-50' : '' }}">
                                <span class="text-xl font-semibold {{ $isMax ? 'text-green-600' : 'text-gray-900' }}">
                                    {{ $count }}
                                </span>
                                <span class="text-sm text-gray-500 ml-1">recenzii</span>
                            </td>
                            @endforeach
                        </tr>

                        <!-- Experience -->
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 bg-gray-50 font-medium text-gray-700 sticky left-0">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Experiență
                                </div>
                            </td>
                            @foreach($craftsmen as $craftsman)
                            @php
                                $exp = $craftsman->experience_years ?? 0;
                                $isMax = $exp == $comparisonData['maxExperience'] && $exp > 0;
                            @endphp
                            <td class="p-4 text-center {{ $isMax ? 'bg-green-50' : '' }}">
                                <span class="text-xl font-semibold {{ $isMax ? 'text-green-600' : 'text-gray-900' }}">
                                    {{ $exp }}
                                </span>
                                <span class="text-sm text-gray-500 ml-1">ani</span>
                            </td>
                            @endforeach
                        </tr>

                        <!-- Location -->
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 bg-gray-50 font-medium text-gray-700 sticky left-0">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Locație
                                </div>
                            </td>
                            @foreach($craftsmen as $craftsman)
                            <td class="p-4 text-center">
                                <span class="text-gray-900">{{ $craftsman->location?->name ?? '-' }}</span>
                            </td>
                            @endforeach
                        </tr>

                        <!-- Verified -->
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 bg-gray-50 font-medium text-gray-700 sticky left-0">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Verificat
                                </div>
                            </td>
                            @foreach($craftsmen as $craftsman)
                            <td class="p-4 text-center">
                                @if($craftsman->is_verified)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Da
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-600">
                                        Nu
                                    </span>
                                @endif
                            </td>
                            @endforeach
                        </tr>

                        <!-- Services Count -->
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 bg-gray-50 font-medium text-gray-700 sticky left-0">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    Servicii
                                </div>
                            </td>
                            @foreach($craftsmen as $craftsman)
                            @php
                                $svcCount = $craftsman->services->count();
                                $isMax = $svcCount == $comparisonData['maxServices'] && $svcCount > 0;
                            @endphp
                            <td class="p-4 text-center {{ $isMax ? 'bg-green-50' : '' }}">
                                <span class="text-xl font-semibold {{ $isMax ? 'text-green-600' : 'text-gray-900' }}">
                                    {{ $svcCount }}
                                </span>
                                <span class="text-sm text-gray-500 ml-1">servicii</span>
                            </td>
                            @endforeach
                        </tr>

                        <!-- Weekend Availability -->
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 bg-gray-50 font-medium text-gray-700 sticky left-0">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Weekend
                                </div>
                            </td>
                            @foreach($craftsmen as $craftsman)
                            <td class="p-4 text-center">
                                @if($craftsman->available_weekends)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        Disponibil
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-600">
                                        Nu
                                    </span>
                                @endif
                            </td>
                            @endforeach
                        </tr>

                        <!-- Emergency Services -->
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 bg-gray-50 font-medium text-gray-700 sticky left-0">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    Urgențe
                                </div>
                            </td>
                            @foreach($craftsmen as $craftsman)
                            <td class="p-4 text-center">
                                @if($craftsman->emergency_services)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        Disponibil
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-600">
                                        Nu
                                    </span>
                                @endif
                            </td>
                            @endforeach
                        </tr>

                        <!-- Insurance -->
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 bg-gray-50 font-medium text-gray-700 sticky left-0">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    Asigurare
                                </div>
                            </td>
                            @foreach($craftsmen as $craftsman)
                            <td class="p-4 text-center">
                                @if($craftsman->has_insurance)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        Da
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-600">
                                        Nu
                                    </span>
                                @endif
                            </td>
                            @endforeach
                        </tr>

                        <!-- Certifications -->
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 bg-gray-50 font-medium text-gray-700 sticky left-0">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                    Certificări
                                </div>
                            </td>
                            @foreach($craftsmen as $craftsman)
                            @php
                                $certCount = $craftsman->certifications->count();
                                $isMax = $certCount == $comparisonData['maxCertifications'] && $certCount > 0;
                            @endphp
                            <td class="p-4 text-center {{ $isMax ? 'bg-green-50' : '' }}">
                                <span class="text-xl font-semibold {{ $isMax ? 'text-green-600' : 'text-gray-900' }}">
                                    {{ $certCount }}
                                </span>
                                <span class="text-sm text-gray-500 ml-1">certificate</span>
                            </td>
                            @endforeach
                        </tr>

                        <!-- Gallery Preview -->
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 bg-gray-50 font-medium text-gray-700 sticky left-0">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Galerie
                                </div>
                            </td>
                            @foreach($craftsmen as $craftsman)
                            <td class="p-4">
                                @if($craftsman->gallery->count() > 0)
                                    <div class="flex gap-1 justify-center">
                                        @foreach($craftsman->gallery->take(4) as $photo)
                                            <img src="{{ asset('storage/' . $photo->image_path) }}" 
                                                 alt="Portofoliu"
                                                 class="w-12 h-12 rounded object-cover">
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">Fără poze</span>
                                @endif
                            </td>
                            @endforeach
                        </tr>

                        <!-- Contact Action -->
                        <tr class="bg-gray-50">
                            <td class="p-4 font-medium text-gray-700 sticky left-0">
                                Acțiuni
                            </td>
                            @foreach($craftsmen as $craftsman)
                            <td class="p-4 text-center">
                                <div class="flex flex-col gap-2">
                                    <a href="{{ route('craftsman.show', $craftsman->slug) }}" 
                                       class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition">
                                        Vezi profil complet
                                    </a>
                                    @auth
                                        <a href="{{ route('messages.create', ['craftsman_id' => $craftsman->id]) }}" 
                                           class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg text-sm font-medium transition">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                            Trimite mesaj
                                        </a>
                                    @endauth
                                </div>
                            </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Services Comparison (Optional Expanded View) -->
        @if($craftsmen->contains(fn($c) => $c->services->count() > 0))
        <div class="mt-8 bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Comparație Servicii</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-{{ $craftsmen->count() }} gap-6">
                @foreach($craftsmen as $craftsman)
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 mb-3">{{ $craftsman->name }}</h3>
                    @if($craftsman->services->count() > 0)
                        <ul class="space-y-2">
                            @foreach($craftsman->services->take(5) as $service)
                            <li class="flex items-center justify-between text-sm">
                                <span class="text-gray-700">{{ $service->name }}</span>
                                @if($service->price_from)
                                    <span class="text-primary-600 font-medium">
                                        de la {{ number_format($service->price_from, 0) }} lei
                                    </span>
                                @endif
                            </li>
                            @endforeach
                            @if($craftsman->services->count() > 5)
                                <li class="text-sm text-gray-400">
                                    + alte {{ $craftsman->services->count() - 5 }} servicii
                                </li>
                            @endif
                        </ul>
                    @else
                        <p class="text-gray-400 text-sm">Nu are servicii listate</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recent Reviews -->
        @if($craftsmen->contains(fn($c) => $c->reviews->count() > 0))
        <div class="mt-8 bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Recenzii Recente</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-{{ $craftsmen->count() }} gap-6">
                @foreach($craftsmen as $craftsman)
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 mb-3">{{ $craftsman->name }}</h3>
                    @if($craftsman->reviews->count() > 0)
                        <div class="space-y-3">
                            @foreach($craftsman->reviews->take(2) as $review)
                            <div class="border-l-2 border-gray-200 pl-3">
                                <div class="flex items-center gap-1 text-yellow-400 mb-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-3 h-3 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <p class="text-sm text-gray-600 line-clamp-2">{{ $review->comment }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-400 text-sm">Nicio recenzie încă</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function clearCompare() {
    localStorage.removeItem('compareCraftsmen');
    window.location.href = '{{ route("home") }}';
}
</script>
@endpush
