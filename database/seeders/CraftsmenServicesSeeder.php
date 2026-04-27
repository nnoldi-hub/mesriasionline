<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\User;
use App\Models\Category;

class CraftsmenServicesSeeder extends Seeder
{
    public function run()
    {
        $craftsmen = User::where('role', 'specialist')->with('category')->get();

        if ($craftsmen->isEmpty()) {
            $this->command->info('Rulează mai întâi CraftsmenSeeder!');
            return;
        }

        foreach ($craftsmen as $craftsman) {
            $this->createServicesForCraftsman($craftsman);
        }

        $this->command->info('Servicii create cu succes!');
    }

    private function createServicesForCraftsman($craftsman)
    {
        $categorySlug = $craftsman->category->slug;

        switch ($categorySlug) {
            case 'electrician':
                $this->createElectricianServices($craftsman);
                break;
            case 'instalator':
                $this->createInstalatorServices($craftsman);
                break;
            case 'tamplar':
                $this->createTamplarServices($craftsman);
                break;
            case 'zugrav':
                $this->createZugravServices($craftsman);
                break;
            case 'zidar':
                $this->createZidarServices($craftsman);
                break;
            case 'faiantar':
                $this->createFaiantarServices($craftsman);
                break;
            case 'frigotehnist':
                $this->createFrigotehnistServices($craftsman);
                break;
            case 'mecanic-auto':
                $this->createMecanicAutoServices($craftsman);
                break;
        }
    }

    private function createElectricianServices($craftsman)
    {
        $services = [
            [
                'name' => 'Instalare priză electrică',
                'description' => 'Montaj priză nouă cu protecție și verificare instalație',
                'detailed_description' => 'Serviciul include montaj priză electrică cu verificarea instalației electrice existente, testare și punere în funcțiune. Materiale incluse: priză, cutie montaj, cleme conexiune.',
                'price' => 80,
                'pricing_type' => 'fixed',
                'duration' => 60,
                'complexity' => 'easy',
                'materials_included' => true,
                'is_on_location' => true
            ],
            [
                'name' => 'Instalare tablou electric',
                'description' => 'Montaj tablou electric complet cu siguranțe automate',
                'detailed_description' => 'Montaj tablou electric nou sau înlocuire tablou vechi. Include cablare, siguranțe automate, releu diferențial, etichetare circuite.',
                'price' => 350,
                'min_price' => 300,
                'max_price' => 600,
                'pricing_type' => 'per_project',
                'duration' => 240,
                'min_duration' => 180,
                'max_duration' => 300,
                'complexity' => 'medium',
                'materials_included' => false,
                'client_provides_materials' => true,
                'is_on_location' => true
            ],
            [
                'name' => 'Instalație electrică completă apartament',
                'description' => 'Proiectare și executare instalație electrică nouă pentru apartament 2-3 camere',
                'detailed_description' => 'Serviciu complet de instalație electrică pentru apartament: proiectare, trasare circuite, montaj cabluri, tablou electric, prize, întrerupătoare, corpuri iluminat.',
                'min_price' => 2000,
                'max_price' => 5000,
                'pricing_type' => 'per_project',
                'min_duration' => 480,
                'max_duration' => 960,
                'complexity' => 'hard',
                'materials_included' => false,
                'client_provides_materials' => false,
                'is_on_location' => true
            ],
            [
                'name' => 'Depanare electrică urgentă',
                'description' => 'Intervenție rapidă pentru probleme electrice urgente',
                'detailed_description' => 'Serviciu de urgență 24/7 pentru probleme electrice: pierdere curent, scurtcircuit, siguranțe arse, prize defecte.',
                'price' => 150,
                'pricing_type' => 'hourly',
                'duration' => 120,
                'complexity' => 'medium',
                'materials_included' => false,
                'is_on_location' => true
            ]
        ];

        foreach ($services as $serviceData) {
            $serviceData['user_id'] = $craftsman->id;
            $serviceData['category_id'] = $craftsman->category_id;
            $serviceData['is_active'] = true;
            Service::create($serviceData);
        }
    }

