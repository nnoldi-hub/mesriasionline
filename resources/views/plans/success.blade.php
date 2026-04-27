@extends('layouts.app')

@section('title', 'Plată reușită — Omul Potrivit')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md text-center">

        {{-- Success icon --}}
        <div class="mx-auto mb-6 w-20 h-20 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
            <svg class="w-10 h-10 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">Plată reușită!</h1>

        @if($plan)
            <p class="text-lg text-gray-600 dark:text-gray-400 mb-2">
                Planul <strong class="text-emerald-600 dark:text-emerald-400">{{ $plan->name }}</strong> a fost activat.
            </p>
        @endif

        <p class="text-sm text-gray-500 dark:text-gray-500 mb-8">
            Abonamentul tău este activ pentru 30 de zile. Vei primi un email de confirmare.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('craftsman.dashboard') }}"
               class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-colors">
                Mergi la dashboard
            </a>
            <a href="{{ route('plans.index') }}"
               class="px-6 py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 font-semibold transition-colors">
                Planurile mele
            </a>
        </div>
    </div>
</div>
@endsection
