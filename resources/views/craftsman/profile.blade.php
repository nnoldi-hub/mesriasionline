@extends('layouts.craftsman')

@section('title', 'Profilul Meu')
@section('page-title', 'Profilul Meu')

@section('content')
<div class="max-w-4xl">
    <form id="profile-update-form" action="{{ route('craftsman.profile.update') }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf
        @method('PUT')

        <input type="hidden" name="latitude" id="craftsman_lat" value="{{ old('latitude', $craftsman->latitude) }}">
        <input type="hidden" name="longitude" id="craftsman_lng" value="{{ old('longitude', $craftsman->longitude) }}">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nume Complet</label>
                <input type="text" name="name" value="{{ old('name', $craftsman->name) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                @error('name')
                    <p class="mt-1 text-sm text-error-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                <input type="text" name="phone" value="{{ old('phone', $craftsman->phone) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                @error('phone')
                    <p class="mt-1 text-sm text-error-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ani Experiență</label>
                <input type="number" name="experience_years" value="{{ old('experience_years', $craftsman->experience_years) }}" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Specializare</label>
                <input type="text" name="specialization" value="{{ old('specialization', $craftsman->specialization) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Rază Servicii (km)</label>
                <input type="number" name="service_radius_km" value="{{ old('service_radius_km', $craftsman->service_radius_km) }}" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Distanța maximă față de locația ta la care ești dispus să te deplasezi</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Oraș / Județ</label>
                <select name="location_id" id="location_select"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent bg-white">
                    <option value="">-- Selectează orașul --</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}"
                            data-lat="{{ $loc->latitude }}"
                            data-lng="{{ $loc->longitude }}"
                            data-slug="{{ $loc->slug }}"
                            {{ old('location_id', $craftsman->location_id) == $loc->id ? 'selected' : '' }}>
                            {{ $loc->city }}{{ $loc->city !== $loc->county ? ' (' . $loc->county . ')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <div class="border border-blue-200 rounded-lg p-4" style="background-color:#eef6fc;">
                    <div class="flex items-start gap-3 mb-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color:#2980B9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 text-sm">Locația ta GPS</h3>
                            <p class="text-xs text-gray-600 mt-0.5">
                                Coordonatele GPS permit sistemului să te recomande automat clienților din zona ta 
                                și să afișezi raza în care lucrezi.
                            </p>
                        </div>
                    </div>

                    @if($craftsman->latitude && $craftsman->longitude)
                        <div class="flex items-center gap-2 mb-3 text-sm text-green-700 bg-green-50 px-3 py-2 rounded-lg">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Locație setată: {{ number_format($craftsman->latitude, 5) }}, {{ number_format($craftsman->longitude, 5) }}</span>
                        </div>
                    @else
                        <div class="flex items-center gap-2 mb-3 text-sm text-amber-700 bg-amber-50 px-3 py-2 rounded-lg">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>Locația GPS nu este setată — nu vei apărea în căutări de proximitate</span>
                        </div>
                    @endif

                    <div class="flex flex-wrap gap-3 items-center">
                        <button type="button" id="detect-location-btn"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors"
                            style="background-color:#2980B9;"
                            onmouseover="this.style.backgroundColor='#1f6ea0'" 
                            onmouseout="this.style.backgroundColor='#2980B9'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span id="detect-location-text">Detectează locația automat</span>
                        </button>

                        <button type="button" id="use-city-btn"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors"
                            style="background-color:#16a34a;"
                            onmouseover="this.style.backgroundColor='#15803d'"
                            onmouseout="this.style.backgroundColor='#16a34a'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Folosește coordonatele orașului
                        </button>

                        <span class="text-xs text-gray-500">sau introdu manual:</span>

                        <div class="flex gap-2">
                            <input type="number" id="manual_lat" step="0.00001" placeholder="Latitudine (ex: 44.43225)"
                                value="{{ $craftsman->latitude }}"
                                class="w-44 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-300"
                                oninput="document.getElementById('craftsman_lat').value=this.value">
                            <input type="number" id="manual_lng" step="0.00001" placeholder="Longitudine (ex: 26.10626)"
                                value="{{ $craftsman->longitude }}"
                                class="w-44 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-300"
                                oninput="document.getElementById('craftsman_lng').value=this.value">
                        </div>
                    </div>

                    <p id="location-status" class="text-xs text-gray-500 mt-2 hidden"></p>
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Descriere</label>
                <div id="profile_desc_editor" style="min-height:120px;" class="border border-gray-300 rounded-lg bg-white"></div>
                <textarea name="description" id="profile_description" class="hidden">{{ old('description', $craftsman->description) }}</textarea>
            </div>

            <div class="md:col-span-2 space-y-3">
                <div class="flex items-center">
                    <input type="checkbox" name="available_weekends" value="1" {{ $craftsman->available_weekends ? 'checked' : '' }}
                        class="h-4 w-4 text-primary-600 focus:ring-primary-600 border-gray-300 rounded">
                    <label class="ml-2 text-sm text-gray-700">Disponibil weekend</label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="emergency_services" value="1" {{ $craftsman->emergency_services ? 'checked' : '' }}
                        class="h-4 w-4 text-primary-600 focus:ring-primary-600 border-gray-300 rounded">
                    <label class="ml-2 text-sm text-gray-700">Ofer servicii de urgență</label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="has_insurance" value="1" {{ old('has_insurance', $craftsman->has_insurance) ? 'checked' : '' }}
                        class="h-4 w-4 text-primary-600 focus:ring-primary-600 border-gray-300 rounded">
                    <label class="ml-2 text-sm text-gray-700">Am asigurare profesională</label>
                </div>
            </div>

            {{-- Secțiune Firmă / Persoană Juridică --}}
            <div class="md:col-span-2">
                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <div class="flex items-center mb-3">
                        <input type="checkbox" name="is_company" id="is_company" value="1"
                            {{ old('is_company', $craftsman->is_company) ? 'checked' : '' }}
                            class="h-4 w-4 text-primary-600 focus:ring-primary-600 border-gray-300 rounded"
                            onchange="toggleCompanyFields(this.checked)">
                        <label for="is_company" class="ml-2 text-sm font-medium text-gray-800">
                            Activez ca firmă / persoană juridică (PFA, SRL, SA, II)
                        </label>
                    </div>

                    <div id="company-fields" class="{{ old('is_company', $craftsman->is_company) ? '' : 'hidden' }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Denumire firmă <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="company_name"
                                    value="{{ old('company_name', $craftsman->company_name) }}"
                                    placeholder="ex: SC Instalații Rapide SRL"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                                @error('company_name')
                                    <p class="mt-1 text-xs text-error-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tip entitate</label>
                                <select name="company_type"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-600 focus:border-transparent bg-white">
                                    <option value="">-- Selectează --</option>
                                    @foreach(['PFA' => 'PFA – Persoană Fizică Autorizată', 'SRL' => 'SRL – Societate cu Răspundere Limitată', 'SA' => 'SA – Societate pe Acțiuni', 'II' => 'II – Întreprindere Individuală', 'RA' => 'RA – Regie Autonomă'] as $val => $label)
                                        <option value="{{ $val }}" {{ old('company_type', $craftsman->company_type) === $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    CUI (Cod Unic de Înregistrare)
                                </label>
                                <input type="text" name="cui"
                                    value="{{ old('cui', $craftsman->cui) }}"
                                    placeholder="ex: RO12345678 sau 12345678"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                                @error('cui')
                                    <p class="mt-1 text-xs text-error-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nr. Registrul Comerțului
                                </label>
                                <input type="text" name="reg_com"
                                    value="{{ old('reg_com', $craftsman->reg_com) }}"
                                    placeholder="ex: J40/1234/2020"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                                @error('reg_com')
                                    <p class="mt-1 text-xs text-error-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <p class="mt-3 text-xs text-gray-500">
                            Datele firmei vor fi afișate pe profilul tău public și cresc încrederea clienților.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium">
                Salvează Modificările
            </button>
        </div>
    </form>
</div>

@push('head')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
// Quill editor pentru descriere profil
var profileDescQuill = new Quill('#profile_desc_editor', {
    theme: 'snow',
    placeholder: 'Descrie serviciile tale, experiența, specializările...',
    modules: {
        toolbar: [
            [{ 'header': [2, 3, false] }],
            ['bold', 'italic'],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            ['clean']
        ]
    }
});
var profileDescExisting = document.getElementById('profile_description').value;
if (profileDescExisting) {
    profileDescQuill.clipboard.dangerouslyPasteHTML(profileDescExisting);
    // Deselect după paste pentru a evita "tot textul selectat"
    setTimeout(function() { profileDescQuill.setSelection(null); }, 50);
}
// Sincronizare live: textarea se actualizează la orice modificare în editor
profileDescQuill.on('text-change', function() {
    var html = profileDescQuill.root.innerHTML;
    // Nu salva starea goală Quill (<p><br></p>)
    document.getElementById('profile_description').value = (html === '<p><br></p>') ? '' : html;
});
// Backup: sincronizare și la submit
document.getElementById('profile-update-form').addEventListener('submit', function () {
    var html = profileDescQuill.root.innerHTML;
    document.getElementById('profile_description').value = (html === '<p><br></p>') ? '' : html;
});
</script>
<script>
function toggleCompanyFields(checked) {
    const fields = document.getElementById('company-fields');
    if (checked) {
        fields.classList.remove('hidden');
    } else {
        fields.classList.add('hidden');
    }
}

// Coordonate fallback pentru orașele principale (când DB are valori null)
const CITY_COORDS_FALLBACK = {
    'bucuresti': [44.4268, 26.1025],
    'voluntari-ilfov': [44.4953, 26.1886],
    'popesti-leordeni-ilfov': [44.3814, 26.1614],
    'bragadiru-ilfov': [44.3614, 26.0408],
    'pantelimon-ilfov': [44.4533, 26.2192],
    'cluj-napoca-cluj': [46.7712, 23.6236],
    'floresti-cluj': [46.7486, 23.4986],
    'turda-cluj': [46.5683, 23.7847],
    'iasi-iasi': [47.1585, 27.6014],
    'pascani-iasi': [47.2453, 26.7167],
    'timisoara-timis': [45.7489, 21.2087],
    'lugoj-timis': [45.6856, 21.9031],
    'constanta-constanta': [44.1598, 28.6348],
    'mangalia-constanta': [43.8153, 28.5831],
    'medgidia-constanta': [44.2469, 28.2769],
    'brasov-brasov': [45.6427, 25.5887],
    'sacele-brasov': [45.6167, 25.7000],
    'codlea-brasov': [45.7042, 25.4514],
    'craiova-dolj': [44.3302, 23.7949],
    'ploiesti-prahova': [44.9397, 26.0173],
    'campina-prahova': [45.1272, 25.7331],
    'galati-galati': [45.4353, 28.0080],
    'braila-braila': [45.2692, 27.9575],
    'oradea-bihor': [47.0465, 21.9189],
    'bacau-bacau': [46.5670, 26.9146],
    'arad-arad': [46.1866, 21.3123],
    'pitesti-arges': [44.8565, 24.8692],
    'sibiu-sibiu': [45.7983, 24.1256],
    'targu-mures-mures': [46.5425, 24.5575],
    'baia-mare-maramures': [47.6567, 23.5678],
};

document.getElementById('use-city-btn').addEventListener('click', function () {
    const select = document.getElementById('location_select');
    const statusEl = document.getElementById('location-status');

    if (!select.value) {
        statusEl.textContent = 'Selectează mai întâi un oraș din dropdown-ul de mai sus.';
        statusEl.style.color = '#b45309';
        statusEl.classList.remove('hidden');
        return;
    }

    const option = select.options[select.selectedIndex];
    const lat = option.getAttribute('data-lat');
    const lng = option.getAttribute('data-lng');
    const slug = option.getAttribute('data-slug');

    // Dacă avem coordonatele direct din DB (în HTML), le folosim
    if (lat && lng && lat !== 'null' && lng !== 'null' && lat !== '' && lng !== '') {
        applyCoordinates(parseFloat(lat), parseFloat(lng), option.textContent.trim());
        return;
    }

    // Fallback 1: coordonate hardcodate pentru orașele principale
    if (slug && CITY_COORDS_FALLBACK[slug]) {
        const coords = CITY_COORDS_FALLBACK[slug];
        applyCoordinates(coords[0], coords[1], option.textContent.trim());
        return;
    }

    // Fallback 2: le cerem din API
    const btn = this;
    btn.disabled = true;
    btn.textContent = 'Se încarcă...';

    fetch('/api/v1/locations/' + select.value)
        .then(r => r.json())
        .then(data => {
            const loc = data.data || data;
            if (loc.latitude && loc.longitude) {
                applyCoordinates(parseFloat(loc.latitude), parseFloat(loc.longitude), option.textContent.trim());
            } else {
                statusEl.textContent = 'Orașul selectat nu are coordonate GPS salvate în baza de date.';
                statusEl.style.color = '#dc2626';
                statusEl.classList.remove('hidden');
            }
        })
        .catch(() => {
            statusEl.textContent = 'Eroare la obținerea coordonatelor. Încearcă manual.';
            statusEl.style.color = '#dc2626';
            statusEl.classList.remove('hidden');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>Folosește coordonatele orașului';
        });
});

function applyCoordinates(lat, lng, cityName) {
    document.getElementById('craftsman_lat').value = lat;
    document.getElementById('craftsman_lng').value = lng;
    document.getElementById('manual_lat').value = lat.toFixed(5);
    document.getElementById('manual_lng').value = lng.toFixed(5);

    const statusEl = document.getElementById('location-status');
    statusEl.textContent = '✓ Coordonate preluate din: ' + cityName + ' (' + lat.toFixed(5) + ', ' + lng.toFixed(5) + '). Salvează profilul pentru a confirma.';
    statusEl.style.color = '#15803d';
    statusEl.classList.remove('hidden');
}

document.getElementById('detect-location-btn').addEventListener('click', function () {
    if (!navigator.geolocation) {
        document.getElementById('location-status').textContent = 'Browserul tău nu suportă geolocalizare.';
        document.getElementById('location-status').classList.remove('hidden');
        return;
    }

    const btn = this;
    const statusEl = document.getElementById('location-status');

    btn.disabled = true;
    document.getElementById('detect-location-text').textContent = 'Se detectează...';
    statusEl.textContent = 'Așteptare permisiune locație...';
    statusEl.classList.remove('hidden');
    statusEl.style.color = '#6b7280';

    navigator.geolocation.getCurrentPosition(
        function (position) {
            const lat = position.coords.latitude.toFixed(8);
            const lng = position.coords.longitude.toFixed(8);

            document.getElementById('craftsman_lat').value = lat;
            document.getElementById('craftsman_lng').value = lng;
            document.getElementById('manual_lat').value = lat;
            document.getElementById('manual_lng').value = lng;

            statusEl.textContent = '✓ Locație detectată: ' + lat + ', ' + lng + '. Salvează profilul pentru a confirma.';
            statusEl.style.color = '#15803d';
            document.getElementById('detect-location-text').textContent = 'Locație detectată';
            btn.disabled = false;
        },
        function (error) {
            const msgs = {
                1: 'Ai blocat accesul la locație. Permite accesul în setările browserului.',
                2: 'Nu s-a putut determina locația. Introdu coordonatele manual.',
                3: 'Timeout — încearcă din nou sau introdu coordonatele manual.',
            };
            statusEl.textContent = msgs[error.code] || 'Eroare necunoscută.';
            statusEl.style.color = '#dc2626';
            document.getElementById('detect-location-text').textContent = 'Detectează locația automat';
            btn.disabled = false;
        },
        { enableHighAccuracy: true, timeout: 15000 }
    );
});
</script>
@endpush
@endsection
