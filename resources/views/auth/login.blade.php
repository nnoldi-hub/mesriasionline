@extends('layouts.app')

@section('title', 'Autentificare')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="flex justify-center">
                <svg class="w-16 h-16 text-primary-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                </svg>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Autentificare
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Intră în contul tău pentru a accesa dashboard-ul
            </p>
        </div>

        <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
            @csrf
            
            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" name="email" type="email" required 
                        value="{{ old('email') }}"
                        class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary-600 focus:border-primary-600 focus:z-10 sm:text-sm @error('email') border-error-400 @enderror" 
                        placeholder="admin@meseriasi.ro">
                    @error('email')
                        <p class="mt-1 text-sm text-error-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Parolă</label>
                    <input id="password" name="password" type="password" required 
                        class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary-600 focus:border-primary-600 focus:z-10 sm:text-sm" 
                        placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" 
                        class="h-4 w-4 text-primary-600 focus:ring-primary-600 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Ține-mă minte
                    </label>
                </div>
            </div>

            {{-- Google reCAPTCHA --}}
            <x-recaptcha />

            <div>
                <button type="submit" 
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                    Autentificare
                </button>
            </div>

            <div class="text-center space-y-3">
                <p class="text-sm text-gray-600">
                    Nu ai cont? 
                    <a href="{{ route('register.client.form') }}" class="font-medium text-primary-600 hover:text-primary-500">
                        Creează cont
                    </a>
                </p>
                <p class="text-sm text-gray-600">
                    Ești meseriaș? 
                    <a href="{{ route('register') }}" class="font-medium text-primary-600 hover:text-primary-500">
                        Înscrie-te aici
                    </a>
                </p>
            </div>

            <div class="text-center text-sm text-gray-600 bg-gray-50 p-4 rounded-lg">
                <p class="font-semibold mb-2">Conturi demo:</p>
                <p>Admin: <span class="font-mono text-primary-600">admin@dariabeauty.ro</span></p>
                <p>Parolă: <span class="font-mono text-primary-600">password</span></p>
            </div>
        </form>
    </div>
</div>
@endsection
