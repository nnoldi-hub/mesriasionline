@extends('layouts.app')

@section('title', 'Dashboard Afiliere - Fixacasa')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Afiliere</h1>
            <p class="text-gray-600">Bun venit, {{ auth()->user()->name }}!</p>
        </div>
        <div class="flex items-center gap-4">
            @if($affiliate->status === 'pending')
                <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg font-medium">
                    ⏳ În așteptare aprobare
                </span>
            @elseif($affiliate->status === 'active')
                <span class="px-4 py-2 bg-green-100 text-green-800 rounded-lg font-medium">
                    ✓ Cont activ
                </span>
            @elseif($affiliate->status === 'suspended')
                <span class="px-4 py-2 bg-red-100 text-red-800 rounded-lg font-medium">
                    ⚠ Cont suspendat
                </span>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 mb-6">
            {{ session('error') }}
        </div>
    @endif

    @if($affiliate->status === 'active')
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Câștiguri</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($statistics['earnings']['total'], 2) }} lei</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Disponibil</p>
                        <p class="text-2xl font-bold text-primary-600">{{ number_format($statistics['earnings']['pending'], 2) }} lei</p>
                    </div>
                    <div class="p-3 bg-primary-100 rounded-lg">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Click-uri</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_clicks'] }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Conversii</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['conversions'] }}</p>
                        <p class="text-xs text-gray-400">{{ $statistics['conversion_rate'] }}% rată</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Referral Link -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Link-ul Tău de Referral</h2>
            <div class="flex items-center gap-4">
                <input type="text" readonly value="{{ $affiliate->referral_url }}" id="referral-link"
                    class="flex-1 bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-700">
                <button onclick="copyLink()" class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Copiază
                </button>
            </div>
            <p class="text-sm text-gray-500 mt-2">Codul tău de referral: <strong>{{ $affiliate->referral_code }}</strong></p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Referrals -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Referral-uri Recente</h2>
                    <a href="{{ route('affiliate.referrals') }}" class="text-sm text-primary-600 hover:underline">Vezi toate</a>
                </div>
                @if($recentReferrals->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentReferrals as $referral)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ $referral->referredUser?->name ?? 'Vizitator' }}
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $referral->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    @if($referral->status === 'converted') bg-green-100 text-green-700
                                    @elseif($referral->status === 'registered') bg-blue-100 text-blue-700
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ ucfirst($referral->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">Niciun referral încă. Distribuie link-ul tău!</p>
                @endif
            </div>

            <!-- Recent Commissions -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Comisioane Recente</h2>
                    <a href="{{ route('affiliate.earnings') }}" class="text-sm text-primary-600 hover:underline">Vezi toate</a>
                </div>
                @if($recentCommissions->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentCommissions as $commission)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">
                                        +{{ number_format($commission->commission_amount, 2) }} lei
                                    </p>
                                    <p class="text-sm text-gray-500">{{ ucfirst($commission->transaction_type) }}</p>
                                </div>
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    @if($commission->status === 'paid') bg-green-100 text-green-700
                                    @elseif($commission->status === 'approved') bg-blue-100 text-blue-700
                                    @elseif($commission->status === 'pending') bg-yellow-100 text-yellow-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ ucfirst($commission->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">Niciun comision încă.</p>
                @endif
            </div>
        </div>

        <!-- Request Payout -->
        @if($affiliate->canRequestPayout())
            <div class="mt-8 bg-green-50 border border-green-200 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-green-900">Poți solicita plata!</h3>
                        <p class="text-green-700">Ai {{ number_format($affiliate->pending_earnings, 2) }} lei disponibili pentru retragere.</p>
                    </div>
                    <form action="{{ route('affiliate.request-payout') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                            Solicită Plata
                        </button>
                    </form>
                </div>
            </div>
        @endif
    @else
        <!-- Pending/Suspended State -->
        <div class="bg-white rounded-xl shadow-sm p-8 text-center">
            @if($affiliate->status === 'pending')
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Cererea ta este în așteptare</h2>
                <p class="text-gray-600">Vom revizui cererea ta și te vom notifica prin email în curând.</p>
            @elseif($affiliate->status === 'suspended')
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Contul tău este suspendat</h2>
                <p class="text-gray-600">{{ $affiliate->notes ?? 'Contactează suportul pentru mai multe informații.' }}</p>
            @elseif($affiliate->status === 'rejected')
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Cererea ta a fost respinsă</h2>
                <p class="text-gray-600">{{ $affiliate->notes ?? 'Contactează suportul pentru mai multe informații.' }}</p>
            @endif
        </div>
    @endif
</div>

<script>
function copyLink() {
    const input = document.getElementById('referral-link');
    input.select();
    document.execCommand('copy');
    
    // Show feedback
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Copiat!';
    setTimeout(() => {
        button.innerHTML = originalText;
    }, 2000);
}
</script>
@endsection
