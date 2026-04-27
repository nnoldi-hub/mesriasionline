@extends('layouts.app')

@section('title', 'Politica de Confidentialitate - Meseriasi Profesionisti')

@section('content')
<div class="bg-white">
    <!-- Hero Section -->
    <section class="bg-linear-to-br from-primary-600 to-primary-700 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl md:text-5xl font-bold text-center mb-4">Politica de Confidentialitate</h1>
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
                    Respectăm confidențialitatea datelor dvs. personale și ne angajăm să protejăm informațiile pe care 
                    ni le furnizați. Această politică descrie modul în care colectăm, utilizăm și protejăm informațiile 
                    dvs. personale conform Regulamentului General privind Protecția Datelor (GDPR).
                </p>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">2. Informatii pe care le colectam</h2>
                
                <h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">2.1 Informatii furnizate direct de dvs.</h3>
                <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
                    <li>Nume si prenume</li>
                    <li>Adresa de email</li>
                    <li>Numar de telefon</li>
                    <li>Adresa (pentru meseriasi)</li>
                    <li>Informatii despre experienta profesionala (pentru meseriasi)</li>
                    <li>Descrierea serviciilor oferite (pentru meseriasi)</li>
                </ul>

                <h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">2.2 Informatii colectate automat</h3>
                <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
                    <li>Adresa IP</li>
                    <li>Tipul de browser</li>
                    <li>Timpul petrecut pe platforma</li>
                    <li>Paginile vizitate</li>
                    <li>Dispozitivul utilizat</li>
                </ul>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">3. Cum utilizam informatiile</h2>
                <p class="text-gray-600 mb-4">Utilizam informatiile colectate pentru:</p>
                <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
                    <li>Furnizarea și îmbunătățirea serviciilor noastre</li>
                    <li>Crearea și gestionarea conturilor utilizatorilor</li>
                    <li>Facilitarea comunicării dintre clienți și meseriași</li>
                    <li>Trimiterea de notificări și actualizări importante</li>
                    <li>Personalizarea experienței utilizatorului</li>
                    <li>Analizarea utilizării platformei pentru îmbunătățiri</li>
                    <li>Prevenirea fraudelor și asigurarea securității</li>
                    <li>Respectarea obligațiilor legale</li>
                </ul>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">4. Partajarea informatiilor</h2>
                <p class="text-gray-600 mb-4">
                    Nu vindem, inchiriem sau comercializam informatiile dvs. personale catre terte parti. 
                    Putem partaja informatii in urmatoarele situatii:
                </p>
                <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
                    <li><strong>Cu meseriasii:</strong> Informatiile de contact ale clientilor sunt partajate cu meseriasii selectati pentru facilitarea serviciilor</li>
                    <li><strong>Cu furnizorii de servicii:</strong> Companii care ne ajuta sa operam platforma (hosting, analiza, suport tehnic)</li>
                    <li><strong>Cerinte legale:</strong> Cand suntem obligati prin lege sau pentru protejarea drepturilor noastre</li>
                </ul>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">5. Cookie-uri si tehnologii similare</h2>
                <p class="text-gray-600 mb-4">
                    Utilizam cookie-uri pentru a imbunatati experienta dvs. pe platforma. Cookie-urile sunt fisiere 
                    mici de text stocate pe dispozitivul dvs. care ne ajuta sa:
                </p>
                <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
                    <li>Pastram sesiunea deschisa cand sunteti autentificat</li>
                    <li>Intelegem cum utilizati platforma</li>
                    <li>Personalizam continutul si reclamele</li>
                    <li>Imbunatatim performanta platformei</li>
                </ul>
                <p class="text-gray-600 mb-4">
                    Puteti configura browserul pentru a refuza cookie-urile, dar acest lucru poate afecta 
                    functionalitatea platformei.
                </p>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">6. Securitatea datelor</h2>
                <p class="text-gray-600 mb-4">
                    Implementam masuri de securitate tehnice si organizationale pentru a proteja datele dvs. impotriva 
                    accesului neautorizat, pierderii sau distrugerii:
                </p>
                <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
                    <li>Criptarea datelor sensibile</li>
                    <li>Acces restricționat la informațiile personale</li>
                    <li>Monitorizarea regulată a sistemelor</li>
                    <li>Actualizări de securitate periodice</li>
                </ul>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">7. Drepturile dvs. conform GDPR</h2>
                <p class="text-gray-600 mb-4">Conform GDPR, aveti urmatoarele drepturi:</p>
                <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
                    <li><strong>Dreptul de acces:</strong> Puteti solicita o copie a datelor personale pe care le detinem</li>
                    <li><strong>Dreptul de rectificare:</strong> Puteti corecta datele inexacte sau incomplete</li>
                    <li><strong>Dreptul la stergere:</strong> Puteti solicita stergerea datelor in anumite conditii</li>
                    <li><strong>Dreptul la restrictionare:</strong> Puteti limita modul in care utilizam datele dvs.</li>
                    <li><strong>Dreptul la portabilitate:</strong> Puteti primi datele intr-un format structurat</li>
                    <li><strong>Dreptul la opozitie:</strong> Puteti refuza anumite prelucrari ale datelor</li>
                </ul>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">8. Pastrarea datelor</h2>
                <p class="text-gray-600 mb-4">
                    Pastram datele dvs. personale atat timp cat este necesar pentru furnizarea serviciilor sau 
                    pentru respectarea obligatiilor legale. Cand un cont este inchis, stergem sau anonimizam 
                    datele personale in termen de 90 de zile, cu exceptia cazului in care avem o obligatie legala 
                    de a le pastra.
                </p>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">9. Transferuri internationale</h2>
                <p class="text-gray-600 mb-4">
                    Datele dvs. sunt stocate si procesate in Romania. Daca transferam date in afara Spatiului 
                    Economic European, ne asiguram ca exista garantii adecvate conform GDPR.
                </p>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">10. Minori</h2>
                <p class="text-gray-600 mb-4">
                    Platforma noastra nu este destinata persoanelor sub 18 ani. Nu colectam in mod constient 
                    informatii de la minori. Daca descoperim ca am colectat date de la un minor, vom sterge 
                    imediat aceste informatii.
                </p>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">11. Modificari ale politicii</h2>
                <p class="text-gray-600 mb-4">
                    Ne rezervam dreptul de a actualiza aceasta politica de confidentialitate. Va vom notifica 
                    despre modificari prin publicarea noii politici pe platforma si actualizarea datei de 
                    "Ultima actualizare". Va incurajam sa revedeti periodic aceasta politica.
                </p>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">12. Contact si DPO</h2>
                <p class="text-gray-600 mb-4">
                    Pentru intrebari legate de aceasta politica sau pentru exercitarea drepturilor dvs., ne puteti 
                    contacta la:
                </p>
                <p class="text-gray-600 mb-4">
                    <strong>Data Protection Officer (DPO):</strong><br>
                    Email: <a href="mailto:privacy@meseriasi.ro" class="text-primary-600 hover:underline">privacy@meseriasi.ro</a><br>
                    Telefon: +40 720 123 456<br>
                    Adresa: Str. Exemplu Nr. 123, Bucuresti, Romania
                </p>
                <p class="text-gray-600 mb-4">
                    De asemenea, aveti dreptul de a depune o plangere la Autoritatea Nationala de Supraveghere 
                    a Prelucrarii Datelor cu Caracter Personal (ANSPDCP).
                </p>

                <div class="bg-secondary-100 p-6 rounded-lg mt-8">
                    <p class="text-gray-600">
                        <strong>Consimtamant:</strong> Prin utilizarea platformei Meseriasi Profesionisti, confirmati 
                        ca ati citit si inteles aceasta politica de confidentialitate si consimtiti la prelucrarea 
                        datelor dvs. personale conform celor descrise mai sus.
                    </p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
