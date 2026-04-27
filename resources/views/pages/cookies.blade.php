@extends('layouts.app')

@section('title', 'Politica Cookies - Omul Potrivit')
@section('description', 'Află ce sunt cookies, cum le folosim pe Omul Potrivit și cum poți gestiona preferințele tale.')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-8 mt-8">
    <h1 class="text-3xl font-bold mb-6">Politica Cookies</h1>
    <p class="mb-4">Această pagină explică ce sunt cookies, cum le folosim pe platforma Omul Potrivit și ce opțiuni ai pentru gestionarea lor.</p>
    <h2 class="text-xl font-semibold mt-6 mb-2">Ce sunt cookies?</h2>
    <p class="mb-4">Cookies sunt fișiere text mici stocate pe dispozitivul tău de către site-ul web pe care îl vizitezi. Ele ajută la funcționarea corectă a site-ului, la personalizarea experienței și la colectarea de statistici anonime.</p>
    <h2 class="text-xl font-semibold mt-6 mb-2">Cum folosim cookies pe Omul Potrivit?</h2>
    <ul class="list-disc pl-6 mb-4">
        <li>Funcționare esențială a platformei (autentificare, navigare, rezervări).</li>
        <li>Analiză și statistici anonime pentru îmbunătățirea serviciilor.</li>
        <li>Reținerea preferințelor tale (limbă, acceptare cookies).</li>
    </ul>
    <h2 class="text-xl font-semibold mt-6 mb-2">Cum poți gestiona cookies?</h2>
    <p class="mb-4">Poți șterge sau bloca cookies din setările browserului tău. Totuși, blocarea cookies esențiale poate afecta funcționarea corectă a platformei.</p>
    <h2 class="text-xl font-semibold mt-6 mb-2">Mai multe informații</h2>
    <p>Pentru detalii suplimentare, consultă <a href="{{ route('privacy') }}" class="underline text-primary-600">Politica de confidențialitate</a> sau contactează-ne la <a href="mailto:contact@omulpotrivit.ro" class="underline text-primary-600">contact@omulpotrivit.ro</a>.</p>
</div>
@endsection
