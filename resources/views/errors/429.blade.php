@extends('layouts.app')

@section('title', 'Prea multe încercări')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full text-center">
        <div class="mb-8">
            <div class="mx-auto w-24 h-24 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>
        
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">429</h1>
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Prea multe încercări</h2>
        
        <p class="text-gray-600 dark:text-gray-400 mb-8">
            Ai depășit limita de cereri permise. Te rugăm să aștepți 
            <span class="font-semibold text-primary-600 dark:text-primary-400" id="countdown">
                {{ ceil($retryAfter / 60) }} minute
            </span>
            înainte de a încerca din nou.
        </p>

        <div class="mb-8">
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                <div id="progress-bar" class="bg-primary-600 h-2.5 rounded-full transition-all duration-1000" style="width: 100%"></div>
            </div>
        </div>

        <div class="space-y-4">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center justify-center w-full px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Înapoi la pagina principală
            </a>
            
            <button onclick="location.reload()" 
                    id="retry-button"
                    disabled
                    class="inline-flex items-center justify-center w-full px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-medium rounded-lg transition duration-200 cursor-not-allowed">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span id="retry-text">Încearcă din nou</span>
            </button>
        </div>

        <div class="mt-8 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                <svg class="w-4 h-4 inline mr-1 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                Această măsură de siguranță protejează platforma și utilizatorii săi.
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let retryAfter = {{ $retryAfter ?? 60 }};
    const countdown = document.getElementById('countdown');
    const progressBar = document.getElementById('progress-bar');
    const retryButton = document.getElementById('retry-button');
    const retryText = document.getElementById('retry-text');
    const initialTime = retryAfter;

    const interval = setInterval(function() {
        retryAfter--;
        
        // Update countdown text
        if (retryAfter > 60) {
            countdown.textContent = Math.ceil(retryAfter / 60) + ' minute';
        } else if (retryAfter > 0) {
            countdown.textContent = retryAfter + ' secunde';
        } else {
            countdown.textContent = 'gata!';
        }
        
        // Update progress bar
        const progress = (retryAfter / initialTime) * 100;
        progressBar.style.width = progress + '%';
        
        // Update retry button text
        if (retryAfter > 0) {
            retryText.textContent = 'Încearcă din nou (' + retryAfter + 's)';
        }
        
        if (retryAfter <= 0) {
            clearInterval(interval);
            retryButton.disabled = false;
            retryButton.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-500', 'dark:text-gray-400', 'cursor-not-allowed');
            retryButton.classList.add('bg-primary-600', 'hover:bg-primary-700', 'text-white', 'cursor-pointer');
            retryText.textContent = 'Încearcă din nou';
        }
    }, 1000);
});
</script>
@endsection
