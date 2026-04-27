@extends('layouts.admin')

@section('title', 'Plăți Afilieri - Meseriași Admin')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li><a href="{{ route('admin.affiliates.index') }}" class="text-gray-500 hover:text-gray-700">Afilieri</a></li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-700">Plăți</span>
                    </li>
                </ol>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900">Plăți afilieri</h1>
            <p class="mt-2 text-gray-600">Gestionează cererile de plată de la afiliați</p>
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

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
            <form method="GET" action="{{ route('admin.affiliates.payouts') }}" class="flex flex-wrap items-center gap-4">
                <div>
                    <select name="status" 
                            onchange="this.form.submit()"
                            class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                        <option value="">Toate statusurile</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>În așteptare</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>În procesare</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Finalizate</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Eșuate</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Payouts Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            @if($payouts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Afiliat
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Metodă
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Detalii plată
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Data cererii
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Sumă
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acțiuni
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($payouts as $payout)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                                <span class="text-gray-500 font-medium">
                                                    {{ strtoupper(substr($payout->affiliate->user->name ?? '', 0, 1)) }}
                                                </span>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $payout->affiliate->user->name ?? 'N/A' }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $payout->affiliate->referral_code }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @switch($payout->payment_method)
                                            @case('iban')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Transfer bancar
                                                </span>
                                                @break
                                            @case('paypal')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    PayPal
                                                </span>
                                                @break
                                            @case('revolut')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                    Revolut
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4">
                                        <code class="text-sm text-gray-600 bg-gray-50 px-2 py-1 rounded">
                                            {{ $payout->payment_details }}
                                        </code>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @switch($payout->status)
                                            @case('pending')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <span class="w-2 h-2 bg-yellow-400 rounded-full mr-1.5 animate-pulse"></span>
                                                    În așteptare
                                                </span>
                                                @break
                                            @case('processing')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <span class="w-2 h-2 bg-blue-400 rounded-full mr-1.5 animate-pulse"></span>
                                                    În procesare
                                                </span>
                                                @break
                                            @case('completed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <span class="w-2 h-2 bg-green-400 rounded-full mr-1.5"></span>
                                                    Finalizat
                                                </span>
                                                @break
                                            @case('failed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <span class="w-2 h-2 bg-red-400 rounded-full mr-1.5"></span>
                                                    Eșuat
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $payout->created_at->format('d.m.Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="text-lg font-semibold text-gray-900">
                                            {{ number_format($payout->amount, 2) }} lei
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($payout->status === 'pending')
                                                <form action="{{ route('admin.affiliates.payout.process', $payout) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="px-3 py-1.5 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors">
                                                        Procesează
                                                    </button>
                                                </form>
                                            @elseif($payout->status === 'processing')
                                                <button type="button" 
                                                        onclick="openCompleteModal({{ $payout->id }})"
                                                        class="px-3 py-1.5 text-sm bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">
                                                    Finalizează
                                                </button>
                                                <button type="button" 
                                                        onclick="openFailModal({{ $payout->id }})"
                                                        class="px-3 py-1.5 text-sm bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                                                    Eșuat
                                                </button>
                                            @elseif($payout->status === 'completed')
                                                <span class="text-green-600 text-sm">
                                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($payouts->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $payouts->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Nicio cerere de plată</h3>
                    <p class="text-gray-500">Nu există plăți în această categorie.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Complete Modal -->
<div id="completeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Finalizează plata</h3>
        <form id="completeForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Referință plată (opțional)</label>
                <input type="text" 
                       name="payment_reference" 
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                       placeholder="ID tranzacție, referință bancară...">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" 
                        onclick="closeCompleteModal()"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    Anulează
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">
                    Confirmă plata
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Fail Modal -->
<div id="failModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Marchează plata ca eșuată</h3>
        <form id="failForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Motivul eșecului</label>
                <textarea name="reason" 
                          rows="3" 
                          class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                          placeholder="IBAN invalid, cont inexistent..."
                          required></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" 
                        onclick="closeFailModal()"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    Anulează
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                    Marchează eșuat
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCompleteModal(payoutId) {
        document.getElementById('completeForm').action = `/admin/affiliates/payouts/${payoutId}/complete`;
        document.getElementById('completeModal').classList.remove('hidden');
    }
    
    function closeCompleteModal() {
        document.getElementById('completeModal').classList.add('hidden');
    }
    
    function openFailModal(payoutId) {
        document.getElementById('failForm').action = `/admin/affiliates/payouts/${payoutId}/fail`;
        document.getElementById('failModal').classList.remove('hidden');
    }
    
    function closeFailModal() {
        document.getElementById('failModal').classList.add('hidden');
    }
</script>
@endsection
