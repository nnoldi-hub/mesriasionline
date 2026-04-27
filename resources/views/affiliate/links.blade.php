@extends('layouts.app')

@section('title', 'Link-urile mele de afiliere - Meseriași')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li><a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700">Acasă</a></li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <a href="{{ route('affiliate.dashboard') }}" class="text-gray-500 hover:text-gray-700">Program Afiliere</a>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-700">Link-uri</span>
                    </li>
                </ol>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900">Link-urile mele de afiliere</h1>
            <p class="mt-2 text-gray-600">Creează și gestionează link-urile tale de referral pentru diferite pagini</p>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-green-700">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Main Referral Link -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Link-ul tău principal</h2>
                    <p class="text-sm text-gray-600 mb-4">
                        Folosește acest link pentru a invita utilizatori noi pe platformă. 
                        Codul tău de referral: <span class="font-mono font-bold text-yellow-600">{{ $affiliate->referral_code }}</span>
                    </p>
                    
                    <div class="flex items-center gap-2">
                        <input type="text" 
                               id="main-referral-link"
                               value="{{ $affiliate->referral_url }}" 
                               class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm font-mono"
                               readonly>
                        <button onclick="copyToClipboard('main-referral-link')" 
                                class="px-4 py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Custom Links Generator -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Generator link-uri personalizate</h2>
                    <p class="text-sm text-gray-600 mb-4">
                        Creează link-uri pentru pagini specifice ale platformei
                    </p>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Selectează pagina</label>
                            <select id="page-selector" onchange="generateCustomLink()" 
                                    class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                                <option value="">Pagina principală</option>
                                <option value="/meseriasi">Lista meșterilor</option>
                                <option value="/categorii">Categorii servicii</option>
                                <option value="/articole">Blog & Articole</option>
                                <option value="/inregistrare/meserias">Înregistrare meșter</option>
                                <option value="/inregistrare">Înregistrare client</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Link generat</label>
                            <div class="flex items-center gap-2">
                                <input type="text" 
                                       id="custom-referral-link"
                                       value="{{ $affiliate->referral_url }}" 
                                       class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm font-mono"
                                       readonly>
                                <button onclick="copyToClipboard('custom-referral-link')" 
                                        class="px-4 py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pre-made Links -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Link-uri rapide</h2>
                    
                    <div class="space-y-3">
                        @php
                            $quickLinks = [
                                ['name' => 'Pagina principală', 'path' => ''],
                                ['name' => 'Lista meșterilor', 'path' => '/meseriasi'],
                                ['name' => 'Înregistrare meșter', 'path' => '/inregistrare/meserias'],
                                ['name' => 'Înregistrare client', 'path' => '/inregistrare'],
                                ['name' => 'Categorii servicii', 'path' => '/categorii'],
                            ];
                        @endphp
                        
                        @foreach($quickLinks as $index => $link)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <span class="font-medium text-gray-900">{{ $link['name'] }}</span>
                                    <span class="text-sm text-gray-500 ml-2">{{ $link['path'] ?: '/' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="text" 
                                           id="quick-link-{{ $index }}"
                                           value="{{ url($link['path']) }}?ref={{ $affiliate->referral_code }}" 
                                           class="w-64 px-3 py-2 text-xs font-mono bg-white border border-gray-200 rounded"
                                           readonly>
                                    <button onclick="copyToClipboard('quick-link-{{ $index }}')" 
                                            class="p-2 text-gray-500 hover:text-yellow-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Stats Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistici link-uri</h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total click-uri</span>
                            <span class="text-2xl font-bold text-gray-900">{{ $affiliate->total_clicks }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Referrali</span>
                            <span class="text-2xl font-bold text-gray-900">{{ $affiliate->total_referrals }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Conversii</span>
                            <span class="text-2xl font-bold text-green-600">{{ $affiliate->converted_referrals }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Rată conversie</span>
                            <span class="text-lg font-bold text-blue-600">{{ $affiliate->conversion_rate }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Share Buttons -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribuie rapid</h3>
                    
                    <div class="space-y-3">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($affiliate->referral_url) }}" 
                           target="_blank"
                           class="flex items-center w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            Distribuie pe Facebook
                        </a>
                        
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode($affiliate->referral_url) }}&text={{ urlencode('Găsește meseriași de încredere pe Meseriași!') }}" 
                           target="_blank"
                           class="flex items-center w-full px-4 py-3 bg-sky-500 hover:bg-sky-600 text-white rounded-lg transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                            Distribuie pe Twitter
                        </a>
                        
                        <a href="https://wa.me/?text={{ urlencode('Găsește meseriași de încredere pe ' . $affiliate->referral_url) }}" 
                           target="_blank"
                           class="flex items-center w-full px-4 py-3 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Trimite pe WhatsApp
                        </a>
                        
                        <a href="mailto:?subject={{ urlencode('Recomandare: Meseriași') }}&body={{ urlencode('Salut! Ți-l recomand pe Meseriași pentru servicii de calitate: ' . $affiliate->referral_url) }}" 
                           class="flex items-center w-full px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Trimite prin Email
                        </a>
                    </div>
                </div>

                <!-- Tips -->
                <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl border border-yellow-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">💡 Sfaturi</h3>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li class="flex items-start">
                            <span class="text-yellow-500 mr-2">•</span>
                            Distribuie link-urile pe rețelele sociale
                        </li>
                        <li class="flex items-start">
                            <span class="text-yellow-500 mr-2">•</span>
                            Folosește link-uri specifice pentru audiențe diferite
                        </li>
                        <li class="flex items-start">
                            <span class="text-yellow-500 mr-2">•</span>
                            Recomandă platforma prietenilor și familiei
                        </li>
                        <li class="flex items-start">
                            <span class="text-yellow-500 mr-2">•</span>
                            Adaugă link-ul în semnătura de email
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const referralCode = '{{ $affiliate->referral_code }}';
    const baseUrl = '{{ url('/') }}';
    
    function generateCustomLink() {
        const page = document.getElementById('page-selector').value;
        const customLink = baseUrl + page + '?ref=' + referralCode;
        document.getElementById('custom-referral-link').value = customLink;
    }
    
    function copyToClipboard(elementId) {
        const input = document.getElementById(elementId);
        input.select();
        input.setSelectionRange(0, 99999);
        
        navigator.clipboard.writeText(input.value).then(() => {
            // Show success feedback
            const button = input.nextElementSibling;
            const originalHTML = button.innerHTML;
            button.innerHTML = '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
            button.classList.add('bg-green-100');
            
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.classList.remove('bg-green-100');
            }, 2000);
        });
    }
</script>
@endsection
