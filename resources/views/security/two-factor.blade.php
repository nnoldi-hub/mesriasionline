@extends('layouts.app')

@section('title', 'Securitate - Autentificare în Doi Pași')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li><a href="{{ route('home') }}" class="text-gray-500 hover:text-primary-600">Acasă</a></li>
                <li class="flex items-center">
                    <svg class="w-4 h-4 mx-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-gray-700 dark:text-gray-300">Securitate</span>
                </li>
            </ol>
        </nav>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Autentificare în Doi Pași (2FA)</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Adaugă un nivel suplimentar de securitate contului tău</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        @if($isEnabled)
            {{-- 2FA is enabled --}}
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">2FA Activat</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Contul tău este protejat cu autentificare în doi pași.</p>
                    </div>
                </div>
            </div>

            {{-- Recovery codes --}}
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Coduri de Recuperare</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Salvează aceste coduri într-un loc sigur. Le poți folosi pentru a accesa contul dacă pierzi accesul la aplicația de autentificare.
                </p>
                
                @if(count($recoveryCodes) > 0)
                    <div class="grid grid-cols-2 gap-2 mb-4" id="recovery-codes">
                        @foreach($recoveryCodes as $code)
                            <div class="bg-gray-100 dark:bg-gray-700 p-2 rounded text-center font-mono text-sm">
                                {{ $code }}
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-400 mb-4">
                        Coduri rămase: {{ count($recoveryCodes) }} din 8
                    </p>
                @else
                    <p class="text-yellow-600 mb-4">Nu mai ai coduri de recuperare disponibile!</p>
                @endif

                <button onclick="regenerateRecoveryCodes()" 
                        class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Regenerează Coduri
                </button>
            </div>

            {{-- Disable 2FA --}}
            <div class="p-6">
                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Dezactivează 2FA</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Nu recomandăm dezactivarea autentificării în doi pași, dar o poți face introducând un cod valid.
                </p>
                <button onclick="showDisableModal()" 
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                    Dezactivează 2FA
                </button>
            </div>
        @else
            {{-- 2FA is not enabled --}}
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">2FA Neactivat</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Contul tău nu are protecție suplimentară.</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="mb-6">
                    <h4 class="text-md font-medium text-gray-900 dark:text-white mb-2">Cum funcționează?</h4>
                    <ol class="list-decimal list-inside text-sm text-gray-600 dark:text-gray-400 space-y-2">
                        <li>Descarcă o aplicație de autentificare (Google Authenticator, Authy, etc.)</li>
                        <li>Scanează codul QR generat de platformă</li>
                        <li>Introdu codul din aplicație pentru confirmare</li>
                        <li>Salvează codurile de recuperare într-un loc sigur</li>
                    </ol>
                </div>

                <button onclick="enableTwoFactor()" 
                        class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Activează Autentificarea în Doi Pași
                </button>
            </div>
        @endif
    </div>
</div>

