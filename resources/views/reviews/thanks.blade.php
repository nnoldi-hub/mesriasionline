<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mulțumim pentru recenzie — meseriasionline.ro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen flex flex-col items-center justify-center px-4">

<div class="bg-white rounded-2xl shadow-xl p-8 md:p-12 max-w-lg w-full text-center">

    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Mulțumim!</h1>
    <p class="text-gray-600 mb-6">
        Recenzia ta pentru <strong>{{ $quoteRequest->craftsman->name }}</strong> a fost trimisă și va apărea
        pe platformă după o verificare rapidă.
    </p>

    <a href="{{ route('home') }}"
       class="block w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-xl transition">
        Mergi la pagina principală
    </a>

</div>

</body>
</html>
