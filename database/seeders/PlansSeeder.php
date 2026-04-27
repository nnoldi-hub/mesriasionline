<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'                 => 'Free',
                'slug'                 => 'free',
                'description'          => 'Listare de bază — potrivit pentru a începe.',
                'price_monthly'        => 0.00,
                'max_quotes_per_month' => 3,
                'featured_listing'     => false,
                'priority_visibility'  => false,
                'badge_visible'        => false,
                'sort_order'           => 1,
                'features' => [
                    'Profil public activ',
                    'Până la 3 oferte/lună',
                    'Recenzii de la clienți',
                    'Galerie foto (max 5)',
                ],
            ],
            [
                'name'                 => 'Starter',
                'slug'                 => 'starter',
                'description'          => 'Vizibilitate mărită și mai multe oferte.',
                'price_monthly'        => 49.00,
                'max_quotes_per_month' => 10,
                'featured_listing'     => false,
                'priority_visibility'  => true,
                'badge_visible'        => true,
                'sort_order'           => 2,
                'features' => [
                    'Tot ce include Free',
                    'Până la 10 oferte/lună',
                    'Badge „Starter" în listări',
                    'Vizibilitate mărită în căutări',
                    'Galerie foto (max 20)',
                ],
            ],
            [
                'name'                 => 'Pro',
                'slug'                 => 'pro',
                'description'          => 'Vizibilitate premium și oferte nelimitate.',
                'price_monthly'        => 99.00,
                'max_quotes_per_month' => 0, // nelimitat
                'featured_listing'     => true,
                'priority_visibility'  => true,
                'badge_visible'        => true,
                'sort_order'           => 3,
                'features' => [
                    'Tot ce include Starter',
                    'Oferte nelimitate',
                    'Badge „Pro" în listări',
                    'Poziție Top în căutări',
                    'Galerie foto nelimitată',
                    'Statistici avansate',
                    'Suport prioritar',
                ],
            ],
        ];

        foreach ($plans as $data) {
            Plan::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
