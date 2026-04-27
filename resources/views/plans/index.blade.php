@extends('layouts.app')

@section('title', 'Planuri și Prețuri — Fixacasa')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-4">
    <div class="max-w-5xl mx-auto">

        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Alege planul potrivit pentru tine
            </h1>
            <p class="text-lg text-gray-500 dark:text-gray-400">
                Începe gratuit. Fă upgrade oricând, fără contract.
            </p>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mb-8 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300 text-center font-medium">
                {{ session('success') }}
            </div>
        @endif

        {{-- Pricing Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($plans as $plan)
                @php
                    $isCurrent = $currentPlan && $currentPlan->slug === $plan->slug;
                    $isPro     = $plan->slug === 'pro';
                @endphp

                <div class="relative rounded-2xl border-2 {{ $isPro ? 'border-emerald-500 shadow-xl shadow-emerald-100 dark:shadow-emerald-900/20' : 'border-gray-200 dark:border-gray-700' }} bg-white dark:bg-gray-800 flex flex-col">

                    {{-- "Recomandat" ribbon --}}
                    @if($isPro)
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                            <span class="bg-emerald-500 text-white text-xs font-bold px-4 py-1.5 rounded-full shadow">
                                ⭐ Recomandat
                            </span>
                        </div>
                    @endif

                    <div class="p-8 flex-1">
                        {{-- Plan name + badge --}}
                        <div class="flex items-center gap-3 mb-2">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $plan->name }}</h2>
                            @if($plan->badge_visible)
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full
                                    {{ $plan->slug === 'pro' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                    {{ strtoupper($plan->name) }}
                                </span>
                            @endif
                        </div>

                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">{{ $plan->description }}</p>

                        {{-- Price --}}
                        <div class="mb-6">
                            @if($plan->price_monthly == 0)
                                <span class="text-4xl font-extrabold text-gray-900 dark:text-white">Gratuit</span>
                            @else
                                <span class="text-4xl font-extrabold text-gray-900 dark:text-white">{{ number_format($plan->price_monthly, 0) }} RON</span>
                                <span class="text-gray-400 text-sm">/lună</span>
                            @endif
                        </div>

                        {{-- Features list --}}
                        <ul class="space-y-3">
                            @foreach($plan->features ?? [] as $feature)
                                <li class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <svg class="w-4 h-4 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- CTA --}}
                    <div class="p-8 pt-0">
                        @if($isCurrent)
                            <div class="w-full text-center py-3 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm font-semibold">
                                ✓ Planul tău actual
                            </div>
                            @if(!$plan->isFree() && $activeSubscription)
                                <form method="POST" action="{{ route('subscription.cancel') }}" class="mt-3"
                                      onsubmit="return confirm('Ești sigur că vrei să anulezi abonamentul?')">
                                    @csrf
                                    <button type="submit" class="w-full text-sm text-gray-400 dark:text-gray-500 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                                        Anulează abonamentul
                                    </button>
                                </form>
                            @endif
                        @else
                            @if($plan->isFree())
                                <form method="POST" action="{{ route('subscription.subscribe', $plan->slug) }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full py-3 px-6 rounded-xl font-semibold text-sm transition-all bg-gray-900 hover:bg-gray-700 dark:bg-white dark:hover:bg-gray-200 text-white dark:text-gray-900">
                                        Treci la Free
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('payment.checkout', $plan->slug) }}"
                                    class="block w-full py-3 px-6 rounded-xl font-semibold text-sm text-center transition-all
                                        {{ $isPro
                                            ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg shadow-emerald-200 dark:shadow-emerald-900/30'
                                            : 'bg-gray-900 hover:bg-gray-700 dark:bg-white dark:hover:bg-gray-200 text-white dark:text-gray-900' }}">
                                    Activează {{ $plan->name }}
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- FAQ / Note --}}
        <div class="mt-12 text-center text-sm text-gray-400 dark:text-gray-500 space-y-2">
            <p>Toate planurile includ profil public activ și recenzii de la clienți.</p>
            <p>Fără taxe ascunse. Poți anula oricând din contul tău.</p>
            @auth
                @if($activeSubscription && $activeSubscription->ends_at)
                    <p class="text-emerald-600 dark:text-emerald-400 font-medium">
                        Abonamentul tău {{ $currentPlan->name }} este activ până pe
                        {{ $activeSubscription->ends_at->format('d.m.Y') }}.
                    </p>
                @endif
            @endauth
        </div>

    </div>
</div>
@endsection
