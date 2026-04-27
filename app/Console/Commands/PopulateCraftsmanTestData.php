<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Review;
use App\Models\Gallery;
use App\Models\Certification;
use App\Models\AvailabilitySlot;
use Illuminate\Support\Facades\Hash;

class PopulateCraftsmanTestData extends Command
{
    protected $signature = 'test:populate-craftsman {user_id}';
    protected $description = 'Populează contul de meseriaș cu date fictive pentru testare';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);

        if (!$user || !in_array($user->role, ['craftsman', 'specialist'])) {
            $this->error('Utilizatorul nu există sau nu este meseriaș!');
            return 1;
        }

        $this->info("Populare date pentru: {$user->name}");
        $this->newLine();

        // 1. Servicii
        $this->populateServices($user);

        // 2. Creare clienți ficțivi
        $clients = $this->createClients();

        // 3. Programări
        $this->populateAppointments($user, $clients);

        // 4. Recenzii
        $this->populateReviews($user, $clients);

        // 5. Galerie lucrări
        $this->populateGallery($user);

        // 6. Certificări
        $this->populateCertifications($user);

        // 7. Disponibilitate
        $this->populateAvailability($user);

        $this->newLine();
        $this->info('✅ Contul a fost populat cu date fictive!');
        $this->info('🔄 Reîmprospătează dashboard-ul în browser.');

        return 0;
    }

    private function populateServices($user)
    {
        $this->info('📋 Creare servicii electrician...');

        $services = [
            ['name' => 'Instalații Electrice Noi', 'description' => 'Instalații electrice complete pentru case și apartamente noi', 'price' => 150, 'duration' => 240],
            ['name' => 'Revizie Instalație Electrică', 'description' => 'Verificare și întreținere instalație electrică existentă', 'price' => 80, 'duration' => 120],
            ['name' => 'Montaj Prize și Întrerupătoare', 'description' => 'Montaj și înlocuire prize, întrerupătoare, dimere', 'price' => 50, 'duration' => 60],
            ['name' => 'Instalare Tablou Electric', 'description' => 'Montaj și configurare tablou electric modern cu protecții', 'price' => 200, 'duration' => 180],
            ['name' => 'Iluminat Decorativ', 'description' => 'Proiectare și instalare iluminat LED decorativ', 'price' => 120, 'duration' => 150],
            ['name' => 'Depanare Urgentă', 'description' => 'Intervenție urgentă pentru probleme electrice', 'price' => 100, 'duration' => 90],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['user_id' => $user->id, 'name' => $service['name']],
                array_merge($service, [
                    'category_id' => $user->category_id,
                    'is_active' => true,
                ])
            );
        }

        $this->info('  ✓ Create ' . count($services) . ' servicii');
    }

    private function createClients()
    {
        $this->info('👥 Creare clienți ficțivi...');

        $clientsData = [
            ['name' => 'Ion Popescu', 'email' => 'ion.popescu@test.ro', 'phone' => '0721123456'],
            ['name' => 'Maria Ionescu', 'email' => 'maria.ionescu@test.ro', 'phone' => '0722234567'],
            ['name' => 'Andrei Georgescu', 'email' => 'andrei.georgescu@test.ro', 'phone' => '0723345678'],
            ['name' => 'Elena Popa', 'email' => 'elena.popa@test.ro', 'phone' => '0724456789'],
            ['name' => 'Mihai Radu', 'email' => 'mihai.radu@test.ro', 'phone' => '0725567890'],
        ];

        $clients = [];
        foreach ($clientsData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'client',
                    'phone' => $data['phone'],
                ]
            );

            $clients[] = $user;
        }

        $this->info('  ✓ Creați ' . count($clients) . ' clienți');
        return $clients;
    }

    private function populateAppointments($user, $clients)
    {
        $this->info('📅 Creare programări...');

        $services = Service::where('user_id', $user->id)->get();
        
        if ($services->isEmpty()) {
            $this->warn('  ⚠ Nu există servicii pentru programări');
            return;
        }

        $statuses = ['pending', 'confirmed', 'completed'];
        $count = 0;

        // Programări din ultimele 30 zile
        for ($i = 0; $i < 8; $i++) {
            $client = $clients[array_rand($clients)];
            $service = $services->random();
            $date = now()->subDays(rand(1, 30));

            Appointment::create([
                'specialist_id' => $user->id,
                'client_id' => $client->id,
                'client_name' => $client->name,
                'client_email' => $client->email,
                'client_phone' => $client->phone ?? '0721000000',
                'service_id' => $service->id,
                'appointment_date' => $date->format('Y-m-d'),
                'appointment_time' => sprintf('%02d:00', rand(8, 17)),
                'status' => $statuses[array_rand($statuses)],
                'client_address' => 'Str. Libertății Nr. ' . rand(1, 100),
                'client_city' => 'București',
                'notes' => 'Programare de test',
                'total_amount' => $service->price,
                'payment_status' => 'pending',
            ]);
            $count++;
        }

        // Programări viitoare
        for ($i = 0; $i < 4; $i++) {
            $client = $clients[array_rand($clients)];
            $service = $services->random();
            $date = now()->addDays(rand(1, 14));

            Appointment::create([
                'specialist_id' => $user->id,
                'client_id' => $client->id,
                'client_name' => $client->name,
                'client_email' => $client->email,
                'client_phone' => $client->phone ?? '0721000000',
                'service_id' => $service->id,
                'appointment_date' => $date->format('Y-m-d'),
                'appointment_time' => sprintf('%02d:00', rand(8, 17)),
                'status' => rand(0, 1) ? 'confirmed' : 'pending',
                'client_address' => 'Str. Unirii Nr. ' . rand(1, 100),
                'client_city' => 'București',
                'notes' => 'Programare viitoare',
                'total_amount' => $service->price,
                'payment_status' => 'pending',
                'service_id' => $service->id,
                'appointment_date' => $date->format('Y-m-d'),
                'appointment_time' => sprintf('%02d:00', rand(8, 17)),
                'duration' => $service->duration ?? 120,
                'status' => rand(0, 1) ? 'confirmed' : 'pending',
                'location' => 'Str. Unirii Nr. ' . rand(1, 100) . ', București',
                'notes' => 'Programare viitoare',
                'price' => $service->price,
            ]);
            $count++;
        }

        $this->info("  ✓ Create {$count} programări");
    }

    private function populateReviews($user, $clients)
    {
        $this->info('⭐ Creare recenzii...');

        $comments = [
            'Foarte profesionist! A rezolvat problema rapid și eficient.',
            'Recomand cu încredere! Lucrare de calitate și preț corect.',
            'Electrician serios, punctual și atent la detalii.',
            'Mulțumit de serviciile prestate. Munca foarte bună!',
            'Persoană de încredere, lucrare executată profesional.',
        ];

        $count = 0;
        $completedAppointments = Appointment::where('specialist_id', $user->id)
            ->where('status', 'completed')
            ->get();

        foreach ($completedAppointments->take(5) as $appointment) {
            Review::create([
                'specialist_id' => $user->id,
                'client_id' => $appointment->client_id,
                'client_name' => $appointment->client_name,
                'appointment_id' => $appointment->id,
                'rating' => rand(4, 5),
                'comment' => $comments[array_rand($comments)],
                'is_verified' => true,
            ]);
            $count++;
        }

        $this->info("  ✓ Create {$count} recenzii");
    }

    private function populateGallery($user)
    {
        $this->info('🖼️ Creare galerie lucrări...');

        $works = [
            ['title' => 'Instalare Tablou Electric Modern', 'description' => 'Tablou electric cu protecții automate și diferențiale'],
            ['title' => 'Iluminat LED Apartament', 'description' => 'Sistem complet de iluminat LED cu control smart'],
            ['title' => 'Renovare Instalație Electrică', 'description' => 'Înlocuire completă instalație electrică apartament 3 camere'],
            ['title' => 'Montaj Prize Încastrate', 'description' => 'Prize și întrerupătoare moderne încastrate în gips-carton'],
        ];

        $count = 0;
        foreach ($works as $work) {
            Gallery::create([
                'user_id' => $user->id,
                'title' => $work['title'],
                'description' => $work['description'],
                'image_path' => 'gallery/placeholder-' . ($count + 1) . '.jpg',
                'is_featured' => $count === 0,
            ]);
            $count++;
        }

        $this->info("  ✓ Create {$count} imagini în galerie");
    }

    private function populateCertifications($user)
    {
        $this->info('🎓 Creare certificări...');

        $certifications = [
            ['name' => 'Autorizație Electrician Instalator', 'issuer' => 'ANRE', 'issue_date' => '2020-03-15'],
            ['name' => 'Curs SSM Domeniul Electric', 'issuer' => 'ITM București', 'issue_date' => '2021-06-20'],
            ['name' => 'Certificat Instalații Fotovoltaice', 'issuer' => 'ANRE', 'issue_date' => '2022-09-10'],
        ];

        $count = 0;
        foreach ($certifications as $cert) {
            Certification::updateOrCreate(
                ['user_id' => $user->id, 'title' => $cert['name']],
                [
                    'issuer' => $cert['issuer'],
                    'issue_date' => $cert['issue_date'],
                    'expiry_date' => now()->addYears(3)->format('Y-m-d'),
                ]
            );
            $count++;
        }

        $this->info("  ✓ Create {$count} certificări");
    }

    private function populateAvailability($user)
    {
        $this->info('🕐 Creare disponibilitate...');

        $count = 0;
        for ($i = 1; $i <= 14; $i++) {
            $date = now()->addDays($i);
            
            if ($date->isWeekend()) {
                continue;
            }

            for ($hour = 8; $hour <= 16; $hour++) {
                if (rand(1, 100) <= 80) {
                    AvailabilitySlot::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'date' => $date->format('Y-m-d'),
                            'start_time' => sprintf('%02d:00', $hour),
                        ],
                        [
                            'end_time' => sprintf('%02d:00', $hour + 1),
                            'is_available' => true,
                        ]
                    );
                    $count++;
                }
            }
        }

        $this->info("  ✓ Create {$count} sloturi disponibilitate");
    }
}
