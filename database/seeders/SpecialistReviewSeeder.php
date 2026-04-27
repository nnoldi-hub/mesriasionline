<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Review;

class SpecialistReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed reviews for first 3 active specialists
        $specialists = User::where('role', 'specialist')->where('is_active', true)->take(3)->get();

        $clientNames = ['Ana', 'Mihai', 'Elena', 'Roxana', 'Vlad', 'Ioana', 'Andrei', 'Cristina'];

        foreach ($specialists as $specialist) {
            $service = $specialist->services()->inRandomOrder()->first();
            if (!$service) {
                continue;
            }

            for ($i = 0; $i < 3; $i++) {
                $date = Carbon::now()->subDays(rand(3, 30));
                $time = sprintf('%02d:%02d:00', rand(9, 18), [0, 15, 30, 45][rand(0, 3)]);

                $appointment = Appointment::create([
                    'specialist_id' => $specialist->id,
                    'client_name' => $clientNames[array_rand($clientNames)] . ' ' . Str::upper(Str::random(1)) . '.',
                    'client_email' => Str::lower(Str::random(6)) . '@example.com',
                    'client_phone' => '+40 7' . rand(10, 99) . ' ' . rand(100, 999) . ' ' . rand(100, 999),
                    'service_id' => $service->id,
                    'appointment_date' => $date->format('Y-m-d'),
                    'appointment_time' => $time,
                    'status' => 'completed',
                    'is_home_service' => true,
                    'client_address' => 'Str. Exemplu ' . rand(1, 99),
                    'client_city' => 'Bucuresti',
                    'client_zone' => 'Sector ' . rand(1, 6),
                    'distance_km' => rand(2, 15),
                    'transport_fee' => rand(10, 30),
                    'estimated_travel_time' => rand(10, 45),
                    'payment_status' => 'paid',
                    'payment_method' => 'cash',
                    'total_amount' => $service->price + rand(0, 20),
                    'review_token' => Str::random(40),
                ]);

                Review::updateOrCreate(
                    [
                        'appointment_id' => $appointment->id,
                        'specialist_id' => $specialist->id,
                    ],
                    [
                        'client_name' => $appointment->client_name,
                        'rating' => rand(4, 5),
                        'comment' => $this->sampleComment($specialist->category_id),
                        'is_approved' => true,
                        'is_featured' => (bool)rand(0, 1),
                        'photos' => [],
                        'service_quality_rating' => rand(4, 5),
                        'punctuality_rating' => rand(4, 5),
                        'cleanliness_rating' => rand(4, 5),
                        'overall_experience' => rand(4, 5),
                    ]
                );
            }
        }
    }

    private function sampleComment(?int $categoryId): string
    {
        $comments = [
            // Instalații
            1 => [
                'Instalator excelent! A reparat toate tevile în timp record.',
                'Foarte profesionist, a rezolvat problema rapid și curat.',
                'Recomand cu încredere, lucrare de calitate!',
            ],
            // Electricieni
            2 => [
                'Electrician foarte priceput, a instalat totul perfect.',
                'Lucrare impecabilă, foarte atent la detalii de siguranță.',
                'Profesionist adevărat, munca excelentă!',
            ],
            // Zugravi
            3 => [
                'Zugrăveala arată perfect, culori uniforme.',
                'Foarte curat și ordonat, nu a lăsat nicio urmă.',
                'Lucrare de calitate, recomand!',
            ],
            // Tâmplari
            4 => [
                'Mobilă montată perfect, foarte atent la detalii.',
                'Reparație rapidă și profesională a ușilor.',
                'Tâmplar excelent, recomand cu drag!',
            ],
            // Mecanici auto
            5 => [
                'Service auto de încredere, reparație corectă.',
                'Mecanic priceput, a găsit problema rapid.',
                'Prețuri corecte și muncă de calitate!',
            ],
        ];

        $pool = $comments[$categoryId] ?? [
            'Experiență excelentă, recomand cu încredere!',
            'Foarte profesionist și punctual.',
            'Lucrare de calitate, mulțumit!',
            'Recomand! A făcut treabă foarte bună.',
            'Corect și priceput, voi apela din nou.',
        ];
        
        return $pool[array_rand($pool)];
    }
}