    private function createInstalatorServices($craftsman)
    {
        $services = [
            [
                'name' => 'Montaj chiuvetă baie/bucătărie',
                'description' => 'Instalare chiuvetă completă cu racorduri apă și scurgere',
                'detailed_description' => 'Montaj chiuvetă cu baterie, racordare la țevi apă caldă/rece, montaj sifon și racordare canalizare. Include testare etanșeitate.',
                'price' => 150,
                'pricing_type' => 'fixed',
                'duration' => 120,
                'complexity' => 'easy',
                'materials_included' => false,
                'client_provides_materials' => true,
                'is_on_location' => true
            ],
            [
                'name' => 'Instalare centrală termică',
                'description' => 'Montaj și punere în funcțiune centrală termică murală',
                'detailed_description' => 'Serviciu complet instalare centrală termică: montaj perete, racordare gaze, apă, evacuare fum, programare parametri, test funcționare. Necesită autorizație ISCIR.',
                'min_price' => 500,
                'max_price' => 1000,
                'pricing_type' => 'per_project',
                'duration' => 480,
                'min_duration' => 360,
                'max_duration' => 600,
                'complexity' => 'hard',
                'materials_included' => false,
                'is_on_location' => true
            ],
            [
                'name' => 'Desfundare canalizare',
                'description' => 'Desfundare țevi canalizare cu spirală electrică',
                'detailed_description' => 'Intervenție pentru desfundare canalizare baie sau bucătărie folosind echipament profesional (spirală electrică). Verificare și testare scurgere.',
                'price' => 200,
                'pricing_type' => 'fixed',
                'duration' => 90,
                'complexity' => 'easy',
                'materials_included' => true,
                'is_on_location' => true
            ],
            [
                'name' => 'Instalație sanitară completă baie',
                'description' => 'Execuție instalație sanitară nouă pentru baie completă',
                'detailed_description' => 'Instalație sanitară completă: țevi apă caldă/rece, canalizare, montaj căzi/cabină duș, chiuvetă, vas WC, baterii. Include testare etanșeitate și probe presiune.',
                'min_price' => 1500,
                'max_price' => 3500,
                'pricing_type' => 'per_project',
                'min_duration' => 480,
                'max_duration' => 960,
                'complexity' => 'hard',
                'materials_included' => false,
                'is_on_location' => true
            ]
        ];

        foreach ($services as $serviceData) {
            $serviceData['user_id'] = $craftsman->id;
            $serviceData['category_id'] = $craftsman->category_id;
            $serviceData['is_active'] = true;
            Service::create($serviceData);
        }
    }

    private function createTamplarServices($craftsman)
    {
        $services = [
            [
                'name' => 'Montaj ușă interior',
                'description' => 'Montaj ușă interioară cu toc și balamale',
                'detailed_description' => 'Instalare ușă interioară completă: montaj toc, nivelare, fixare ușă, montaj balamale, încuietoare și mânere. Include reglaj și finisaje.',
                'price' => 250,
                'pricing_type' => 'fixed',
                'duration' => 180,
                'complexity' => 'medium',
                'materials_included' => false,
                'client_provides_materials' => true,
                'is_on_location' => true
            ],
            [
                'name' => 'Mobilier bucătărie la comandă',
                'description' => 'Proiectare și realizare mobilier bucătărie personalizat',
                'detailed_description' => 'Serviciu complet mobilier bucătărie: măsurători, proiect 3D, fabricare corpuri, blat, montaj la locație, racordări. Materiale premium (PAL melaminat, MDF vopsit).',
                'min_price' => 3000,
                'max_price' => 8000,
                'pricing_type' => 'per_project',
                'min_duration' => 960,
                'max_duration' => 1920,
                'complexity' => 'hard',
                'materials_included' => true,
                'is_on_location' => true
            ],
            [
                'name' => 'Montaj parchet laminat',
                'description' => 'Instalare parchet laminat cu finisaje complete',
                'detailed_description' => 'Montaj parchet laminat: pregătire suprafață, montaj folie protecție, instalare lambriuri, plinte și finisaje. Preț pe mp.',
                'price' => 35,
                'pricing_type' => 'per_project',
                'duration' => 480,
                'complexity' => 'medium',
                'materials_included' => false,
                'is_on_location' => true
            ],
            [
                'name' => 'Reparații mobilier lemn',
                'description' => 'Reparații diverse mobilier din lemn masiv sau PAL',
                'detailed_description' => 'Servicii reparații mobilier: înlocuire balamale, sertare, reglaje uși, recondiționare suprafețe, vopsire, retuș.',
                'price' => 80,
                'pricing_type' => 'hourly',
                'duration' => 120,
                'complexity' => 'easy',
                'materials_included' => false,
                'is_on_location' => true
            ]
        ];

        foreach ($services as $serviceData) {
            $serviceData['user_id'] = $craftsman->id;
            $serviceData['category_id'] = $craftsman->category_id;
            $serviceData['is_active'] = true;
            Service::create($serviceData);
        }
    }

