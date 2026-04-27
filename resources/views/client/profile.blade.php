@extends('layouts.client')

@section('title', 'Profilul Meu')
@section('page-title', 'Profilul Meu')

@section('content')
<div class="max-w-3xl">
    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 flex items-start">
        <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="text-green-800">{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <!-- Header profil -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center">
                <div class="w-20 h-20 bg-primary-100 rounded-full flex items-center justify-center text-primary-600 text-2xl font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-semibold text-gray-900">{{ auth()->user()->name }}</h2>
                    <p class="text-gray-600">{{ auth()->user()->email }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        Membru din {{ auth()->user()->created_at->format('d M Y') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Formular editare -->
        <form action="{{ route('client.profile.update') }}" method="POST" class="p-6">
            @csrf
            
            <div class="space-y-6">
                <!-- Nume -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nume complet <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600 @error('name') border-red-400 @enderror">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600 @error('email') border-red-400 @enderror">
                    @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Telefon -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                        Telefon
                    </label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600 @error('phone') border-red-400 @enderror"
                        placeholder="07XX XXX XXX">
                    @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Separator -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Schimbă Parola</h3>
                    <p class="text-sm text-gray-600 mb-4">Lasă câmpurile goale dacă nu dorești să schimbi parola.</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                                Parola curentă
                            </label>
                            <input type="password" id="current_password" name="current_password"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600 @error('current_password') border-red-400 @enderror">
                            @error('current_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                    Parola nouă
                                </label>
                                <input type="password" id="password" name="password"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600 @error('password') border-red-400 @enderror">
                                @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                    Confirmă parola
                                </label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buton salvare -->
                <div class="flex justify-end pt-4">
                    <button type="submit" 
                        class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                        Salvează Modificările
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Statistici cont -->
    <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Statistici Cont</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-2xl font-bold text-primary-600">{{ auth()->user()->addresses()->count() }}</p>
                <p class="text-sm text-gray-600">Adrese salvate</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-2xl font-bold text-primary-600">{{ \App\Models\QuoteRequest::where('client_id', auth()->id())->count() }}</p>
                <p class="text-sm text-gray-600">Cereri trimise</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-2xl font-bold text-primary-600">{{ \App\Models\Appointment::where('client_email', auth()->user()->email)->count() }}</p>
                <p class="text-sm text-gray-600">Programări</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-2xl font-bold text-primary-600">{{ \App\Models\Review::where('client_id', auth()->id())->count() }}</p>
                <p class="text-sm text-gray-600">Recenzii date</p>
            </div>
        </div>
    </div>
</div>
@endsection
