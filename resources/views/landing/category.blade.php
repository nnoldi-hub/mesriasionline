@extends('layouts.app')

@section('title', $meta['title'])
@section('meta_description', $meta['description'])
@section('meta_keywords', $meta['keywords'])

@push('head')
<link rel="canonical" href="{{ $meta['canonical'] }}">
<meta name="robots" content="index, follow">
@endpush

@section('content')

{{-- Hero --}}
<section class="bg-gradient-to-br from-primary-700 to-primary-900 text-white py-16 px-4">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center gap-2 text-primary-300 text-sm mb-4">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">Acasă</a>
            <span>/</span>
            <span class="text-white">{{ $category->name }}</span>
        </div>
        <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight mb-4">
            {{ $category->name }} profesioniști<br>
            <span class="text-primary-300">în toată România</span>
        </h1>
        <p class="text-lg text-primary-200 mb-8 max-w-2xl">
            {{ $category->description }}
        </p>

        {{-- Quick search by city --}}
        <form method="GET" action="{{ route('home') }}" class="flex flex-col sm:flex-row gap-3 max-w-xl">
            <input type="hidden" name="category_id" value="{{ $category->id }}">
            <select name="location_id"
                class="flex-1 px-4 py-3 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-300">
                <option value="">Toate orașele</option>
                @foreach($topLocations as $loc)
                    <option value="{{ $loc->id }}">{{ $loc->city }} ({{ $loc->craftsmen_count }})</option>
                @endforeach
            </select>
            <button type="submit"
                class="bg-white text-primary-700 font-bold px-6 py-3 rounded-xl hover:bg-primary-50 transition-colors shadow">
                Caută
            </button>
        </form>
    </div>
</section>

{{-- Stats bar --}}
<div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-5xl mx-auto px-4 py-5 grid grid-cols-3 gap-6 text-center">
        <div>
            <p class="text-2xl font-extrabold text-primary-600">{{ $craftsmen->count() }}+</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $category->name }} activi</p>
        </div>
        <div>
            <p class="text-2xl font-extrabold text-primary-600">{{ $craftsmen->where('is_verified', true)->count() }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Verificați</p>
        </div>
        <div>
            <p class="text-2xl font-extrabold text-primary-600">{{ $topLocations->count() }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Orașe</p>
        </div>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 py-12 grid grid-cols-1 lg:grid-cols-3 gap-10">

    {{-- Main: craftsmen list --}}
    <div class="lg:col-span-2 space-y-6">

        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
            {{ $category->name }} cu recenzii bune
        </h2>

        @forelse($craftsmen as $craftsman)
            @include('partials.craftsman-card', ['craftsman' => $craftsman])
        @empty
            <div class="text-center py-12 text-gray-500">
                <p class="text-lg mb-4">Nu am găsit meseriași în această categorie momentan.</p>
                <a href="{{ route('onboarding.quick-register') }}" class="text-primary-600 font-semibold hover:underline">
                    Ești meseriaș? Înregistrează-te gratuit →
                </a>
            </div>
        @endforelse

        {{-- Service templates --}}
        @if(!empty($serviceTemplates))
            <div class="mt-10">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                    Servicii {{ $category->name }} — Prețuri orientative
                </h2>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    @foreach($serviceTemplates as $tpl)
                        <div class="flex items-center justify-between px-5 py-3.5 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $tpl['name'] }}</span>
                            <span class="text-sm font-semibold text-primary-600 dark:text-primary-400">
                                de la {{ $tpl['price'] }} RON / {{ $tpl['unit'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-400 mt-2">* Prețurile sunt orientative și pot varia în funcție de complexitate și regiune.</p>
            </div>
        @endif

        {{-- FAQ --}}
        <div class="mt-10">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Întrebări frecvente</h2>
            <div class="space-y-3" x-data="{open: null}">
                @foreach($faqItems as $i => $faq)
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <button @click="open === {{ $i }} ? open = null : open = {{ $i }}"
                            class="w-full flex items-center justify-between px-5 py-4 text-left">
                            <span class="font-semibold text-gray-900 dark:text-white text-sm">{{ $faq['question'] }}</span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open === {{ $i }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open === {{ $i }}" x-cloak class="px-5 pb-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ $faq['answer'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">

        {{-- Cities --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="font-bold text-gray-900 dark:text-white mb-3 text-sm">{{ $category->name }} pe orașe</h3>
            <ul class="space-y-1">
                @foreach($topLocations as $loc)
                    <li>
                        <a href="{{ route('landing.category-city', [$category->slug, $loc->slug]) }}"
                           class="flex items-center justify-between py-1.5 text-sm text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                            {{ $loc->city }}
                            <span class="text-xs text-gray-400">{{ $loc->craftsmen_count }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- CTA Meseriaș --}}
        <div class="bg-gradient-to-br from-primary-600 to-primary-800 rounded-xl p-5 text-white">
            <p class="font-bold text-base mb-1">Ești {{ $category->name }}?</p>
            <p class="text-primary-200 text-sm mb-4">Înregistrează-te gratuit și primești clienți din zona ta.</p>
            <a href="{{ route('onboarding.quick-register') }}"
               class="block text-center bg-white text-primary-700 font-semibold text-sm py-2.5 px-4 rounded-lg hover:bg-primary-50 transition-colors">
                Începe gratuit →
            </a>
        </div>

        {{-- Other categories --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="font-bold text-gray-900 dark:text-white mb-3 text-sm">Alte meserii</h3>
            @php
                $otherCategories = \App\Models\Category::where('is_active', true)
                    ->where('id', '!=', $category->id)
                    ->orderBy('order')->limit(6)->get();
            @endphp
            <ul class="space-y-1">
                @foreach($otherCategories as $cat)
                    <li>
                        <a href="{{ route('landing.category', $cat->slug) }}"
                           class="text-sm text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                            → {{ $cat->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

    </div>
</div>
@endsection
