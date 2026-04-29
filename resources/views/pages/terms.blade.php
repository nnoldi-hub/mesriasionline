@extends('layouts.app')

@section('title', 'Termeni si Conditii - Meseriasi Profesionisti')

@section('content')
<div class="bg-white">
    <!-- Hero Section -->
    <section class="bg-linear-to-br from-primary-600 to-primary-700 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl md:text-5xl font-bold text-center mb-4">Termeni si Conditii</h1>
            <p class="text-xl text-center text-secondary-100">
                Ultima actualizare: {{ date('d.m.Y') }}
            </p>
        </div>
    </section>

    <!-- Content -->
    <section class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="prose prose-lg max-w-none">
                
                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">1. Introducere</h2>
                <p class="text-gray-600 mb-4">
                    Bine ați venit pe platforma Meseriași Profesioniști. Prin accesarea și utilizarea acestui site web, 
                    acceptați să respectați și să fiți obligat de următorii termeni și condiții de utilizare.
                </p>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">2. Definiții</h2>
                <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
                    <li><strong>Platformă</strong> - site-ul web Meseriași Profesioniști și toate serviciile aferente</li>
                    <li><strong>Utilizator</strong> - orice persoană care accesează platforma</li>
                    <li><strong>Meseriaș</strong> - profesionist înregistrat care oferă servicii prin platformă</li>
                    <li><strong>Client</strong> - utilizator care caută serviciile unui meseriaș</li>
                </ul>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">3. Utilizarea Platformei</h2>
                <h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">3.1 Condiții Generale</h3>
                <p class="text-gray-600 mb-4">
                    Pentru a utiliza platforma, trebuie să aveți cel puțin 18 ani și să aveți capacitatea juridică 
                    de a încheia contracte. Prin utilizarea platformei, confirmați că îndepliniți aceste cerințe.
                </p>

                <h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">3.2 Cont de Utilizator</h3>
                <p class="text-gray-600 mb-4">
                    Pentru anumite funcționalități, este necesar să creați un cont. Sunteți responsabil pentru 
                    menținerea confidențialității contului și parolei dvs. și pentru restricționarea accesului la 
                    computerul dvs.
                </p>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">4. Obligațiile Meseriașilor</h2>
                <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
                    <li>Să furnizeze informații corecte și actualizate despre serviciile oferite</li>
                    <li>Să dețină autorizațiile și certificările necesare pentru serviciile oferite</li>
                    <li>Să mențină un standard ridicat de profesionalism în interacțiunile cu clienții</li>
                    <li>Să respecte termenii și condițiile stabilite cu fiecare client</li>
                    <li>Să răspundă prompt la solicitările clienților</li>
                </ul>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">5. Obligațiile Clienților</h2>
                <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
                    <li>Să furnizeze informații corecte despre serviciile necesare</li>
                    <li>Să trateze meseriașii cu respect și profesionalism</li>
                    <li>Să respecte termenii de plată agreați</li>
                    <li>Să furnizeze feedback onest și constructiv</li>
                </ul>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">6. Prețuri și Plăți</h2>
                <p class="text-gray-600 mb-4">
                    Prețurile serviciilor sunt stabilite direct de către meseriași. Platforma nu este responsabilă 
                    pentru tranzacțiile financiare dintre clienți și meseriași. Toate plățile se efectuează direct 
                    către meseriaș, conform acordului stabilit între părți.
                </p>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">7. Recenzii și Evaluări</h2>
                <p class="text-gray-600 mb-4">
                    Clienții pot lăsa recenzii și evaluări pentru serviciile primite. Recenziile trebuie să fie 
                    oneste, constructive și bazate pe experiențe reale. Ne rezervăm dreptul de a elimina recenziile 
                    care încalcă acești termeni sau care conțin limbaj ofensator.
                </p>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">8. Limitarea raspunderii</h2>
                <p class="text-gray-600 mb-4">
                    Platforma actioneaza doar ca intermediar intre clienti si meseriasi. Nu suntem responsabili pentru:
                </p>
                <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
                    <li>Calitatea serviciilor furnizate de meseriasi</li>
                    <li>Orice daune sau pierderi rezultate din serviciile prestate</li>
                    <li>Disputele dintre clienti si meseriasi</li>
                    <li>Acuratetea informatiilor furnizate de utilizatori</li>
                </ul>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">9. Proprietate intelectuala</h2>
                <p class="text-gray-600 mb-4">
                    Tot continutul platformei, inclusiv dar fara a se limita la texte, grafice, logo-uri, imagini, 
                    este proprietatea noastra sau a furnizorilor nostri de continut si este protejat de legile 
                    privind drepturile de autor.
                </p>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">10. Rezilierea</h2>
                <p class="text-gray-600 mb-4">
                    Ne rezervam dreptul de a suspenda sau inchide contul oricarui utilizator care incalca acesti 
                    termeni si conditii, fara notificare prealabila.
                </p>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">11. Modificari ale termenilor</h2>
                <p class="text-gray-600 mb-4">
                    Ne rezervam dreptul de a modifica acesti termeni si conditii in orice moment. Modificarile vor 
                    intra in vigoare imediat dupa publicarea lor pe platforma. Utilizarea continua a platformei dupa 
                    publicarea modificarilor constituie acceptarea noilor termeni.
                </p>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">12. Legea aplicabila</h2>
                <p class="text-gray-600 mb-4">
                    Acesti termeni si conditii sunt guvernati de legile Romaniei. Orice disputa va fi solutionata 
                    de instantele competente din Romania.
                </p>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">13. Contact</h2>
                <p class="text-gray-600 mb-4">
                    Pentru intrebari sau nelamuriri legate de acesti termeni si conditii, ne puteti contacta la:
                </p>
                <p class="text-gray-600 mb-4">
                    Email: <a href="mailto:contact@meseriasionline.ro" class="text-primary-600 hover:underline">contact@meseriasionline.ro</a><br>
                    Telefon: 0740 173 581
                </p>

                <div class="bg-secondary-100 p-6 rounded-lg mt-8">
                    <p class="text-gray-600">
                        <strong>Nota:</strong> Prin utilizarea platformei Meseriasi Profesionisti, confirmati ca ati citit, 
                        inteles si acceptat acesti termeni si conditii in intregime.
                    </p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
