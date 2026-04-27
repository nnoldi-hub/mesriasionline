@extends('layouts.app')

@section('title', 'Finalizare abonament — ' . $plan->name)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">

        {{-- Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">

            {{-- Header --}}
            <div class="bg-gradient-to-r from-emerald-600 to-teal-500 px-8 py-6 text-white text-center">
                <h1 class="text-2xl font-bold">Activează planul {{ $plan->name }}</h1>
                <p class="text-emerald-100 text-sm mt-1">{{ $plan->description }}</p>
            </div>

            <div class="p-8">
                {{-- Summary --}}
                <div class="rounded-xl bg-gray-50 dark:bg-gray-700/50 p-4 mb-6 space-y-3">
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                        <span>Plan</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $plan->name }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                        <span>Durată</span>
                        <span class="font-semibold text-gray-900 dark:text-white">30 de zile</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                        <span>Oferte/lună</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ $plan->max_quotes_per_month === 0 ? 'Nelimitate' : $plan->max_quotes_per_month }}
                        </span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-600 pt-3 flex justify-between">
                        <span class="font-semibold text-gray-700 dark:text-gray-300">Total</span>
                        <span class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400">
                            {{ number_format($plan->price_monthly, 0) }} RON
                        </span>
                    </div>
                </div>

                {{-- Features --}}
                <ul class="space-y-2 mb-6">
                    @foreach($plan->features ?? [] as $feature)
                        <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>

                {{-- Stripe CTA --}}
                <form method="POST" action="{{ route('payment.stripe', $plan->slug) }}">
                    @csrf
                    <button type="submit"
                        class="w-full py-3.5 px-6 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors shadow-lg shadow-emerald-200 dark:shadow-emerald-900/30 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Plătește cu cardul — {{ number_format($plan->price_monthly, 0) }} RON
                    </button>
                </form>

                <div class="mt-4 text-center">
                    <a href="{{ route('plans.index') }}" class="text-sm text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        ← Înapoi la planuri
                    </a>
                </div>

                {{-- Security note --}}
                <div class="mt-6 flex items-center justify-center gap-2 text-xs text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Plată securizată prin Stripe. Datele cardului nu ajung pe serverele noastre.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
