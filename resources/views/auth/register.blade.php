@extends('layouts.app')

@section('title', 'Devino Meseriaș')

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <svg class="w-16 h-16 text-primary-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-extrabold text-gray-900">Devino Meseriaș</h1>
                <p class="mt-2 text-gray-600">Alătură-te platformei noastre și începe să primești clienți</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 bg-error-50 border border-error-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-error-700">Erori la validare:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <!-- Informații de Bază -->
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informații de Bază</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Nume Complet <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600 @error('name') border-error-400 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-error-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600 @error('email') border-error-400 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-error-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                Telefon <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600 @error('phone') border-error-400 @enderror"
                                placeholder="0712345678">
                            @error('phone')
                                <p class="mt-1 text-sm text-error-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                Parolă <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password" name="password" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600 @error('password') border-error-400 @enderror">
                            @error('password')
                                <p class="mt-1 text-sm text-error-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                Confirmă Parola <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600">
                        </div>
                    </div>
                </div>

                <!-- Detalii Profesionale -->
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Detalii Profesionale</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Categorie <span class="text-red-500">*</span>
                            </label>
                            <select id="category_id" name="category_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600 @error('category_id') border-error-400 @enderror">
                                <option value="">Selectează categoria</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-error-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="location_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Județ <span class="text-red-500">*</span>
                            </label>
                            <select id="location_id" name="location_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600 @error('location_id') border-error-400 @enderror">
                                <option value="">Selectează județul</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('location_id')
                                <p class="mt-1 text-sm text-error-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="experience_years" class="block text-sm font-medium text-gray-700 mb-1">
                                Ani Experiență <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="experience_years" name="experience_years" value="{{ old('experience_years') }}" 
                                required min="0" max="50"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600 @error('experience_years') border-error-400 @enderror">
                            @error('experience_years')
                                <p class="mt-1 text-sm text-error-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="service_radius_km" class="block text-sm font-medium text-gray-700 mb-1">
                                Rază Acoperire (km)
                            </label>
                            <input type="number" id="service_radius_km" name="service_radius_km" 
                                value="{{ old('service_radius_km', 30) }}" min="5" max="100"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600">
                        </div>

                        <div class="md:col-span-2">
                            <label for="specialization" class="block text-sm font-medium text-gray-700 mb-1">
                                Specializare <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="specialization" name="specialization" value="{{ old('specialization') }}" 
                                required maxlength="255"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600 @error('specialization') border-error-400 @enderror"
                                placeholder="Ex: Instalații termice și sanitare">
                            @error('specialization')
                                <p class="mt-1 text-sm text-error-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Descriere Servicii <span class="text-red-500">*</span>
                            </label>
                            <textarea id="description" name="description" rows="4" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600 @error('description') border-error-400 @enderror"
                                placeholder="Descrie experiența și serviciile tale (minim 50 caractere)">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-error-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Opțiuni Suplimentare -->
                <div class="pb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Opțiuni Suplimentare</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <input type="checkbox" id="available_weekends" name="available_weekends" value="1"
                                {{ old('available_weekends') ? 'checked' : '' }}
                                class="h-4 w-4 text-primary-600 focus:ring-primary-600 border-gray-300 rounded mt-1">
                            <label for="available_weekends" class="ml-3">
                                <span class="block text-sm font-medium text-gray-700">Disponibil în weekend</span>
                                <span class="block text-sm text-gray-500">Pot lucra sâmbăta și duminica</span>
                            </label>
                        </div>

                        <div class="flex items-start">
                            <input type="checkbox" id="emergency_services" name="emergency_services" value="1"
                                {{ old('emergency_services') ? 'checked' : '' }}
                                class="h-4 w-4 text-primary-600 focus:ring-primary-600 border-gray-300 rounded mt-1">
                            <label for="emergency_services" class="ml-3">
                                <span class="block text-sm font-medium text-gray-700">Servicii de urgență</span>
                                <span class="block text-sm text-gray-500">Disponibil pentru intervenții urgente</span>
                            </label>
                        </div>

                        <div class="flex items-start">
                            <input type="checkbox" id="has_insurance" name="has_insurance" value="1"
                                {{ old('has_insurance') ? 'checked' : '' }}
                                class="h-4 w-4 text-primary-600 focus:ring-primary-600 border-gray-300 rounded mt-1">
                            <label for="has_insurance" class="ml-3">
                                <span class="block text-sm font-medium text-gray-700">Am asigurare profesională</span>
                                <span class="block text-sm text-gray-500">Dispun de asigurare de răspundere civilă</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Google reCAPTCHA --}}
                <x-recaptcha />

                <!-- Submit Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('home') }}" class="text-sm text-gray-600 hover:text-gray-900">
                        ← Înapoi la pagina principală
                    </a>
                    <div class="flex gap-4">
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">
                            Am deja cont
                        </a>
                        <button type="submit" 
                            class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                            Înregistrează-te
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Ai deja un cont? 
                    <a href="{{ route('login') }}" class="font-medium text-primary-600 hover:text-primary-500">
                        Autentifică-te
                    </a>
                </p>
                <p class="text-sm text-gray-600 mt-2">
                    Ești client și vrei să cauți meșteri? 
                    <a href="{{ route('register.client.form') }}" class="font-medium text-primary-600 hover:text-primary-500">
                        Creează cont client
                    </a>
                </p>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Notă importantă</h3>
                    <p class="mt-1 text-sm text-blue-700">
                        După înregistrare, contul tău va fi verificat de administratorii noștri. 
                        Vei primi o notificare când contul va fi activat și vei putea primi comenzi.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
