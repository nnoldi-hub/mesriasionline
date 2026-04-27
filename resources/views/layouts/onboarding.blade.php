<!DOCTYPE html>
<html lang="ro" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Înregistrare Meseriaș') — Omul Potrivit</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">

<div class="min-h-screen flex flex-col justify-center py-10 px-4 sm:px-6 lg:px-8">

    {{-- Logo --}}
    <div class="text-center mb-6">
        <a href="{{ route('home') }}" class="inline-flex items-center space-x-2">
            <span class="text-2xl font-extrabold text-primary-600">Omul Potrivit</span>
        </a>
        <p class="mt-1 text-sm text-gray-500">Platforma meseriașilor de încredere</p>
    </div>

    {{-- Progress Bar --}}
    @isset($currentStep)
    <div class="max-w-lg mx-auto w-full mb-6">
        <div class="flex items-center justify-between mb-2">
            @foreach(['Date personale', 'Primul serviciu', 'Poză profil', 'Disponibilitate'] as $i => $label)
                @php $n = $i + 1; @endphp
                <div class="flex flex-col items-center flex-1">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                        {{ $n < $currentStep ? 'bg-primary-600 text-white' : ($n === $currentStep ? 'bg-primary-600 text-white ring-4 ring-primary-200' : 'bg-gray-200 text-gray-500') }}">
                        @if($n < $currentStep)
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        @else
                            {{ $n }}
                        @endif
                    </div>
                    <span class="text-xs mt-1 {{ $n === $currentStep ? 'text-primary-600 font-semibold' : 'text-gray-400' }} hidden sm:block">{{ $label }}</span>
                </div>
                @if($n < 4)
                    <div class="flex-1 h-1 {{ $n < $currentStep ? 'bg-primary-600' : 'bg-gray-200' }} mx-1 mb-4"></div>
                @endif
            @endforeach
        </div>
        <p class="text-center text-xs text-gray-400">Pasul {{ $currentStep }} din 4</p>
    </div>
    @endisset

    {{-- Card principal --}}
    <div class="max-w-lg mx-auto w-full bg-white rounded-2xl shadow-lg p-8">

        @if ($errors->any())
            <div class="mb-5 bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    <p class="text-center mt-5 text-xs text-gray-400">
        Ai deja cont? <a href="{{ route('login') }}" class="text-primary-600 hover:underline">Conectează-te</a>
    </p>
</div>

</body>
</html>
