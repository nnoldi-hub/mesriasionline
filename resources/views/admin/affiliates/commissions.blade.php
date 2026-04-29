@extends('layouts.admin')

@section('title', 'Comisioane Afiliere')
@section('page-title', 'Comisioane')

@section('content')

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
    <a href="{{ route('admin.affiliates.index') }}" class="hover:text-gray-700 transition">Afilieri</a>
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
    <span class="text-gray-900 font-medium">Comisioane</span>
</nav>

@if(session('success'))
    <div class="mb-4 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- Filtre --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ route('admin.affiliates.commissions') }}" class="flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[160px]">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                <option value="">Toate statusurile</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>În așteptare</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprobat</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Respins</option>
                <option value="paid"     {{ request('status') === 'paid'     ? 'selected' : '' }}>Plătit</option>
            </select>
        </div>
        <div class="flex-1 min-w-[160px]">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tip Tranzacție</label>
            <select name="type" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                <option value="">Toate tipurile</option>
                <option value="registration"  {{ request('type') === 'registration'  ? 'selected' : '' }}>Înregistrare</option>
                <option value="subscription"  {{ request('type') === 'subscription'  ? 'selected' : '' }}>Subscripție</option>
                <option value="booking"       {{ request('type') === 'booking'       ? 'selected' : '' }}>Programare</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                Filtrează
            </button>
            @if(request()->hasAny(['status', 'type']))
                <a href="{{ route('admin.affiliates.commissions') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if($commissions->count() > 0)
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Afiliat</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Utilizator Referit</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tip</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Tranzacție</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Comision</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Data</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acțiuni</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($commissions as $commission)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <p class="text-sm font-medium text-gray-900">{{ $commission->affiliate->user->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-400 font-mono">{{ $commission->affiliate->referral_code ?? '' }}</p>
                    </td>
                    <td class="px-5 py-4 text-sm text-gray-600">
                        {{ $commission->referredUser->name ?? '—' }}
                    </td>
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                            {{ ucfirst(str_replace('_', ' ', $commission->transaction_type)) }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-right text-sm text-gray-700">
                        {{ number_format($commission->transaction_amount, 2) }} lei
                        <p class="text-xs text-gray-400">{{ $commission->commission_rate }}%</p>
                    </td>
                    <td class="px-5 py-4 text-right">
                        <span class="text-sm font-bold text-gray-900">{{ number_format($commission->commission_amount, 2) }} lei</span>
                    </td>
                    <td class="px-5 py-4 text-center">
                        @php
                            $statusConfig = [
                                'pending'  => ['bg-yellow-100 text-yellow-700', 'În așteptare'],
                                'approved' => ['bg-green-100 text-green-700',  'Aprobat'],
                                'rejected' => ['bg-red-100 text-red-700',      'Respins'],
                                'paid'     => ['bg-blue-100 text-blue-700',    'Plătit'],
                            ];
                            [$cls, $label] = $statusConfig[$commission->status] ?? ['bg-gray-100 text-gray-600', $commission->status];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $cls }}">
                            {{ $label }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-sm text-gray-500">
                        {{ $commission->created_at->format('d.m.Y') }}
                    </td>
                    <td class="px-5 py-4 text-right">
                        @if($commission->status === 'pending')
                            <div class="flex items-center justify-end gap-1">
                                <form action="{{ route('admin.affiliates.commissions.approve', $commission) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition" title="Aprobă">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </form>
                                <button type="button" onclick="openRejectModal({{ $commission->id }})"
                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Respinge">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        @else
                            <span class="text-gray-300 text-sm">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($commissions->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50">
                {{ $commissions->withQueryString()->links() }}
            </div>
        @endif
    @else
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-gray-900 font-semibold text-base mb-1">Nu există comisioane</h3>
            <p class="text-gray-500 text-sm">Comisioanele vor apărea aici pe măsură ce afiliații aduc referrals.</p>
        </div>
    @endif
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <form id="rejectForm" method="POST">
            @csrf
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900">Respinge Comision</h3>
                <button type="button" onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="px-6 py-5">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Motiv</label>
                <textarea name="reason" rows="3" required
                    class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    placeholder="Explică motivul respingerii..."></textarea>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Anulează</button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">Respinge</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const rejectBaseUrl = '{{ url("admin/affiliates/commissions") }}';

function openRejectModal(id) {
    document.getElementById('rejectForm').action = rejectBaseUrl + '/' + id + '/reject';
    const modal = document.getElementById('rejectModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});
</script>
@endpush

@endsection
