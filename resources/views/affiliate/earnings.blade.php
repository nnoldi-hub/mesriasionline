@extends('layouts.app')

@section('title', 'Câștigurile mele - Meseriași')

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
                        <span class="text-gray-700">Câștiguri</span>
                    </li>
                </ol>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900">Câștigurile mele</h1>
            <p class="mt-2 text-gray-600">Vezi istoricul comisioanelor și câștigurilor tale</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total câștigat</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($affiliate->total_earnings, 2) }} lei</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Disponibil pentru retragere</p>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($affiliate->available_balance, 2) }} lei</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">În așteptare</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ number_format($affiliate->pending_balance, 2) }} lei</p>
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
                        <p class="text-sm text-gray-500">Total retras</p>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($affiliate->paid_balance, 2) }} lei</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Commissions Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Istoricul comisioanelor</h2>
                        
                        <form method="GET" action="{{ route('affiliate.earnings') }}" class="flex items-center gap-2">
                            <select name="status" 
                                    onchange="this.form.submit()"
                                    class="text-sm px-3 py-1.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-yellow-500">
                                <option value="">Toate</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>În așteptare</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprobate</option>
                                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Plătite</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Respinse</option>
                            </select>
                        </form>
                    </div>
                    
                    @if($commissions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Data
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tip
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Descriere
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
                                    @foreach($commissions as $commission)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $commission->created_at->format('d.m.Y') }}
                                                <br>
                                                <span class="text-xs text-gray-400">{{ $commission->created_at->format('H:i') }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @switch($commission->commission_type)
                                                    @case('registration')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            Înregistrare
                                                        </span>
                                                        @break
                                                    @case('subscription')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                            Abonament
                                                        </span>
                                                        @break
                                                    @case('order')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            Comandă
                                                        </span>
                                                        @break
                                                    @default
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            {{ ucfirst($commission->commission_type) }}
                                                        </span>
                                                @endswitch
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">
                                                    {{ $commission->description ?? 'Comision afiliere' }}
                                                </div>
                                                @if($commission->referral)
                                                    <div class="text-xs text-gray-500">
                                                        Referral: {{ $commission->referral->referredUser->name ?? 'N/A' }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @switch($commission->status)
                                                    @case('pending')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            <span class="w-2 h-2 bg-yellow-400 rounded-full mr-1.5"></span>
                                                            În așteptare
                                                        </span>
                                                        @break
                                                    @case('approved')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <span class="w-2 h-2 bg-green-400 rounded-full mr-1.5"></span>
                                                            Aprobat
                                                        </span>
                                                        @break
                                                    @case('paid')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            <span class="w-2 h-2 bg-blue-400 rounded-full mr-1.5"></span>
                                                            Plătit
                                                        </span>
                                                        @break
                                                    @case('rejected')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            <span class="w-2 h-2 bg-red-400 rounded-full mr-1.5"></span>
                                                            Respins
                                                        </span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <span class="text-lg font-semibold {{ $commission->status === 'rejected' ? 'text-gray-400 line-through' : 'text-green-600' }}">
                                                    +{{ number_format($commission->amount, 2) }} lei
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($commissions->hasPages())
                            <div class="px-6 py-4 border-t border-gray-200">
                                {{ $commissions->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Niciun comision încă</h3>
                            <p class="text-gray-500 mb-4">Câștigurile vor apărea aici când referralii tăi vor fi convertiți</p>
                            <a href="{{ route('affiliate.links') }}" 
                               class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                                Obține link-uri de afiliere
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Request Payout Card -->
                @if($affiliate->canRequestPayout())
                    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white">
                        <h3 class="text-lg font-semibold mb-2">Solicită plata</h3>
                        <p class="text-green-100 text-sm mb-4">
                            Ai {{ number_format($affiliate->available_balance, 2) }} lei disponibili pentru retragere.
                        </p>
                        <a href="{{ route('affiliate.payouts') }}" 
                           class="block w-full text-center px-4 py-3 bg-white text-green-600 font-semibold rounded-lg hover:bg-green-50 transition-colors">
                            Solicită plata acum
                        </a>
                    </div>
                @else
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Solicită plata</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Minimum pentru retragere: <strong>{{ number_format($affiliate->program->min_payout ?? 100, 2) }} lei</strong>
                        </p>
                        
                        <div class="relative pt-1">
                            @php
                                $progress = min(100, ($affiliate->available_balance / ($affiliate->program->min_payout ?? 100)) * 100);
                            @endphp
                            <div class="flex mb-2 items-center justify-between">
                                <div>
                                    <span class="text-xs font-semibold inline-block text-yellow-600">
                                        {{ number_format($progress, 0) }}%
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-semibold inline-block text-gray-600">
                                        {{ number_format($affiliate->available_balance, 2) }} / {{ number_format($affiliate->program->min_payout ?? 100, 2) }} lei
                                    </span>
                                </div>
                            </div>
                            <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                                <div style="width: {{ $progress }}%" 
                                     class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-yellow-500"></div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Commission Rates -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Rate comisioane</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-700">Înregistrare meșter</span>
                            </div>
                            <span class="font-semibold text-green-600">
                                @if($affiliate->program->commission_type === 'percentage')
                                    {{ $affiliate->program->commission_rate }}%
                                @else
                                    {{ number_format($affiliate->program->fixed_amount, 2) }} lei
                                @endif
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-700">Abonament meșter</span>
                            </div>
                            <span class="font-semibold text-green-600">
                                {{ $affiliate->program->commission_rate }}%
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Monthly Summary -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Sumar luna curentă</h3>
                    
                    @php
                        $monthlyEarnings = $commissions->where('created_at', '>=', now()->startOfMonth())->sum('amount');
                        $monthlyConversions = $affiliate->referrals()->where('converted_at', '>=', now()->startOfMonth())->count();
                    @endphp
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Câștiguri</span>
                            <span class="font-semibold text-gray-900">{{ number_format($monthlyEarnings, 2) }} lei</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Conversii</span>
                            <span class="font-semibold text-gray-900">{{ $monthlyConversions }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Click-uri</span>
                            <span class="font-semibold text-gray-900">{{ $affiliate->total_clicks }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
