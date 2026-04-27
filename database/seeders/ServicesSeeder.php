<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\User;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Servicii Intretinere Imobile
        $intretinereCategory = \App\Models\Category::where('slug', 'intretinere-imobile')->first();
        $intretinereServices = [
                [
                    'name' => 'Curatenie generala imobil',
                    'description' => 'Curatenie completa pentru blocuri, case, birouri si spatii comerciale.',
                    'price' => 300,
                    'duration' => 180,
                ],
                [
                    'name' => 'Verificare tehnica instalatii',
                    'description' => 'Verificare si mentenanta instalatii electrice, sanitare, termice.',
                    'price' => 150,
                    'duration' => 90,
                ],
                [
                    'name' => 'Reparatii minore imobil',
                    'description' => 'Reparatii rapide pentru usi, ferestre, pereti, instalatii.',
                    'price' => 120,
                    'duration' => 60,
                ],
            ];

        $defaultUser = \App\Models\User::where('role', 'specialist')->first();
        foreach ($intretinereServices as $service) {
            Service::create(array_merge($service, [
                'user_id' => $defaultUser ? $defaultUser->id : 1,
                'category_id' => $intretinereCategory ? $intretinereCategory->id : null,
                'sub_brand' => null,
                'is_on_location' => true,
                'is_active' => true,
            ]));
        }

        // Servicii Mentenanta
        $mentenantaCategory = \App\Models\Category::where('slug', 'mentenanta')->first();
        $mentenantaServices = [
                [
                    'name' => 'Mentenanta instalatii electrice',
                    'description' => 'Verificare, testare si mentenanta instalatii electrice pentru imobile.',
                    'price' => 200,
                    'duration' => 120,
                ],
                [
                    'name' => 'Mentenanta sisteme securitate',
                    'description' => 'Mentenanta si verificare sisteme de alarma, supraveghere video, interfon.',
                    'price' => 180,
                    'duration' => 90,
                ],
                [
                    'name' => 'Mentenanta echipamente HVAC',
                    'description' => 'Intretinere si reparatii pentru centrale termice, aer conditionat, ventilatie.',
                    'price' => 250,
                    'duration' => 120,
                ],
            ];

        foreach ($mentenantaServices as $service) {
            Service::create(array_merge($service, [
                'user_id' => $defaultUser ? $defaultUser->id : 1,
                'category_id' => $mentenantaCategory ? $mentenantaCategory->id : null,
                'sub_brand' => null,
                'is_on_location' => true,
                'is_active' => true,
            ]));
        }
        // Get specialists for each sub-brand
        $nailsSpecialist = User::where('sub_brand', 'dariaNails')->where('role', 'specialist')->first();
        $hairSpecialist = User::where('sub_brand', 'dariaHair')->where('role', 'specialist')->first();
        $glowSpecialist = User::where('sub_brand', 'dariaGlow')->where('role', 'specialist')->first();

        // dariaNails Services
        if ($nailsSpecialist) {
            $nailsServices = [
                // Manichiura
                [
                    'name' => 'Manichiură clasică',
                    'description' => 'Îngrijire completă a unghiilor naturale cu lac clasic',
                    'price' => 50,
                    'duration' => 45,
                    'category' => 'Manichiură',
                ],
                [
                    'name' => 'Manichiură gel',
                    'description' => 'Manichiură cu lac semipermanent, rezistent până la 3 săptămâni',
                    'price' => 80,
                    'duration' => 60,
                    'category' => 'Manichiură',
                ],
                [
                    'name' => 'French manichiură',
                    'description' => 'Manichiură elegantă în stil french',
                    'price' => 90,
                    'duration' => 60,
                    'category' => 'Manichiură',
                ],
                [
                    'name' => 'Întreținere gel',
                    'description' => 'Întreținere și reîmprospătare manichiură gel',
                    'price' => 70,
                    'duration' => 45,
                    'category' => 'Manichiură',
                ],
                // Pedichiura
                [
                    'name' => 'Pedichiură clasică',
                    'description' => 'Îngrijire completă a picioarelor și unghiilor',
                    'price' => 70,
                    'duration' => 60,
                    'category' => 'Pedichiură',
                ],
                [
                    'name' => 'Pedichiură SPA',
                    'description' => 'Pedichiură luxoasă cu tratament de relaxare',
                    'price' => 120,
                    'duration' => 90,
                    'category' => 'Pedichiură',
                ],
                [
                    'name' => 'Pedichiură gel',
                    'description' => 'Pedichiură cu lac semipermanent',
                    'price' => 100,
                    'duration' => 75,
                    'category' => 'Pedichiură',
                ],
                // Nail Art
                [
                    'name' => 'Nail art simplu',
                    'description' => 'Design personalizat pe 2-4 unghii',
                    'price' => 30,
                    'duration' => 30,
                    'category' => 'Nail Art',
                ],
                [
                    'name' => 'Nail art avansat',
                    'description' => 'Design complex pe toate unghiile',
                    'price' => 80,
                    'duration' => 60,
                    'category' => 'Nail Art',
                ],
                [
                    'name' => 'Aplicare strasuri',
                    'description' => 'Decorare cu strasuri și accesorii',
                    'price' => 20,
                    'duration' => 20,
                    'category' => 'Nail Art',
                ],
            ];

            foreach ($nailsServices as $service) {
                Service::create(array_merge($service, [
                    'user_id' => $nailsSpecialist->id,
                    'sub_brand' => 'dariaNails',
                    'is_mobile' => true,
                    'is_active' => true,
                ]));
            }
        }

        // dariaHair Services
        if ($hairSpecialist) {
            $hairServices = [
                // Tunsori
                [
                    'name' => 'Tunsoare feminină',
                    'description' => 'Tunsoare personalizată în funcție de forma feței',
                    'price' => 100,
                    'duration' => 60,
                    'category' => 'Tunsori',
                ],
                [
                    'name' => 'Tunsoare + styling',
                    'description' => 'Tunsoare completă cu coafură finală',
                    'price' => 150,
                    'duration' => 90,
                    'category' => 'Tunsori',
                ],
                [
                    'name' => 'Refresh tunsoare',
                    'description' => 'Reîmprospătare și egalizare păr',
                    'price' => 70,
                    'duration' => 45,
                    'category' => 'Tunsori',
                ],
                // Styling & Coafuri
                [
                    'name' => 'Coafură simplă',
                    'description' => 'Styling pentru zi sau seri casual',
                    'price' => 80,
                    'duration' => 45,
                    'category' => 'Styling',
                ],
                [
                    'name' => 'Coafură eveniment',
                    'description' => 'Coafură elegantă pentru nunți, botezuri',
                    'price' => 200,
                    'duration' => 120,
                    'category' => 'Styling',
                ],
                [
                    'name' => 'Ondulare / Pliere',
                    'description' => 'Ondulare sau întindere păr cu placa',
                    'price' => 60,
                    'duration' => 45,
                    'category' => 'Styling',
                ],
                // Colorare
                [
                    'name' => 'Colorare completă',
                    'description' => 'Vopsire completă cu culoare nouă',
                    'price' => 250,
                    'duration' => 180,
                    'category' => 'Colorare',
                ],
                [
                    'name' => 'Suvițe / Șatușe',
                    'description' => 'Suvițe balayage sau șatușe naturale',
                    'price' => 300,
                    'duration' => 210,
                    'category' => 'Colorare',
                ],
                [
                    'name' => 'Retușare rădăcini',
                    'description' => 'Acoperire rădăcini crescute',
                    'price' => 150,
                    'duration' => 120,
                    'category' => 'Colorare',
                ],
                // Tratamente
                [
                    'name' => 'Tratament restructurant',
                    'description' => 'Tratament intensiv pentru păr deteriorat',
                    'price' => 100,
                    'duration' => 60,
                    'category' => 'Tratamente',
                ],
                [
                    'name' => 'Keratinizare',
                    'description' => 'Tratament cu keratină pentru netezire',
                    'price' => 400,
                    'duration' => 240,
                    'category' => 'Tratamente',
                ],
            ];

            foreach ($hairServices as $service) {
                Service::create(array_merge($service, [
                    'user_id' => $hairSpecialist->id,
                    'sub_brand' => 'dariaHair',
                    'is_mobile' => true,
                    'is_active' => true,
                ]));
            }
        }

        // dariaGlow Services
        if ($glowSpecialist) {
            $glowServices = [
                // Tratamente faciale
                [
                    'name' => 'Curățare facială clasică',
                    'description' => 'Curățare profundă a pielii feței',
                    'price' => 150,
                    'duration' => 60,
                    'category' => 'Tratamente faciale',
                ],
                [
                    'name' => 'Curățare facială profundă',
                    'description' => 'Curățare cu extractie și mască',
                    'price' => 200,
                    'duration' => 90,
                    'category' => 'Tratamente faciale',
                ],
                [
                    'name' => 'Tratament anti-aging',
                    'description' => 'Tratament pentru reducerea ridurilor',
                    'price' => 300,
                    'duration' => 90,
                    'category' => 'Tratamente faciale',
                ],
                [
                    'name' => 'Hidratare intensivă',
                    'description' => 'Tratament de hidratare pentru piele uscată',
                    'price' => 180,
                    'duration' => 75,
                    'category' => 'Tratamente faciale',
                ],
                [
                    'name' => 'Peeling chimic',
                    'description' => 'Exfoliere chimică pentru regenerare celulară',
                    'price' => 250,
                    'duration' => 60,
                    'category' => 'Tratamente faciale',
                ],
                // Machiaj
                [
                    'name' => 'Machiaj de zi',
                    'description' => 'Machiaj natural pentru zi',
                    'price' => 100,
                    'duration' => 45,
                    'category' => 'Machiaj',
                ],
                [
                    'name' => 'Machiaj de seară',
                    'description' => 'Machiaj elegant pentru evenimente',
                    'price' => 150,
                    'duration' => 60,
                    'category' => 'Machiaj',
                ],
                [
                    'name' => 'Machiaj mireasă',
                    'description' => 'Machiaj special pentru nuntă cu probă',
                    'price' => 350,
                    'duration' => 120,
                    'category' => 'Machiaj',
                ],
                [
                    'name' => 'Machiaj artistic',
                    'description' => 'Machiaj creativ pentru sesiuni foto',
                    'price' => 200,
                    'duration' => 90,
                    'category' => 'Machiaj',
                ],
                // Sprancene & Gene
                [
                    'name' => 'Pensat sprâncene',
                    'description' => 'Aranjare și modelare sprâncene',
                    'price' => 40,
                    'duration' => 30,
                    'category' => 'Sprâncene & Gene',
                ],
                [
                    'name' => 'Vopsit sprâncene',
                    'description' => 'Colorare sprâncene cu henă',
                    'price' => 60,
                    'duration' => 45,
                    'category' => 'Sprâncene & Gene',
                ],
                [
                    'name' => 'Extensii gene',
                    'description' => 'Aplicare extensii gene volum clasic',
                    'price' => 200,
                    'duration' => 120,
                    'category' => 'Sprâncene & Gene',
                ],
                [
                    'name' => 'Laminare gene',
                    'description' => 'Laminare și lifting gene naturale',
                    'price' => 150,
                    'duration' => 60,
                    'category' => 'Sprâncene & Gene',
                ],
            ];

            foreach ($glowServices as $service) {
                Service::create(array_merge($service, [
                    'user_id' => $glowSpecialist->id,
                    'sub_brand' => 'dariaGlow',
                    'is_mobile' => true,
                    'is_active' => true,
                ]));
            }
        }

        $this->command->info('Services seeded successfully!');
    }
}
