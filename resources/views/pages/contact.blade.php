@extends('layouts.app')

@section('title', 'Contact - Meseriasi Profesionisti')

@section('content')
<div class="bg-white">
    <!-- Hero Section -->
    <section class="bg-linear-to-br from-primary-600 to-primary-700 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl md:text-5xl font-bold text-center mb-4">Contacteaza-ne</h1>
            <p class="text-xl text-center text-secondary-100 max-w-3xl mx-auto">
                Suntem aici sa te ajutam. Trimite-ne un mesaj si iti vom raspunde in cel mai scurt timp.
            </p>
        </div>
    </section>

    <!-- Contact Form & Info -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Trimite-ne un mesaj</h2>
                    
                    @if(session('success'))
                        <div class="bg-success-100 border-l-4 border-success-600 text-success-700 p-4 mb-6 rounded">
                            <p class="font-medium">{{ session('success') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nume complet *</label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                value="{{ old('name') }}"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent @error('name') border-error-400 @enderror"
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="{{ old('email') }}"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent @error('email') border-error-400 @enderror"
                            >
                            @error('email')
                                <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                            <input 
                                type="tel" 
                                id="phone" 
                                name="phone" 
                                value="{{ old('phone') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent @error('phone') border-error-400 @enderror"
                            >
                            @error('phone')
                                <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subiect *</label>
                            <input 
                                type="text" 
                                id="subject" 
                                name="subject" 
                                value="{{ old('subject') }}"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent @error('subject') border-error-400 @enderror"
                            >
                            @error('subject')
                                <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Mesaj *</label>
                            <textarea 
                                id="message" 
                                name="message" 
                                rows="6"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent @error('message') border-error-400 @enderror"
                            >{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Google reCAPTCHA --}}
                        <x-recaptcha />

                        <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold px-6 py-3 rounded-lg transition">
                            Trimite mesaj
                        </button>
                    </form>
                </div>

                <!-- Contact Info -->
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Informatii de contact</h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-secondary-200 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Email</h3>
                                <p class="text-gray-600">contact@meseriasionline.ro</p>
                                <p class="text-gray-600">suport@meseriasionline.ro</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-secondary-200 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Telefon</h3>
                                <p class="text-gray-600">0740 173 581</p>
                                <p class="text-sm text-gray-500">Luni - Vineri: 9:00 - 18:00</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-secondary-200 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Adresa</h3>
                                <p class="text-gray-600">Slt. Petre Ionel 205, 077030</p>
                                <p class="text-gray-600">Branesti, Ilfov</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-secondary-200 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Program</h3>
                                <p class="text-gray-600">Luni - Vineri: 9:00 - 18:00</p>
                                <p class="text-gray-600">Sambata: 10:00 - 14:00</p>
                                <p class="text-gray-600">Duminica: Inchis</p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Section -->
                    <div class="mt-12">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Intrebari frecvente</h3>
                        <div class="space-y-4">
                            <div class="bg-secondary-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-900 mb-2">Cat dureaza pana primesc un raspuns?</h4>
                                <p class="text-gray-600 text-sm">De obicei raspundem in maxim 24 de ore in zilele lucratoare.</p>
                            </div>
                            <div class="bg-secondary-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-900 mb-2">Pot vorbi direct cu un meserias?</h4>
                                <p class="text-gray-600 text-sm">Da, dupa ce gasesti meseriasul potrivit, poti sa-l contactezi direct prin telefon.</p>
                            </div>
                            <div class="bg-secondary-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-900 mb-2">Cum pot deveni meserias in platforma?</h4>
                                <p class="text-gray-600 text-sm">Acceseaza pagina de <a href="{{ route('register') }}" class="text-primary-600 hover:underline">inregistrare</a> si completeaza formularul.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
