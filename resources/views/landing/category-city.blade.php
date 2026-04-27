@extends('layouts.app')

@section('title', $meta['title'])
@section('meta_description', $meta['description'])
@section('meta_keywords', $meta['keywords'])

@push('head')
<link rel="canonical" href="{{ $meta['canonical'] }}">
<meta name="robots" content="index, follow">
<script type="application/ld+json">{{ json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) }}</script>
@endpush

@section('content')

{{-- Hero --}}
<section class="bg-gradient-to-br from-primary-700 to-primary-900 text-white py-16 px-4">
    <div class="max-w-5xl mx-auto">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-primary-300 text-sm mb-4 flex-wrap">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">Acasă</a>
            <span>/</span>
            <a href="{{ route('landing.category', $category->slug) }}" class="hover:text-white transition-colors">{{ $category->name }}</a>
            <span>/</span>
            <span class="text-white">{{ $cityName }}</span>
        </div>

        <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight mb-4">
            {{ $category->name }} în <span class="text-primary-300">{{ $cityName }}</span>
        </h1>
        <p class="text-lg text-primary-200 mb-6 max-w-2xl">
            @if($craftsmen->count() > 0)
                {{ $craftsmen->count() }} specialiști verificați disponibili în {{ $cityName }}.
                Recenzii reale, prețuri transparente.
            @else
                Solicită o ofertă și conectăm-te cu un specialist verificat din {{ $cityName }}.
            @endif
        </p>

        {{-- CTA --}}
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('home') }}?category_id={{ $category->id }}&location_id={{ $location->id }}"
               class="inline-block bg-white text-primary-700 font-bold px-6 py-3 rounded-xl hover:bg-primary-50 transition-colors shadow">
                Vezi toți {{ $category->name }}i →
            </a>
            <a href="{{ route('onboarding.quick-register') }}"
               class="inline-block border border-primary-400 text-primary-100 font-semibold px-6 py-3 rounded-xl hover:bg-primary-600 transition-colors">
                Ești meseriaș? Înregistrează-te
            </a>
        </div>
    </div>
</section>

{{-- Stats bar --}}
<div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-5xl mx-auto px-4 py-5 grid grid-cols-3 gap-6 text-center">
        <div>
            <p class="text-2xl font-extrabold text-primary-600">{{ $craftsmen->count() }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Specialiști în {{ $cityName }}</p>
        </div>
        <div>
            <p class="text-2xl font-extrabold text-primary-600">{{ $craftsmen->where('is_verified', true)->count() }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Verificați</p>
        </div>
        <div>
            <p class="text-2xl font-extrabold text-primary-600">
                {{ $craftsmen->filter(fn($c) => $c->reviews_count > 0)->avg('reviews_avg_rating')
                    ? number_format($craftsmen->filter(fn($c) => $c->reviews_count > 0)->avg('reviews_avg_rating'), 1)
                    : 'N/A' }}
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Rating mediu</p>
        </div>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 py-12 grid grid-cols-1 lg:grid-cols-3 gap-10">

    {{-- Main column --}}
    <div class="lg:col-span-2 space-y-6">

        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
            {{ $category->name }} din {{ $cityName }}
        </h2>

        @forelse($craftsmen as $craftsman)
            @include('partials.craftsman-card', ['craftsman' => $craftsman])
        @empty
            <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="text-4xl mb-4">🔍</div>
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Nu am găsit {{ $category->name }} în {{ $cityName }} momentan.
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-500 mb-6">
                    Dar poți solicita o cerere de ofertă și te contactăm când găsim unul disponibil.
                </p>
                <a href="{{ route('home') }}" class="inline-block bg-primary-600 hover:bg-primary-700 text-white font-semibold px-6 py-3 rounded-xl transition-colors">
                    Caută în toate orașele
                </a>
            </div>
        @endforelse

        {{-- Service templates --}}
        @if(!empty($serviceTemplates))
            <div class="mt-10">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                    Cât costă un {{ $category->name }} în {{ $cityName }}?
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Prețuri orientative pentru serviciile cele mai solicitate:
                </p>
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
                <p class="text-xs text-gray-400 mt-2">* Prețurile pot varia în funcție de complexitate și disponibilitate.</p>
            </div>
        @endif

        {{-- FAQ rich snippet --}}
        <div class="mt-10">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                Întrebări despre {{ $category->name }} în {{ $cityName }}
            </h2>
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

        {{-- Request quote CTA --}}
        <div class="bg-gradient-to-br from-primary-600 to-primary-800 text-white rounded-xl p-5">
            <p class="font-bold text-base mb-1">Ai nevoie urgent?</p>
            <p class="text-primary-200 text-sm mb-4">Solicită ofertă gratuită de la {{ $category->name }}i din {{ $cityName }}.</p>
            <a href="{{ route('home') }}?category_id={{ $category->id }}&location_id={{ $location->id }}"
               class="block text-center bg-white text-primary-700 font-semibold text-sm py-2.5 px-4 rounded-lg hover:bg-primary-50 transition-colors">
                Solicită ofertă gratuită →
            </a>
        </div>

        {{-- Other cities --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="font-bold text-gray-900 dark:text-white mb-3 text-sm">{{ $category->name }} în alte orașe</h3>
            @php
                $otherCities = \App\Models\Location::where('is_active', true)
                    ->where('id', '!=', $location->id)
                    ->whereHas('users', fn($q) => $q->where('role','specialist')
                        ->where('is_active',true)->where('category_id',$category->id))
                    ->limit(6)->get();
            @endphp
            <ul class="space-y-1">
                @forelse($otherCities as $city)
                    <li>
                        <a href="{{ route('landing.category-city', [$category->slug, $city->slug]) }}"
                           class="text-sm text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                            → {{ $city->city }}
                        </a>
                    </li>
                @empty
                    <li class="text-sm text-gray-400">Niciun alt oraș disponibil</li>
                @endforelse
            </ul>
        </div>

        {{-- Other categories in this city --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="font-bold text-gray-900 dark:text-white mb-3 text-sm">Alte meserii în {{ $cityName }}</h3>
            @php
                $otherCats = \App\Models\Category::where('is_active', true)
                    ->where('id', '!=', $category->id)
                    ->whereHas('craftsmen', fn($q) => $q->where('is_active',true)->where('location_id',$location->id))
                    ->limit(6)->get();
            @endphp
            <ul class="space-y-1">
                @forelse($otherCats as $cat)
                    <li>
                        <a href="{{ route('landing.category-city', [$cat->slug, $location->slug]) }}"
                           class="text-sm text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                            → {{ $cat->name }}
                        </a>
                    </li>
                @empty
                    <li class="text-sm text-gray-400">Nicio altă categorie</li>
                @endforelse
            </ul>
        </div>

    </div>
</div>

@endsection
