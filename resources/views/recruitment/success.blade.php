<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Înregistrare confirmată — meseriasionline.ro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen flex flex-col items-center justify-center px-4">

<div class="bg-white rounded-2xl shadow-xl p-8 md:p-12 max-w-lg w-full text-center">

    {{-- Icon succes --}}
    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Felicitări, {{ $lead->name }}!</h1>
    <p class="text-gray-600 mb-6">
        Înregistrarea ta ca <strong>{{ $lead->tradeLabel }}</strong> în <strong>{{ $lead->city }}</strong>
        a fost primită cu succes.
    </p>

    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6 text-left text-sm text-blue-800 space-y-2">
        <p>📱 <strong>Ce urmează:</strong></p>
        <ul class="list-disc list-inside space-y-1 ml-2">
            <li>Echipa noastră va verifica înregistrarea în maxim 24h</li>
            @if($lead->email)
                <li>Vei primi un email la <strong>{{ $lead->email }}</strong> cu link-ul de activare a contului complet</li>
            @else
                <li>Te vom contacta la <strong>{{ $lead->phone }}</strong> pentru activarea contului</li>
            @endif
            <li>După activare îți completezi profilul și începi să primești cereri</li>
        </ul>
    </div>

    @if(!$lead->email)
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6 text-sm text-yellow-800">
            💡 <strong>Sfat:</strong> Adaugă un email pentru a primi link-ul de activare mai rapid.
        </div>
    @endif

    {{-- CTA-uri --}}
    <div class="space-y-3">
        <a href="{{ route('home') }}"
           class="block w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-xl transition">
            Mergi la pagina principală
        </a>
        <a href="{{ route('recruitment.form') }}"
           class="block w-full border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium py-3 rounded-xl transition text-sm">
            Înscrie un alt meseriaș
        </a>
    </div>

    {{-- Share social --}}
    <div class="mt-8 pt-6 border-t border-gray-100">
        <p class="text-xs text-gray-500 mb-3">Cunoști alți meseriași care vor clienți noi?</p>
        <a href="https://wa.me/?text={{ urlencode('Am găsit o platformă gratuită de meseriași! Înscrie-te și tu: ' . route('recruitment.form')) }}"
           target="_blank" rel="noopener"
           class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-medium px-5 py-2 rounded-xl text-sm transition">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                <path d="M11.999 2C6.478 2 2 6.478 2 11.999c0 1.977.576 3.818 1.571 5.362L2 22l4.791-1.546A9.96 9.96 0 0011.999 22C17.522 22 22 17.522 22 11.999 22 6.478 17.522 2 11.999 2z"/>
            </svg>
            Trimite pe WhatsApp
        </a>
    </div>

</div>

</body>
</html>
