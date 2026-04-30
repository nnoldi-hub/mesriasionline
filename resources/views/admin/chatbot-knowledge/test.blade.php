@extends('layouts.admin')
@section('title', 'Test Bază Cunoștințe')
@section('page-title', 'Test Bază Cunoștințe Chatbot')

@section('content')

<div class="flex items-center justify-between mb-6">
    <p class="text-gray-500 text-sm">Testează dacă un mesaj se potrivește cu intrările din baza de cunoștințe</p>
    <a href="{{ route('admin.chatbot.knowledge.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 border border-gray-200 px-3 py-2 rounded-lg transition">
        ← Înapoi la baza de cunoștințe
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
        <div class="text-2xl font-bold text-gray-800">{{ $totalEntries }}</div>
        <div class="text-sm text-gray-500">Total intrări</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
        <div class="text-2xl font-bold text-green-600">{{ $activeEntries }}</div>
        <div class="text-sm text-gray-500">Intrări active</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
        <div class="text-2xl font-bold text-orange-500">{{ $totalEntries - $activeEntries }}</div>
        <div class="text-sm text-gray-500">Inactive</div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h3 class="font-semibold text-gray-800 mb-4">Testează un mesaj</h3>
    <div class="flex gap-3">
        <input
            type="text"
            id="testMessage"
            placeholder="ex: Vreau să mă înscriu ca meseriaș"
            class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-red-300 focus:border-red-400 outline-none"
        />
        <button
            onclick="runTest()"
            class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition"
        >
            Testează
        </button>
    </div>
    <div class="flex flex-wrap gap-2 mt-3">
        @foreach(['Vreau să mă înscriu ca meseriaș', 'Am nevoie de un meseriaș', 'Cum funcționează platforma?', 'Care sunt prețurile?'] as $suggestion)
            <button onclick="document.getElementById('testMessage').value='{{ $suggestion }}'; runTest();"
                    class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-full transition">
                {{ $suggestion }}
            </button>
        @endforeach
    </div>
</div>

<div id="results" class="hidden">
    <div id="matchResult" class="rounded-xl border p-4 mb-4 text-sm"></div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Toate intrările verificate</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-4 py-3 text-gray-500 font-medium">Match</th>
                        <th class="text-left px-4 py-3 text-gray-500 font-medium">Exemplu întrebare</th>
                        <th class="text-left px-4 py-3 text-gray-500 font-medium">Cuvinte cheie</th>
                        <th class="text-left px-4 py-3 text-gray-500 font-medium">CTA</th>
                        <th class="text-left px-4 py-3 text-gray-500 font-medium">Răspuns (preview)</th>
                    </tr>
                </thead>
                <tbody id="resultsBody" class="divide-y divide-gray-100"></tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
async function runTest() {
    const msg = document.getElementById('testMessage').value.trim();
    if (!msg) return;

    const btn = document.querySelector('button[onclick="runTest()"]');
    btn.textContent = 'Se testează...';
    btn.disabled = true;

    try {
        const res = await fetch('{{ route('admin.chatbot.knowledge.test.query') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ message: msg }),
        });
        const data = await res.json();

        const matchDiv = document.getElementById('matchResult');
        if (data.first_match) {
            matchDiv.className = 'rounded-xl border border-green-300 bg-green-50 p-4 mb-4 text-sm';
            matchDiv.innerHTML = `
                <div class="font-semibold text-green-800 mb-1">✅ S-a găsit o potrivire!</div>
                <div class="text-green-700"><strong>Intrare:</strong> ${data.first_match.question_example}</div>
                <div class="text-green-700"><strong>Răspuns preview:</strong> ${data.first_match.answer_preview}</div>
                ${data.first_match.cta_url ? `<div class="text-green-700"><strong>CTA:</strong> <a href="${data.first_match.cta_url}" target="_blank" class="underline">${data.first_match.cta_label} → ${data.first_match.cta_url}</a></div>` : '<div class="text-orange-600">⚠️ Fără CTA configurat (lipsesc câmpurile Buton CTA)</div>'}
            `;
        } else {
            matchDiv.className = 'rounded-xl border border-orange-300 bg-orange-50 p-4 mb-4 text-sm';
            matchDiv.innerHTML = `
                <div class="font-semibold text-orange-800 mb-1">⚠️ Nicio potrivire găsită în baza de cunoștințe</div>
                <div class="text-orange-700">Mesajul va fi trimis la OpenAI (sau va returna eroare dacă nu există credite).</div>
                <div class="text-orange-700 mt-1">Verifică cuvintele cheie din intrările active — trebuie să fie substrings din mesajul testat.</div>
            `;
        }

        const tbody = document.getElementById('resultsBody');
        tbody.innerHTML = data.all_results.map(r => `
            <tr class="${r.matched ? 'bg-green-50' : ''}">
                <td class="px-4 py-3">${r.matched ? '<span class="text-green-600 font-bold">✓ DA</span>' : '<span class="text-gray-400">—</span>'}</td>
                <td class="px-4 py-3 text-gray-700">${r.question_example}</td>
                <td class="px-4 py-3"><code class="text-xs bg-gray-100 px-1 rounded">${r.keywords}</code></td>
                <td class="px-4 py-3">${r.cta_url ? `<a href="${r.cta_url}" target="_blank" class="text-blue-600 hover:underline text-xs">${r.cta_label}</a>` : '<span class="text-gray-400 text-xs">fără CTA</span>'}</td>
                <td class="px-4 py-3 text-gray-500 text-xs max-w-xs truncate">${r.answer_preview}</td>
            </tr>
        `).join('');

        document.getElementById('results').classList.remove('hidden');
    } catch(e) {
        alert('Eroare: ' + e.message);
    } finally {
        btn.textContent = 'Testează';
        btn.disabled = false;
    }
}

document.getElementById('testMessage').addEventListener('keydown', e => {
    if (e.key === 'Enter') runTest();
});
</script>
@endpush
