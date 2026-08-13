@extends('layouts.app')

@section('title', 'Plățile mele - Meseriași')

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
                        <span class="text-gray-700">Plăți</span>
                    </li>
                </ol>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900">Plățile mele</h1>
            <p class="mt-2 text-gray-600">Solicită retrageri și vezi istoricul plăților</p>
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

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-red-700">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Disponibil pentru retragere</p>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($affiliate->available_balance, 2) }} lei</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">În procesare</p>
                        <p class="text-2xl font-bold text-yellow-600">
                            {{ number_format($payouts->where('status', 'pending')->sum('amount') + $payouts->where('status', 'processing')->sum('amount'), 2) }} lei
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total primit</p>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($affiliate->paid_balance, 2) }} lei</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Request Payout Form -->
                @if($affiliate->canRequestPayout())
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white">
                        <h2 class="text-xl font-semibold mb-4">Solicită plata</h2>
                        
                        <form action="{{ route('affiliate.request-payout') }}" method="POST" class="space-y-4">
                            @csrf
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-green-100 mb-2">Suma de retras</label>
                                    <div class="relative">
                                        <input type="number" 
                                               name="amount" 
                                               min="{{ $affiliate->program->min_payout ?? 100 }}"
                                               max="{{ $affiliate->available_balance }}"
                                               step="0.01"
                                               value="{{ $affiliate->available_balance }}"
                                               class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-green-200 focus:ring-2 focus:ring-white focus:border-transparent"
                                               required>
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-green-200">lei</span>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-green-100 mb-2">Metodă de plată</label>
                                    <select name="payment_method" 
                                            class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white focus:ring-2 focus:ring-white focus:border-transparent"
                                            required>
                                        <option value="iban" class="text-gray-900">Transfer bancar (IBAN)</option>
                                        <option value="paypal" class="text-gray-900">PayPal</option>
                                        <option value="revolut" class="text-gray-900">Revolut</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-green-100 mb-2">Detalii plată</label>
                                <input type="text" 
                                       name="payment_details" 
                                       placeholder="IBAN, email PayPal sau tag Revolut"
                                       value="{{ $affiliate->payment_details }}"
                                       class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-green-200 focus:ring-2 focus:ring-white focus:border-transparent"
                                       required>
                            </div>
                            
                            <button type="submit" 
                                    class="w-full px-6 py-3 bg-white text-green-600 font-semibold rounded-lg hover:bg-green-50 transition-colors">
                                Solicită retragerea
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Payouts History -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Istoricul plăților</h2>
                    </div>
                    
                    @if($payouts->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Data
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Metodă
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Sumă
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($payouts as $payout)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $payout->created_at->format('d.m.Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $payout->created_at->format('H:i') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    @switch($payout->payment_method)
                                                        @case('iban')
                                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <div class="text-sm font-medium text-gray-900">Transfer bancar</div>
                                                                <div class="text-xs text-gray-500">{{ Str::mask($payout->payment_details ?? '', '*', 4, -4) }}</div>
                                                            </div>
                                                            @break
                                                        @case('paypal')
                                                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center mr-2">
                                                                <span class="text-indigo-600 font-bold text-xs">PP</span>
                                                            </div>
                                                            <div>
                                                                <div class="text-sm font-medium text-gray-900">PayPal</div>
                                                                <div class="text-xs text-gray-500">{{ Str::mask($payout->payment_details ?? '', '*', 3, 5) }}</div>
                                                            </div>
                                                            @break
                                                        @case('revolut')
                                                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-2">
                                                                <span class="text-purple-600 font-bold text-xs">R</span>
                                                            </div>
                                                            <div>
                                                                <div class="text-sm font-medium text-gray-900">Revolut</div>
                                                                <div class="text-xs text-gray-500">{{ $payout->payment_details }}</div>
                                                            </div>
                                                            @break
                                                    @endswitch
                                                </div>
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
                                                        @if($payout->paid_at)
                                                            <div class="text-xs text-gray-500 mt-1">
                                                                {{ $payout->paid_at->format('d.m.Y') }}
                                                            </div>
                                                        @endif
                                                        @break
                                                    @case('rejected')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            <span class="w-2 h-2 bg-red-400 rounded-full mr-1.5"></span>
                                                            Respins
                                                        </span>
                                                        @if($payout->notes)
                                                            <div class="text-xs text-red-600 mt-1">
                                                                {{ $payout->notes }}
                                                            </div>
                                                        @endif
                                                        @break
                                                @endswitch
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <span class="text-lg font-semibold {{ $payout->status === 'completed' ? 'text-green-600' : 'text-gray-900' }}">
                                                    {{ number_format($payout->amount, 2) }} lei
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($payouts->hasPages())
                            <div class="px-6 py-4 border-t border-gray-200">
                                {{ $payouts->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Nicio plată încă</h3>
                            <p class="text-gray-500">Plățile vor apărea aici când vei solicita retrageri</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Payment Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informații plăți</h3>
                    
                    <div class="space-y-4 text-sm">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-500 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <span class="font-medium text-gray-900">Suma minimă</span>
                                <p class="text-gray-600">{{ number_format($affiliate->program->min_payout ?? 100, 2) }} lei</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-500 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <span class="font-medium text-gray-900">Timp procesare</span>
                                <p class="text-gray-600">3-5 zile lucrătoare</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-500 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <span class="font-medium text-gray-900">Fără comisioane</span>
                                <p class="text-gray-600">Nu percepem taxe pentru retrageri</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Metode de plată acceptate</h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">Transfer bancar</span>
                                <p class="text-xs text-gray-500">IBAN România</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-indigo-600 font-bold text-sm">PP</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">PayPal</span>
                                <p class="text-xs text-gray-500">Cont verificat</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-purple-600 font-bold text-sm">R</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">Revolut</span>
                                <p class="text-xs text-gray-500">Tag sau număr telefon</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Need Help -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Ai nevoie de ajutor?</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Dacă ai întrebări despre plăți sau comisioane, contactează-ne.
                    </p>
                    <a href="mailto:{{ \App\Models\PlatformSetting::getValue('contact_email', 'contact@meseriasionline.ro') }}"
                       class="inline-flex items-center text-sm font-medium text-yellow-600 hover:text-yellow-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ \App\Models\PlatformSetting::getValue('contact_email', 'contact@meseriasionline.ro') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
