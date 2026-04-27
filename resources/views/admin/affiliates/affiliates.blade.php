@extends('layouts.admin')

@section('title', 'Lista Afiliați - Meseriași Admin')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li><a href="{{ route('admin.affiliates.index') }}" class="text-gray-500 hover:text-gray-700">Afilieri</a></li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Toți afiliații</span>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-bold text-gray-900">Toți afiliații</h1>
            </div>
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
            <form method="GET" action="{{ route('admin.affiliates.list') }}" class="flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Caută după nume, email sau cod..."
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                </div>
                
                <div>
                    <select name="status" 
                            class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                        <option value="">Toate statusurile</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>În așteptare</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activ</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspendat</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Respins</option>
                    </select>
                </div>
                
                <button type="submit" 
                        class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors">
                    Filtrează
                </button>
                
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.affiliates.list') }}" 
                       class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                        Resetează
                    </a>
                @endif
            </form>
        </div>

        <!-- Affiliates Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            @if($affiliates->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Afiliat
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cod Referral
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Program
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statistici
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Câștiguri
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acțiuni
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($affiliates as $affiliate)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                                <span class="text-gray-500 font-medium">
                                                    {{ strtoupper(substr($affiliate->user->name ?? '', 0, 1)) }}
                                                </span>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $affiliate->user->name ?? 'N/A' }}
                                                </p>
                                                <p class="text-xs text-gray-500">{{ $affiliate->user->email ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <code class="px-2 py-1 bg-gray-100 rounded text-sm font-mono">
                                            {{ $affiliate->referral_code }}
                                        </code>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $affiliate->program->name ?? 'Standard' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @switch($affiliate->status)
                                            @case('pending')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <span class="w-2 h-2 bg-yellow-400 rounded-full mr-1.5"></span>
                                                    În așteptare
                                                </span>
                                                @break
                                            @case('active')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <span class="w-2 h-2 bg-green-400 rounded-full mr-1.5"></span>
                                                    Activ
                                                </span>
                                                @break
                                            @case('suspended')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    <span class="w-2 h-2 bg-orange-400 rounded-full mr-1.5"></span>
                                                    Suspendat
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
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm">
                                            <span class="text-gray-900">{{ $affiliate->total_referrals }}</span>
                                            <span class="text-gray-400"> referrali</span>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $affiliate->converted_referrals }} conversii ({{ $affiliate->conversion_rate }}%)
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-lg font-semibold text-green-600">
                                            {{ number_format($affiliate->total_earnings, 2) }} lei
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ number_format($affiliate->pending_balance, 2) }} lei în așteptare
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.affiliates.show', $affiliate) }}" 
                                               class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                                               title="Detalii">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            
                                            @if($affiliate->status === 'pending')
                                                <form action="{{ route('admin.affiliates.approve', $affiliate) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                                            title="Aprobă">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.affiliates.reject', $affiliate) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                            title="Respinge">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @elseif($affiliate->status === 'active')
                                                <button type="button" 
                                                        onclick="openSuspendModal({{ $affiliate->id }})"
                                                        class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors"
                                                        title="Suspendă">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($affiliates->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $affiliates->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Niciun afiliat găsit</h3>
                    <p class="text-gray-500">Nu există afiliați care să corespundă criteriilor de căutare.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Suspend Modal -->
<div id="suspendModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Suspendă afiliatul</h3>
        <form id="suspendForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Motivul suspendării</label>
                <textarea name="reason" 
                          rows="3" 
                          class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                          placeholder="Specifică motivul suspendării..."
                          required></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" 
                        onclick="closeSuspendModal()"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    Anulează
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-colors">
                    Suspendă
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openSuspendModal(affiliateId) {
        document.getElementById('suspendForm').action = `/admin/affiliates/${affiliateId}/suspend`;
        document.getElementById('suspendModal').classList.remove('hidden');
    }
    
    function closeSuspendModal() {
        document.getElementById('suspendModal').classList.add('hidden');
    }
</script>
@endsection
