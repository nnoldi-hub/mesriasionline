<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoCraftsmenBucharestSeeder extends Seeder
{
    public function run(): void
    {
        $bucuresti = Location::where('slug', 'bucuresti')->first();
        if (! $bucuresti) {
            $this->command->error('Location "bucuresti" nu există. Rulează LocationsSeeder mai întâi.');
            return;
        }

        $craftsmen = [
            [
                'category_slug' => 'zugrav',
                'name'          => 'Gheorghe Matei',
                'email'         => 'gh.matei@example.com',
                'phone'         => '0720111222',
                'specialization'=> 'Zugrăveli și vopsitorii interioare',
                'experience_years' => 14,
                'description'   => 'Zugrav profesionist cu 14 ani experiență. Execute zugrăveli, vopsitorii decorative, stucco și tencuieli decorative.',
                'is_featured'   => true,
                'is_verified'   => true,
            ],
            [
                'category_slug' => 'zugrav',
                'name'          => 'Florin Neagu',
                'email'         => 'florin.neagu@example.com',
                'phone'         => '0720333444',
                'specialization'=> 'Decorațiuni și vopsitorii speciale',
                'experience_years' => 7,
                'description'   => 'Zugrav specializat în tehnici decorative: beton imprimat, glazuri, finisaje din tencuieli decorative.',
                'is_featured'   => false,
                'is_verified'   => true,
            ],
            [
                'category_slug' => 'instalator',
                'name'          => 'Vasile Costea',
                'email'         => 'vasile.costea@example.com',
                'phone'         => '0720555666',
                'specialization'=> 'Instalații sanitare și termice',
                'experience_years' => 18,
                'description'   => 'Instalator autorizat cu 18 ani experiență. Montez centrale, boilere, sisteme de încălzire în pardoseală, instalații sanitare complete.',
                'is_featured'   => true,
                'is_verified'   => true,
            ],
            [
                'category_slug' => 'tamplar',
                'name'          => 'Nicolae Balan',
                'email'         => 'n.balan@example.com',
                'phone'         => '0720777888',
                'specialization'=> 'Tâmplărie PVC și aluminiu',
                'experience_years' => 10,
                'description'   => 'Montez ferestre și uși din PVC, aluminiu și lemn stratificat. Ofer garanție 5 ani pentru lucrările executate.',
                'is_featured'   => false,
                'is_verified'   => true,
            ],
            [
                'category_slug' => 'tamplar',
                'name'          => 'Marian Stoica',
                'email'         => 'm.stoica@example.com',
                'phone'         => '0720999000',
                'specialization'=> 'Mobilier la comandă',
                'experience_years' => 12,
                'description'   => 'Execut mobilier la comandă: dulapuri, biblioteci, bucătării, placare. Materiale premium, livrare și montaj inclus.',
                'is_featured'   => true,
                'is_verified'   => false,
            ],
            [
                'category_slug' => 'zidar',
                'name'          => 'Dumitru Popa',
                'email'         => 'dumitru.popa@example.com',
                'phone'         => '0721000111',
                'specialization'=> 'Construcții și renovări complete',
                'experience_years' => 20,
                'description'   => 'Constructor cu experiență vastă în renovări complete de apartamente și case. Execut demolări, zidărie, șape, gresie și faianță.',
                'is_featured'   => true,
                'is_verified'   => true,
            ],
            [
                'category_slug' => 'zidar',
                'name'          => 'Aurel Dobre',
                'email'         => 'aurel.dobre@example.com',
                'phone'         => '0721222333',
                'specialization'=> 'Gresie, faianță și placări',
                'experience_years' => 9,
                'description'   => 'Specialist în montaj gresie, faianță, plăci decorative și pietre naturale. Precizie, curățenie și respectul termenelor.',
                'is_featured'   => false,
                'is_verified'   => true,
            ],
            [
                'category_slug' => 'electrician',
                'name'          => 'Constantin Rad',
                'email'         => 'c.rad@example.com',
                'phone'         => '0721444555',
                'specialization'=> 'Instalații electrice complete',
                'experience_years' => 11,
                'description'   => 'Electrician autorizat ANRE. Execut instalații electrice complete, tablouri electrice, prize, interfoane și sisteme de automatizare.',
                'is_featured'   => false,
                'is_verified'   => true,
            ],
        ];

        foreach ($craftsmen as $data) {
            $category = Category::where('slug', $data['category_slug'])->first();
            if (! $category) {
                $this->command->warn("Categoria {$data['category_slug']} nu există – skip {$data['name']}");
                continue;
            }

            // Skip if email already exists
            if (User::where('email', $data['email'])->exists()) {
                $this->command->info("Skip {$data['name']} (deja există)");
                continue;
            }

            User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'password'          => Hash::make('password'),
                'role'              => 'specialist',
                'phone'             => $data['phone'],
                'category_id'       => $category->id,
                'location_id'       => $bucuresti->id,
                'specialization'    => $data['specialization'],
                'experience_years'  => $data['experience_years'],
                'description'       => $data['description'],
                'is_active'         => true,
                'is_featured'       => $data['is_featured'],
                'is_verified'       => $data['is_verified'],
                'slug'              => Str::slug($data['name']) . '-' . Str::random(4),
                'onboarding_completed_at' => now(),
            ]);

            $this->command->info("Created: {$data['name']} ({$data['category_slug']}/bucuresti)");
        }
    }
}
