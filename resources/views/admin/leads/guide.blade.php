@extends('layouts.dashboard')

@section('title', 'Ghid Recrutare Meseriași')
@section('page-title', 'Ghid Recrutare Meseriași')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('header-actions')
<div class="flex items-center gap-3">
    <a href="{{ route('admin.leads.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 text-sm font-medium transition">
        + Adaugă manual
    </a>
    <a href="{{ route('admin.leads.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 text-sm transition">
        ← Înapoi la listă
    </a>
</div>
@endsection

@section('content')

<div class="max-w-3xl space-y-6">

    <div class="bg-primary-50 border border-primary-200 rounded-xl p-5">
        <p class="text-sm text-primary-900">
            <strong>Obiectivul tău:</strong> primii 10 meseriași pe fiecare meserie (electrician, instalator, tâmplar, zugrav, mecanic)
            sunt gratuiți — folosește exact asta ca argument de vânzare în orice mesaj trimis. E o ofertă cu termen limitat reală,
            nu una inventată, deci funcționează bine ca motivator.
        </p>
    </div>

    {{-- 1. Canale gratuite --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-bold text-gray-800 mb-3">1. Canale online gratuite</h2>
        <ul class="space-y-3 text-sm text-gray-700">
            <li>
                <strong>Grupuri Facebook locale</strong> — caută "Meseriași [orașul tău]", "Instalatori/Electricieni [oraș]",
                grupuri de "Amenajări și renovări [oraș]". Meseriașii se promovează acolo constant — le poți răspunde
                direct la postări sau le poți scrie în privat.
            </li>
            <li>
                <strong>OLX / Publi24</strong> — cei mai mulți meseriași independenți din România au deja un anunț acolo,
                cu telefon vizibil. E cea mai rapidă sursă de numere de telefon reale, active. Sună direct, spune-le de
                oferta gratuită, și adaugă-i imediat cu <a href="{{ route('admin.leads.create') }}" class="text-primary-600 underline">"Adaugă manual"</a>.
            </li>
            <li>
                <strong>Grupuri WhatsApp de breaslă</strong> — multe orașe au grupuri organizate pe meserie (electricieni,
                zugravi etc.) unde se distribuie oferte de lucru. Cere să fii adăugat printr-un meseriaș deja cunoscut.
            </li>
        </ul>
    </div>

    {{-- 2. Parteneriate locale --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-bold text-gray-800 mb-3">2. Parteneriate cu magazine și firme locale</h2>
        <ul class="space-y-3 text-sm text-gray-700">
            <li>
                <strong>Magazine de bricolaj/materiale</strong> (Dedeman, Hornbach, magazine locale de instalații sau
                electrice) — meseriașii trec zilnic pe acolo. Un flyer sau un cod QR la casă către formularul de înscriere
                costă puțin și ajunge exact la publicul țintă.
            </li>
            <li>
                <strong>Firme de construcții/amenajări</strong> care lucrează cu subcontractori — propune-le un
                parteneriat: ei recomandă meseriași de încredere, tu le oferi vizibilitate.
            </li>
        </ul>
    </div>

    {{-- 3. Recomandari --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-bold text-gray-800 mb-3">3. Recomandări de la meseriași existenți</h2>
        <p class="text-sm text-gray-700">
            Cel mai ieftin canal, de departe. Meseriașii se cunosc între ei pe zonă/breaslă. La fiecare meseriaș activat,
            întreabă-l dacă știe un coleg de încredere — și notează în <strong>admin_notes</strong> cine a recomandat pe cine,
            ca să poți mulțumi/recompensa mai târziu (ex. o lună de promovare gratuită suplimentară).
        </p>
    </div>

    {{-- 4. Outreach direct --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-bold text-gray-800 mb-3">4. Outreach direct (cel mai eficient pe termen scurt)</h2>
        <ul class="space-y-3 text-sm text-gray-700">
            <li>Sună direct la anunțurile de pe OLX/Publi24 — un apel de 2 minute, explici oferta gratuită, trimiți linkul.</li>
            <li>Trimite mesaj WhatsApp scurt și direct, cu linkul formularului: „Bună, ai vrea clienți noi din {oraș}? Primele 10 locuri sunt gratuite pe meseriasionline.ro/inscriere-meserias".</li>
            <li>Vizite scurte la șantiere/magazine de specialitate din zona pe care vrei să o acoperi.</li>
        </ul>
    </div>

    {{-- 5. Follow-up sistematic --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-bold text-gray-800 mb-3">5. Follow-up sistematic (nu lăsa lead-uri să se piardă)</h2>
        <p class="text-sm text-gray-700 mb-3">
            Folosește pipeline-ul din <a href="{{ route('admin.leads.index') }}" class="text-primary-600 underline">Recrutare Meseriași</a>
            pentru fiecare contact: <strong>Nou → Contactat → Invitat → Înregistrat</strong>. Nu lăsa lead-uri fără status actualizat —
            sistemul te anunță automat prin email dacă un lead rămâne "Nou" mai mult de 2 zile, ca să nu uiți de nimeni.
        </p>
        <p class="text-sm text-gray-700">
            După ce trimiți o invitație, dacă meseriașul nu răspunde în câteva zile, revino cu un mesaj scurt de reamintire —
            majoritatea conversiilor vin din al doilea sau al treilea contact, nu din primul.
        </p>
    </div>

    {{-- 6. Masurare --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-bold text-gray-800 mb-3">6. Vezi ce funcționează</h2>
        <p class="text-sm text-gray-700">
            În pagina de detaliu a fiecărui lead apare sursa lui (formular public sau adăugat manual de tine). Urmărește
            periodic care meserii au cei mai mulți convertiți față de total (vezi cardurile din
            <a href="{{ route('admin.leads.index') }}" class="text-primary-600 underline">lista de lead-uri</a>) și investește
            mai mult timp în canalul/meseria care aduce cele mai multe conturi create.
        </p>
    </div>

</div>

@endsection
