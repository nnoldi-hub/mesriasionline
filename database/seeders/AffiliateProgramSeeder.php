?php

namespace Database\Seeders;

use App\Models\AffiliateProgram;
use Illuminate\Database\Seeder;

class AffiliateProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AffiliateProgram::firstOrCreate(
            ['slug' => 'default'],
            [
                'name' => 'Program Standard',
                'description' => 'Programul standard de afiliere Omul Potrivit. Câștigă comision pentru fiecare utilizator care se înregistrează prin link-ul tău.',
                'commission_type' => 'percentage',
                'commission_value' => 10.00, // 10%
                'min_payout' => 100.00, // Minimum 100 lei
                'cookie_days' => 30,
                'is_active' => true,
                'rules' => [
                    'registration_bonus' => 5.00, // 5 lei per registration
                    'first_booking_bonus' => 10.00, // 10 lei for first booking
                ],
            ]
        );

        AffiliateProgram::firstOrCreate(
            ['slug' => 'premium'],
            [
                'name' => 'Program Premium',
                'description' => 'Program de afiliere cu comision crescut pentru parteneri verificați.',
                'commission_type' => 'percentage',
                'commission_value' => 15.00, // 15%
                'min_payout' => 50.00, // Minimum 50 lei
                'cookie_days' => 60,
                'is_active' => true,
                'rules' => [
                    'registration_bonus' => 10.00,
                    'first_booking_bonus' => 20.00,
                ],
            ]
        );
    }
}
