@extends('layouts.app')

@section('title', 'Acces Blocat')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-md w-full space-y-8 text-center">
        <div>
            <svg class="mx-auto h-24 w-24 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Acces Blocat
            </h2>
            <p class="mt-2 text-base text-gray-600">
                Am detectat activitate suspectă de la adresa ta IP.
            </p>
        </div>

        <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-left">
            <h3 class="text-sm font-semibold text-red-800 mb-2">De ce am fost blocat?</h3>
            <ul class="text-sm text-red-700 space-y-1 list-disc list-inside">
                <li>Multiple încercări eșuate de autentificare</li>
                <li>Activitate suspectă sau neobișnuită</li>
                <li>Comportament automat detectat (bot)</li>
                <li>Încercări de atac (SQL injection, XSS, etc.)</li>
            </ul>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-left">
            <h3 class="text-sm font-semibold text-blue-800 mb-2">Ce pot face?</h3>
            <ul class="text-sm text-blue-700 space-y-2">
                <li>✓ Blocarea este temporară și va expira automat</li>
                <li>✓ Verifică că nu folosești VPN sau proxy</li>
                <li>✓ Contactează-ne dacă crezi că este o eroare</li>
            </ul>
        </div>

        <div class="pt-4">
            <a href="{{ route('contact') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Contactează Suportul
            </a>
        </div>

        <p class="text-xs text-gray-500 mt-4">
            ID Incident: {{ substr(md5(request()->ip() . now()), 0, 8) }}<br>
            IP: {{ request()->ip() }}<br>
            Timp: {{ now()->format('Y-m-d H:i:s') }}
        </p>
    </div>
</div>
@endsection
