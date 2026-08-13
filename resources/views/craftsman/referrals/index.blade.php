@extends('layouts.craftsman')

@section('title', 'Recomandă un coleg')
@section('page-title', 'Recomandă un coleg')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Explicație --}}
    <div class="bg-primary-50 border border-primary-200 rounded-lg p-5 text-sm text-primary-900">
        <p><strong>Cum funcționează:</strong> recomandă un coleg de breaslă. Dacă el își creează cont pe platformă,
        primești automat <strong>o lună gratuită în plus</strong> la abonamentul tău activ (dacă ai unul), plus
        vizibilitate ca cel mai activ recomandant.</p>
    </div>

    {{-- Link personal --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold text-gray-900 mb-3">Link-ul tău personal de recomandare</h3>
        <div class="flex flex-col sm:flex-row gap-2">
            <input id="referral-link" type="text" readonly value="{{ $referralLink }}"
                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-700">
            <button onclick="copyReferralLink()" type="button"
                    class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-sm font-medium transition">
                Copiază link
            </button>
            <a href="https://wa.me/?text={{ urlencode('Bună! Caut mereu clienți noi și am găsit o platformă gratuită de meseriași. Înscrie-te și tu: ' . $referralLink) }}"
               target="_blank" rel="noopener"
               class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-medium transition text-center">
                Trimite pe WhatsApp
            </a>
        </div>
        <p id="copy-feedback" class="text-xs text-green-600 mt-2 hidden">✅ Link copiat!</p>
    </div>

    {{-- Formular manual --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Sau adaugă direct un coleg</h3>
        <form method="POST" action="{{ route('craftsman.referrals.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Nume complet *</label>
                <input type="text" name="name" required placeholder="Ex: Ion Popescu"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Telefon *</label>
                <input type="text" name="phone" required placeholder="07xx xxx xxx"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Oraș / zonă *</label>
                <input type="text" name="city" required placeholder="Ex: Cluj-Napoca"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Meserie *</label>
                <select name="trade" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
                    <option value="">— Selectează —</option>
                    <option value="electrician">Electrician</option>
                    <option value="instalator">Instalator</option>
                    <option value="tamplar">Tâmplar</option>
                    <option value="zugrav">Zugrav</option>
                    <option value="mecanic">Mecanic</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-medium px-6 py-2.5 rounded-lg text-sm transition">
                    Adaugă recomandare
                </button>
            </div>
        </form>
    </div>

    {{-- Istoric --}}
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900">Istoricul recomandărilor tale</h3>
            <span class="text-sm text-gray-500">{{ $stats['total'] }} total · {{ $stats['inregistrat'] }} înregistrați</span>
        </div>

        @if($referrals->isEmpty())
            <p class="text-sm text-gray-400 text-center py-8">Nu ai recomandat încă pe nimeni.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-200">
                        <tr>
                            <th class="text-left py-2 font-medium text-gray-600">Nume</th>
                            <th class="text-left py-2 font-medium text-gray-600">Meserie</th>
                            <th class="text-left py-2 font-medium text-gray-600">Oraș</th>
                            <th class="text-left py-2 font-medium text-gray-600">Status</th>
                            <th class="text-left py-2 font-medium text-gray-600">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($referrals as $referral)
                            @php
                                $colors = [
                                    'nou'         => 'bg-blue-100 text-blue-800',
                                    'contactat'   => 'bg-yellow-100 text-yellow-800',
                                    'invitat'     => 'bg-purple-100 text-purple-800',
                                    'inregistrat' => 'bg-green-100 text-green-800',
                                    'respins'     => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <tr>
                                <td class="py-2 font-medium text-gray-900">{{ $referral->name }}</td>
                                <td class="py-2 text-gray-600">{{ $referral->trade_label }}</td>
                                <td class="py-2 text-gray-600">{{ $referral->city }}</td>
                                <td class="py-2">
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $colors[$referral->status] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $referral->status_label }}
                                    </span>
                                </td>
                                <td class="py-2 text-gray-500 text-xs">{{ $referral->created_at->format('d.m.Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

<script>
function copyReferralLink() {
    const input = document.getElementById('referral-link');
    input.select();
    navigator.clipboard.writeText(input.value).then(() => {
        const el = document.getElementById('copy-feedback');
        el.classList.remove('hidden');
        setTimeout(() => el.classList.add('hidden'), 3000);
    });
}
</script>
@endsection
