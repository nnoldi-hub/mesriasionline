<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lasă o recenzie — meseriasionline.ro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-blue-50 to-primary-100 min-h-screen flex flex-col items-center justify-center px-4 py-10">

<div class="bg-white rounded-2xl shadow-xl p-8 md:p-12 max-w-lg w-full">

    <h1 class="text-2xl font-extrabold text-gray-900 mb-2 text-center">Cum a fost lucrarea?</h1>
    <p class="text-gray-600 mb-8 text-center">
        Lucrarea „{{ $quoteRequest->title }}" cu <strong>{{ $quoteRequest->craftsman->name }}</strong>
    </p>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('reviews.public.store', $quoteRequest->review_token) }}" class="space-y-6">
        @csrf

        {{-- Rating general --}}
        <div>
            <label class="block text-sm font-bold text-gray-800 mb-2 text-center">Nota generală *</label>
            <div class="flex justify-center gap-2">
                @for($i = 1; $i <= 5; $i++)
                    <label class="cursor-pointer">
                        <input type="radio" name="rating" value="{{ $i }}" class="peer sr-only" {{ old('rating') == $i ? 'checked' : '' }} required>
                        <span class="flex items-center justify-center w-12 h-12 rounded-xl border-2 border-gray-200 text-lg font-bold text-gray-400 peer-checked:border-primary-600 peer-checked:bg-primary-50 peer-checked:text-primary-700 transition">
                            {{ $i }}★
                        </span>
                    </label>
                @endfor
            </div>
        </div>

        {{-- Comentariu --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Comentariu *</label>
            <textarea name="comment" rows="4" required maxlength="2000" placeholder="Povestește pe scurt cum a decurs lucrarea..."
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 resize-none">{{ old('comment') }}</textarea>
        </div>

        {{-- Sub-ratinguri opționale --}}
        <div class="space-y-3 pt-2 border-t border-gray-100">
            <p class="text-xs text-gray-500">Opțional — detalii suplimentare:</p>
            @foreach(['service_quality_rating' => 'Calitatea lucrării', 'punctuality_rating' => 'Punctualitate', 'cleanliness_rating' => 'Curățenie la finalizare'] as $field => $label)
                <div class="flex items-center justify-between">
                    <label class="text-sm text-gray-600">{{ $label }}</label>
                    <select name="{{ $field }}" class="border border-gray-300 rounded-lg px-2 py-1 text-sm focus:ring-2 focus:ring-primary-500">
                        <option value="">—</option>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old($field) == $i ? 'selected' : '' }}>{{ $i }}★</option>
                        @endfor
                    </select>
                </div>
            @endforeach
        </div>

        <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-xl transition">
            Trimite recenzia
        </button>
    </form>

</div>

</body>
</html>