    private function createZugravServices($craftsman)
    {
        $services = [
            [
                'name' => 'Zugrăvit cameră',
                'description' => 'Zugrăvit completă o cameră cu vopsea lavabilă',
                'detailed_description' => 'Serviciu zugrăvit cameră: pregătire suprafață, șpacluire fisuri, șlefuire, grund, 2 straturi vopsea lavabilă calitate premium. Preț pe mp.',
                'price' => 25,
                'pricing_type' => 'per_project',
                'duration' => 480,
                'min_duration' => 360,
                'max_duration' => 600,
                'complexity' => 'medium',
                'materials_included' => true,
                'is_on_location' => true
            ],
            [
                'name' => 'Zugrăvit apartament 2 camere',
                'description' => 'Zugrăveli complete apartament decomandat',
                'detailed_description' => 'Zugrăvit integral apartament: pereți, tavane, pregătire suprafețe, șpacluire, vopsit 2 straturi. Include protecție mobilier și curățenie finală.',
                'min_price' => 1500,
                'max_price' => 2500,
                'pricing_type' => 'per_project',
                'min_duration' => 960,
                'max_duration' => 1440,
                'complexity' => 'medium',
                'materials_included' => true,
                'is_on_location' => true
            ],
            [
                'name' => 'Tapet decorativ',
                'description' => 'Aplicare tapet decorativ și finisaje speciale',
                'detailed_description' => 'Montaj tapet decorativ: pregătire pereți, aplicare adeziv special, montaj tapet, finisare îmbinări. Lucrez cu toate tipurile de tapet.',
                'price' => 40,
                'pricing_type' => 'per_project',
                'duration' => 360,
                'complexity' => 'medium',
                'materials_included' => false,
                'client_provides_materials' => true,
                'is_on_location' => true
            ]
        ];

        foreach ($services as $serviceData) {
            $serviceData['user_id'] = $craftsman->id;
            $serviceData['category_id'] = $craftsman->category_id;
            $serviceData['is_active'] = true;
            Service::create($serviceData);
        }
    }

    private function createZidarServices($craftsman)
    {
        $services = [
            [
                'name' => 'Zidărie pereți despărțitori',
                'description' => 'Construcție pereți despărțitori din BCA sau cărămidă',
                'detailed_description' => 'Execuție pereți despărțitori interiori: marcaj, zidire BCA/cărămidă, tencuială. Preț pe mp. Include materiale de bază și transport.',
                'price' => 120,
                'pricing_type' => 'per_project',
                'min_duration' => 480,
                'max_duration' => 960,
                'complexity' => 'medium',
                'materials_included' => false,
                'is_on_location' => true
            ],
            [
                'name' => 'Tencuială interioară',
                'description' => 'Executare tencuială interioară mecanizată sau manuală',
                'detailed_description' => 'Tencuială interioară pentru pereți și tavane. Execuție cu mortar pre-amestecat, nivelare, gletare. Preț pe mp.',
                'price' => 35,
                'pricing_type' => 'per_project',
                'duration' => 480,
                'complexity' => 'medium',
                'materials_included' => false,
                'is_on_location' => true
            ],
            [
                'name' => 'Demolare pereți',
                'description' => 'Demolări pereți despărțitori, evacuare moloz',
                'detailed_description' => 'Serviciu demolare pereți interiori cu evacuare moloz. Include protecție restul locuinței și curățenie după demolare.',
                'price' => 80,
                'pricing_type' => 'per_project',
                'duration' => 360,
                'complexity' => 'easy',
                'materials_included' => true,
                'is_on_location' => true
            ]
        ];

        foreach ($services as $serviceData) {
            $serviceData['user_id'] = $craftsman->id;
            $serviceData['category_id'] = $craftsman->category_id;
            $serviceData['is_active'] = true;
            Service::create($serviceData);
        }
    }

    private function createFaiantarServices($craftsman)
    {
        $services = [
            [
                'name' => 'Montaj faianță baie',
                'description' => 'Montaj faianță completă pentru baie standard',
                'detailed_description' => 'Montaj faianță pereți baie: nivelare suport, aplicare adeziv, montaj faianță, rosturi, curățare. Preț pe mp. Materiale adezive incluse.',
                'price' => 70,
                'pricing_type' => 'per_project',
                'duration' => 480,
                'complexity' => 'medium',
                'materials_included' => true,
                'client_provides_materials' => true,
                'is_on_location' => true
            ],
            [
                'name' => 'Montaj gresie apartament',
                'description' => 'Montaj gresie pentru toate camerele apartamentului',
                'detailed_description' => 'Montaj gresie la sol: nivelare șapă, aplicare adeziv, montaj gresie, rosturi, curățare. Include tăieri și finisaje speciale. Preț pe mp.',
                'price' => 60,
                'pricing_type' => 'per_project',
                'min_duration' => 960,
                'max_duration' => 1920,
                'complexity' => 'medium',
                'materials_included' => true,
                'is_on_location' => true
            ],
            [
                'name' => 'Montaj mozaic decorativ',
                'description' => 'Aplicare mozaic pentru decorațiuni speciale',
                'detailed_description' => 'Montaj mozaic decorativ pentru baie, bucătărie sau alte spații. Lucru de precizie, atenție la detalii. Preț pe mp.',
                'price' => 120,
                'pricing_type' => 'per_project',
                'duration' => 480,
                'complexity' => 'hard',
                'materials_included' => true,
                'is_on_location' => true
            ]
        ];

        foreach ($services as $serviceData) {
            $serviceData['user_id'] = $craftsman->id;
            $serviceData['category_id'] = $craftsman->category_id;
            $serviceData['is_active'] = true;
            Service::create($serviceData);
        }
    }