{{-- Setup Modal --}}
<div id="setup-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" onclick="closeModals()"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6 scale-in">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Configurează 2FA</h3>
            
            <div id="setup-step-1">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Scanează acest cod QR cu aplicația ta de autentificare:
                </p>
                <div id="qr-code" class="flex justify-center mb-4 p-4 bg-white rounded-lg"></div>
                <div class="mb-4 p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Sau introdu manual acest cod:</p>
                    <code id="secret-code" class="text-sm font-mono text-gray-900 dark:text-white break-all"></code>
                </div>
            </div>

            <div id="setup-step-2" class="hidden">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Introdu codul din aplicația de autentificare:
                </p>
                <input type="text" id="confirm-code" 
                       class="w-full px-4 py-3 text-center text-2xl tracking-widest font-mono border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700"
                       maxlength="6" placeholder="000000" autocomplete="off">
                <p id="confirm-error" class="text-red-500 text-sm mt-2 hidden"></p>
            </div>

            <div id="setup-step-3" class="hidden">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">2FA Activat cu Succes!</h4>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Salvează aceste coduri de recuperare:
                </p>
                <div id="new-recovery-codes" class="grid grid-cols-2 gap-2 mb-4"></div>
                <p class="text-xs text-red-500">⚠️ Aceste coduri nu vor mai fi afișate!</p>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closeModals()" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                    Anulează
                </button>
                <button id="setup-next-btn" onclick="nextSetupStep()" 
                        class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    Continuă
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Disable Modal --}}
<div id="disable-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" onclick="closeModals()"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6 scale-in">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Dezactivează 2FA</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Pentru a dezactiva autentificarea în doi pași, introdu un cod valid din aplicație sau un cod de recuperare:
            </p>
            <input type="text" id="disable-code" 
                   class="w-full px-4 py-3 text-center text-lg tracking-widest font-mono border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700"
                   placeholder="Cod" autocomplete="off">
            <p id="disable-error" class="text-red-500 text-sm mt-2 hidden"></p>

            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closeModals()" class="px-4 py-2 text-gray-600 dark:text-gray-400">Anulează</button>
                <button onclick="disableTwoFactor()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Dezactivează
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let currentStep = 1;
let currentSecret = '';

function enableTwoFactor() {
    fetch('/security/2fa/enable', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentSecret = data.secret;
            document.getElementById('qr-code').innerHTML = data.qr_code_svg || `<img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(data.qr_code_url)}" alt="QR Code">`;
            document.getElementById('secret-code').textContent = data.secret;
            document.getElementById('setup-modal').classList.remove('hidden');
            currentStep = 1;
            updateSetupUI();
        }
    });
}

function nextSetupStep() {
    if (currentStep === 1) {
        currentStep = 2;
        updateSetupUI();
    } else if (currentStep === 2) {
        confirmTwoFactor();
    } else {
        closeModals();
        location.reload();
    }
}

function confirmTwoFactor() {
    const code = document.getElementById('confirm-code').value;
    
    fetch('/security/2fa/confirm', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ code }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentStep = 3;
            const codesContainer = document.getElementById('new-recovery-codes');
            codesContainer.innerHTML = data.recovery_codes.map(code => 
                `<div class="bg-gray-100 dark:bg-gray-700 p-2 rounded text-center font-mono text-sm">${code}</div>`
            ).join('');
            updateSetupUI();
        } else {
            document.getElementById('confirm-error').textContent = data.message;
            document.getElementById('confirm-error').classList.remove('hidden');
        }
    });
}

function updateSetupUI() {
    document.getElementById('setup-step-1').classList.toggle('hidden', currentStep !== 1);
    document.getElementById('setup-step-2').classList.toggle('hidden', currentStep !== 2);
    document.getElementById('setup-step-3').classList.toggle('hidden', currentStep !== 3);
    
    const btn = document.getElementById('setup-next-btn');
    btn.textContent = currentStep === 3 ? 'Închide' : 'Continuă';
}

function showDisableModal() {
    document.getElementById('disable-modal').classList.remove('hidden');
}

function disableTwoFactor() {
    const code = document.getElementById('disable-code').value;
    
    fetch('/security/2fa/disable', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ code }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModals();
            location.reload();
        } else {
            document.getElementById('disable-error').textContent = data.message;
            document.getElementById('disable-error').classList.remove('hidden');
        }
    });
}

function regenerateRecoveryCodes() {
    const code = prompt('Introdu un cod din aplicație pentru a regenera codurile:');
    if (!code) return;
    
    fetch('/security/2fa/recovery-codes', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ code }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Codurile au fost regenerate. Salvează-le!');
            location.reload();
        } else {
            alert(data.message);
        }
    });
}

function closeModals() {
    document.getElementById('setup-modal').classList.add('hidden');
    document.getElementById('disable-modal').classList.add('hidden');
}
</script>
@endpush
@endsection
