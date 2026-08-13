<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recenzie deja trimisă — meseriasionline.ro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-blue-50 to-primary-100 min-h-screen flex flex-col items-center justify-center px-4">

<div class="bg-white rounded-2xl shadow-xl p-8 md:p-12 max-w-lg w-full text-center">

    <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Ai lăsat deja o recenzie</h1>
    <p class="text-gray-600 mb-6">
        Mulțumim! Recenzia ta pentru <strong>{{ $quoteRequest->craftsman->name }}</strong> a fost deja înregistrată.
    </p>

    <a href="{{ route('home') }}"
       class="block w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-xl transition">
        Mergi la pagina principală
    </a>

</div>

</body>
</html>