    private function createFrigotehnistServices($craftsman)
    {
        $services = [
            [
                'name' => 'Instalare aer condiționat',
                'description' => 'Montaj complet aer condiționat split 9000-12000 BTU',
                'detailed_description' => 'Instalare completă AC split: montaj unități interioară/exterioară, trasare țevi, vid instalație, încărcare freon, testare funcționare. Include materiale montaj.',
                'price' => 350,
                'pricing_type' => 'fixed',
                'duration' => 240,
                'complexity' => 'medium',
                'materials_included' => true,
                'client_provides_materials' => true,
                'is_on_location' => true
            ],
            [
                'name' => 'Service aer condiționat',
                'description' => 'Curățare și verificare completă sistem AC',
                'detailed_description' => 'Service preventiv AC: curățare filtre, baterie, verificare presiune freon, control sistem electric, testare funcționare. Recomandat anual.',
                'price' => 150,
                'pricing_type' => 'fixed',
                'duration' => 120,
                'complexity' => 'easy',
                'materials_included' => true,
                'is_on_location' => true
            ],
            [
                'name' => 'Reparație frigider/congelator',
                'description' => 'Diagnoză și reparații aparate frigorifice',
                'detailed_description' => 'Serviciu reparații frigidere și congelatoare: diagnoză defecțiune, înlocuire piese defecte, încărcare freon dacă e necesar. Deplasare și diagnoză incluse.',
                'min_price' => 150,
                'max_price' => 500,
                'pricing_type' => 'per_project',
                'duration' => 180,
                'complexity' => 'medium',
                'materials_included' => false,
                'is_on_location' => true
            ]
        ];

        foreach ($services as $serviceData) {
            $serviceData['user_id'] = $craftsman->id;
            $serviceData['category_id'] = $craftsman->category_id;
            $serviceData['is_active'] = true;
            Service::create($serviceData);
        }
    }

    private function createMecanicAutoServices($craftsman)
    {
        $services = [
            [
                'name' => 'Schimb ulei și filtre',
                'description' => 'Service complet schimb ulei motor și filtre',
                'detailed_description' => 'Serviciu mobil schimb ulei motor: scurgere ulei uzat, înlocuire filtru ulei, completare ulei nou (specific mașinii), verificare nivel lichide, reset service. Vin la domiciliul tău.',
                'price' => 150,
                'pricing_type' => 'fixed',
                'duration' => 60,
                'complexity' => 'easy',
                'materials_included' => false,
                'client_provides_materials' => true,
                'is_on_location' => true
            ],
            [
                'name' => 'Diagnoză electronică auto',
                'description' => 'Diagnoză completă cu tester profesional',
                'detailed_description' => 'Diagnoză electronică completă: citire erori calculator, verificare senzori, actuatori, sistem injecție, ABS, airbag. Raport detaliat.',
                'price' => 100,
                'pricing_type' => 'fixed',
                'duration' => 90,
                'complexity' => 'easy',
                'materials_included' => true,
                'is_on_location' => true
            ],
            [
                'name' => 'Înlocuire baterie auto',
                'description' => 'Schimb baterie auto cu testare sistem încărcare',
                'detailed_description' => 'Serviciu mobil înlocuire baterie: demontare baterie veche, montare baterie nouă, verificare alternator și sistem încărcare, testare pornire.',
                'price' => 50,
                'pricing_type' => 'fixed',
                'duration' => 30,
                'complexity' => 'easy',
                'materials_included' => false,
                'client_provides_materials' => true,
                'is_on_location' => true
            ],
            [
                'name' => 'Depanare și asistență rutieră',
                'description' => 'Intervenție urgentă pentru mașini care nu pornesc',
                'detailed_description' => 'Serviciu urgență 24/7: depanare la fața locului, pornire mașină, reparații minore urgente. Deplasare rapidă în zonă.',
                'price' => 200,
                'pricing_type' => 'fixed',
                'duration' => 120,
                'complexity' => 'medium',
                'materials_included' => true,
                'is_on_location' => true
            ]
        ];

        foreach ($services as $serviceData) {
            $serviceData['user_id'] = $craftsman->id;
            $serviceData['category_id'] = $craftsman->category_id;
            $serviceData['is_active'] = true;
            Service::create($serviceData);
        }
    }
}
