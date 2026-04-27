<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CraftsmenSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::all();
        $locations = Location::all();

        if ($categories->isEmpty() || $locations->isEmpty()) {
            $this->command->info('Rulează mai întâi CategoriesSeeder și LocationsSeeder!');
            return;
        }

        $craftsmen = [
            // Electricieni
            [
                'name' => 'Ion Popescu',
                'email' => 'ion.popescu@example.com',
                'phone' => '0721234567',
                'category_id' => $categories->where('slug', 'electrician')->first()->id,
                'location_id' => $locations->where('slug', 'bucuresti')->first()->id,
                'specialization' => 'Instalații electrice rezidențiale și comerciale',
                'experience_years' => 15,
                'description' => 'Electrician autorizat ANRE cu 15 ani experiență în instalații electrice pentru apartamente, case și spații comerciale. Intervenții rapide, lucrări de calitate.',
                'certifications' => ['ANRE', 'PSI', 'Protecția muncii'],
                'has_insurance' => true,
                'company_name' => 'Electrician Pro SRL',
                'cui' => 'RO12345678',
                'service_radius_km' => 30,
                'available_weekends' => true,
                'emergency_services' => true,
                'is_active' => true
            ],
            [
                'name' => 'Andrei Ionescu',
                'email' => 'andrei.ionescu@example.com',
                'phone' => '0722345678',
                'category_id' => $categories->where('slug', 'electrician')->first()->id,
                'location_id' => $locations->where('slug', 'cluj-napoca-cluj')->first()->id,
                'specialization' => 'Automatizări și sisteme smart home',
                'experience_years' => 8,
                'description' => 'Specialist în automatizări electrice și sisteme smart home. Instalez și configurez sisteme inteligente pentru case și apartamente moderne.',
                'certifications' => ['ANRE', 'Cisco IoT'],
                'has_insurance' => true,
                'service_radius_km' => 25,
                'available_weekends' => false,
                'emergency_services' => false,
                'is_active' => true
            ],
            
            // Instalatori
            [
                'name' => 'Mihai Dumitrescu',
                'email' => 'mihai.dumitrescu@example.com',
                'phone' => '0723456789',
                'category_id' => $categories->where('slug', 'instalator')->first()->id,
                'location_id' => $locations->where('slug', 'bucuresti')->first()->id,
                'specialization' => 'Instalații sanitare și termice',
                'experience_years' => 12,
                'description' => 'Instalator autorizat ISCIR pentru instalații sanitare, termice și gaze. Montez și repar centrale termice, boilere, sisteme de încălzire în pardoseală.',
                'certifications' => ['ISCIR', 'Distrigaz', 'Protecția muncii'],
                'has_insurance' => true,
                'company_name' => 'Termo Instal SRL',
                'cui' => 'RO23456789',
                'service_radius_km' => 35,
                'available_weekends' => true,
                'emergency_services' => true,
                'is_active' => true
            ],
            [
                'name' => 'Vasile Popa',
                'email' => 'vasile.popa@example.com',
                'phone' => '0724567890',
                'category_id' => $categories->where('slug', 'instalator')->first()->id,
                'location_id' => $locations->where('slug', 'iasi-iasi')->first()->id,
                'specialization' => 'Instalații sanitare și canalizare',
                'experience_years' => 10,
                'description' => 'Instalator cu experiență în instalații sanitare, canalizare, montaj băi complete. Lucrări realizate profesional, cu garanție.',
                'certifications' => ['ISCIR'],
                'has_insurance' => false,
                'service_radius_km' => 20,
                'available_weekends' => true,
                'emergency_services' => true,
                'is_active' => true
            ],
            
            // Tâmplari
            [
                'name' => 'George Marin',
                'email' => 'george.marin@example.com',
                'phone' => '0725678901',
                'category_id' => $categories->where('slug', 'tamplar')->first()->id,
                'location_id' => $locations->where('slug', 'bucuresti')->first()->id,
                'specialization' => 'Mobilier la comandă și renovări lemn',
                'experience_years' => 20,
                'description' => 'Meșter tâmplar cu 20 ani experiență. Realizez mobilier personalizat, bucătării, dulapuri, biblioteci. Montez uși, ferestre, parchet.',
                'certifications' => ['Meșter Tâmplar Categoria I'],
                'has_insurance' => true,
                'company_name' => 'Lemn Art SRL',
                'cui' => 'RO34567890',
                'service_radius_km' => 40,
                'available_weekends' => false,
                'emergency_services' => false,
                'is_active' => true
            ],
            
            // Zugravi
            [
                'name' => 'Alexandru Stan',
                'email' => 'alexandru.stan@example.com',
                'phone' => '0726789012',
                'category_id' => $categories->where('slug', 'zugrav')->first()->id,
                'location_id' => $locations->where('slug', 'timisoara-timis')->first()->id,
                'specialization' => 'Zugrăveli interioare și decorative',
                'experience_years' => 7,
                'description' => 'Zugrav și vopsitor pentru zugrăveli interioare și exterioare. Finisaje decorative, tencuieli, şpaclu, vopsitorii. Lucrez cu materiale premium.',
                'certifications' => ['Curs finisaje decorative'],
                'has_insurance' => false,
                'service_radius_km' => 30,
                'available_weekends' => true,
                'emergency_services' => false,
                'is_active' => true
            ],
            
            // Zidari
            [
                'name' => 'Constantin Radu',
                'email' => 'constantin.radu@example.com',
                'phone' => '0727890123',
                'category_id' => $categories->where('slug', 'zidar')->first()->id,
                'location_id' => $locations->where('slug', 'brasov-brasov')->first()->id,
                'specialization' => 'Construcții și renovări',
                'experience_years' => 18,
                'description' => 'Zidar cu experiență în construcții noi și renovări. Zidărie, tencuieli, finisaje exterioare. Echipă proprie pentru proiecte mari.',
                'certifications' => ['Meșter Constructor'],
                'has_insurance' => true,
                'company_name' => 'Construcții Radu SRL',
                'cui' => 'RO45678901',
                'service_radius_km' => 50,
                'available_weekends' => false,
                'emergency_services' => false,
                'is_active' => true
            ],
            
            // Faiantari
            [
                'name' => 'Cristian Gheorghe',
                'email' => 'cristian.gheorghe@example.com',
                'phone' => '0728901234',
                'category_id' => $categories->where('slug', 'faiantar')->first()->id,
                'location_id' => $locations->where('slug', 'bucuresti')->first()->id,
                'specialization' => 'Montaj faianță, gresie, mozaic',
                'experience_years' => 9,
                'description' => 'Faiantar specializat în montaj faianță, gresie, mozaic pentru băi, bucătării, terase. Finisaje de calitate, atenție la detalii.',
                'certifications' => ['Curs faiantar-gresor'],
                'has_insurance' => false,
                'service_radius_km' => 25,
                'available_weekends' => true,
                'emergency_services' => false,
                'is_active' => true
            ],
            
            // Frigotehniști
            [
                'name' => 'Daniel Nistor',
                'email' => 'daniel.nistor@example.com',
                'phone' => '0729012345',
                'category_id' => $categories->where('slug', 'frigotehnist')->first()->id,
                'location_id' => $locations->where('slug', 'cluj-napoca-cluj')->first()->id,
                'specialization' => 'Instalare și service aer condiționat',
                'experience_years' => 11,
                'description' => 'Frigotehnist autorizat pentru instalare, service și reparații aer condiționat, frigidere, congelatoare. Intervenții rapide, garanție la lucrări.',
                'certifications' => ['ISCIR frigotehnist', 'F-Gas'],
                'has_insurance' => true,
                'company_name' => 'Cool Service SRL',
                'cui' => 'RO56789012',
                'service_radius_km' => 30,
                'available_weekends' => true,
                'emergency_services' => true,
                'is_active' => true
            ],
            
            // Mecanici auto
            [
                'name' => 'Florin Matei',
                'email' => 'florin.matei@example.com',
                'phone' => '0720123456',
                'category_id' => $categories->where('slug', 'mecanic-auto')->first()->id,
                'location_id' => $locations->where('slug', 'bucuresti')->first()->id,
                'specialization' => 'Service auto mobil - toate mărcile',
                'experience_years' => 14,
                'description' => 'Mecanic auto cu service mobil. Vin la tine acasă pentru diagnoză, reparații, schimb ulei, revizie tehnică. Echipament profesional.',
                'certifications' => ['RAR autorizare service', 'Certificat mecanic auto'],
                'has_insurance' => true,
                'company_name' => 'Auto Mobile Service SRL',
                'cui' => 'RO67890123',
                'service_radius_km' => 40,
                'available_weekends' => true,
                'emergency_services' => true,
                'is_active' => true
            ],
        ];

        foreach ($craftsmen as $craftsmanData) {
            $craftsman = User::create([
                'name' => $craftsmanData['name'],
                'email' => $craftsmanData['email'],
                'password' => Hash::make('password123'),
                'role' => 'specialist',
                'phone' => $craftsmanData['phone'],
                'category_id' => $craftsmanData['category_id'],
                'location_id' => $craftsmanData['location_id'],
                'specialization' => $craftsmanData['specialization'],
                'experience_years' => $craftsmanData['experience_years'],
                'description' => $craftsmanData['description'],
                'certifications' => $craftsmanData['certifications'],
                'has_insurance' => $craftsmanData['has_insurance'],
                'company_name' => $craftsmanData['company_name'] ?? null,
                'cui' => $craftsmanData['cui'] ?? null,
                'service_radius_km' => $craftsmanData['service_radius_km'],
                'available_weekends' => $craftsmanData['available_weekends'],
                'emergency_services' => $craftsmanData['emergency_services'],
                'is_active' => $craftsmanData['is_active'],
                'slug' => Str::slug($craftsmanData['name'])
            ]);
        }

        $this->command->info('Meseriași creați cu succes!');
    }
}
