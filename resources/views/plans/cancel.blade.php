@extends('layouts.app')

@section('title', 'Plată anulată — Omul Potrivit')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md text-center">

        {{-- Cancel icon --}}
        <div class="mx-auto mb-6 w-20 h-20 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
            <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">Plată anulată</h1>
        <p class="text-gray-500 dark:text-gray-400 mb-8">
            Nu a fost efectuată nicio plată. Poți reveni oricând la planuri pentru a face upgrade.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('plans.index') }}"
               class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-colors">
                Înapoi la planuri
            </a>
            <a href="{{ route('craftsman.dashboard') }}"
               class="px-6 py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 font-semibold transition-colors">
                Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
