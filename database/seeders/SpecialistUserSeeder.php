<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Service;

class SpecialistUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialists = [
            [
                'name' => 'Mara Ionescu',
                'email' => 'mara.nails@example.com',
                'sub_brand' => 'dariaNails',
                'phone' => '+40 712 111 001',
                'coverage_area' => ['Sector 1','Sector 2','Floreasca','Herastrau'],
                'transport_fee' => 2.5,
                'max_distance' => 25,
                'description' => 'Nail artist cu 5+ ani experiență, specializată în gel și nail art.',
            ],
            [
                'name' => 'Andra Petrescu',
                'email' => 'andra.nails@example.com',
                'sub_brand' => 'dariaNails',
                'phone' => '+40 712 111 002',
                'coverage_area' => ['Sector 3','Sector 4','Baneasa'],
                'transport_fee' => 2.0,
                'max_distance' => 20,
                'description' => 'Manichiură și pedichiură premium la domiciliu.',
            ],
            [
                'name' => 'Irina Dumitru',
                'email' => 'irina.nails@example.com',
                'sub_brand' => 'dariaNails',
                'phone' => '+40 712 111 003',
                'coverage_area' => ['Sector 5','Sector 6','Pipera'],
                'transport_fee' => 3.0,
                'max_distance' => 30,
                'description' => 'Pedichiură SPA și tratamente pentru unghii.',
            ],
            [
                'name' => 'Radu Marin',
                'email' => 'radu.hair@example.com',
                'sub_brand' => 'dariaHair',
                'phone' => '+40 713 222 001',
                'coverage_area' => ['Sector 1','Dorobanti','Amzei'],
                'transport_fee' => 3.0,
                'max_distance' => 25,
                'description' => 'Coafor/stylist, tunsori moderne și styling pentru evenimente.',
            ],
            [
                'name' => 'Cristina Stoica',
                'email' => 'cristina.hair@example.com',
                'sub_brand' => 'dariaHair',
                'phone' => '+40 713 222 002',
                'coverage_area' => ['Sector 2','Sector 3','Floreasca'],
                'transport_fee' => 2.5,
                'max_distance' => 20,
                'description' => 'Coafuri pentru mirese și evenimente.',
            ],
            [
                'name' => 'Alexia Barbu',
                'email' => 'alexia.hair@example.com',
                'sub_brand' => 'dariaHair',
                'phone' => '+40 713 222 003',
                'coverage_area' => ['Sector 4','Sector 5'],
                'transport_fee' => 2.0,
                'max_distance' => 18,
                'description' => 'Colorare, balayage și tratamente pentru păr.',
            ],
            [
                'name' => 'Bianca Pavel',
                'email' => 'bianca.glow@example.com',
                'sub_brand' => 'dariaGlow',
                'phone' => '+40 714 333 001',
                'coverage_area' => ['Sector 1','Sector 6','Herastrau'],
                'transport_fee' => 3.0,
                'max_distance' => 28,
                'description' => 'Makeup artist și tratamente faciale personalizate.',
            ],
            [
                'name' => 'Daria Iliescu',
                'email' => 'daria.glow@example.com',
                'sub_brand' => 'dariaGlow',
                'phone' => '+40 714 333 002',
                'coverage_area' => ['Sector 2','Pipera'],
                'transport_fee' => 2.2,
                'max_distance' => 22,
                'description' => 'Machiaj de zi/seară și skincare.',
            ],
            [
                'name' => 'Ioana Matei',
                'email' => 'ioana.glow@example.com',
                'sub_brand' => 'dariaGlow',
                'phone' => '+40 714 333 003',
                'coverage_area' => ['Sector 3','Sector 4','Baneasa'],
                'transport_fee' => 2.8,
                'max_distance' => 26,
                'description' => 'Tratamente anti-aging și makeup pentru evenimente.',
            ],
        ];

        foreach ($specialists as $spec) {
            $slug = Str::slug($spec['name'].' '.Str::random(4));

            $user = User::updateOrCreate(
                ['email' => $spec['email']],
                [
                    'name' => $spec['name'],
                    'password' => 'password1234', // hashed by model cast
                    'role' => 'specialist',
                    'is_active' => true,
                    'phone' => $spec['phone'],
                    'description' => $spec['description'],
                    'sub_brand' => $spec['sub_brand'],
                    'coverage_area' => $spec['coverage_area'],
                    'transport_fee' => $spec['transport_fee'],
                    'max_distance' => $spec['max_distance'],
                    'slug' => $slug,
                ]
            );

            // Creeaza 2-3 servicii de baza pentru fiecare specialist (simplu, fara imagini)
            $serviceSets = [
                'dariaNails' => [
                    ['Manichiura clasica', 'Curatare, pilire, cuticule, lac clasic', 100, 60, 'Tratamente de baza'],
                    ['Semipermanent', 'Aplicare oja semipermanenta + finish', 150, 75, 'Servicii premium'],
                ],
                'dariaHair' => [
                    ['Tuns si coafat', 'Tuns profesional si styling', 180, 60, 'Servicii premium'],
                    ['Coafat eveniment', 'Coafura pentru evenimente speciale', 250, 90, 'Evenimente speciale'],
                ],
                'dariaGlow' => [
                    ['Tratamente facial', 'Curatare si hidratare', 200, 60, 'Tratamente de baza'],
                    ['Machiaj seara', 'Machiaj profesional de seara', 220, 75, 'Evenimente speciale'],
                ],
            ];

            foreach ($serviceSets[$spec['sub_brand']] as [$name, $desc, $price, $duration, $category]) {
                Service::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'name' => $name,
                    ],
                    [
                        'description' => $desc,
                        'price' => $price,
                        'duration' => $duration,
                        'category' => $category,
                        'sub_brand' => $spec['sub_brand'],
                        'is_mobile' => true,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
