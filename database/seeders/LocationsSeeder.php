<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationsSeeder extends Seeder
{
    public function run()
    {
        $locations = [
            // București și Ilfov
            ['city' => 'București', 'county' => 'București', 'slug' => 'bucuresti', 'latitude' => 44.4268, 'longitude' => 26.1025, 'is_active' => true],
            ['city' => 'Voluntari', 'county' => 'Ilfov', 'slug' => 'voluntari-ilfov', 'latitude' => 44.4953, 'longitude' => 26.1886, 'is_active' => true],
            ['city' => 'Popești-Leordeni', 'county' => 'Ilfov', 'slug' => 'popesti-leordeni-ilfov', 'latitude' => 44.3814, 'longitude' => 26.1614, 'is_active' => true],
            ['city' => 'Bragadiru', 'county' => 'Ilfov', 'slug' => 'bragadiru-ilfov', 'latitude' => 44.3614, 'longitude' => 26.0408, 'is_active' => true],
            ['city' => 'Pantelimon', 'county' => 'Ilfov', 'slug' => 'pantelimon-ilfov', 'latitude' => 44.4533, 'longitude' => 26.2192, 'is_active' => true],
            
            // Cluj
            ['city' => 'Cluj-Napoca', 'county' => 'Cluj', 'slug' => 'cluj-napoca-cluj', 'latitude' => 46.7712, 'longitude' => 23.6236, 'is_active' => true],
            ['city' => 'Florești', 'county' => 'Cluj', 'slug' => 'floresti-cluj', 'latitude' => 46.7486, 'longitude' => 23.4986, 'is_active' => true],
            ['city' => 'Turda', 'county' => 'Cluj', 'slug' => 'turda-cluj', 'latitude' => 46.5683, 'longitude' => 23.7847, 'is_active' => true],
            
            // Iași
            ['city' => 'Iași', 'county' => 'Iași', 'slug' => 'iasi-iasi', 'latitude' => 47.1585, 'longitude' => 27.6014, 'is_active' => true],
            ['city' => 'Pașcani', 'county' => 'Iași', 'slug' => 'pascani-iasi', 'latitude' => 47.2453, 'longitude' => 26.7167, 'is_active' => true],
            
            // Timișoara
            ['city' => 'Timișoara', 'county' => 'Timiș', 'slug' => 'timisoara-timis', 'latitude' => 45.7489, 'longitude' => 21.2087, 'is_active' => true],
            ['city' => 'Lugoj', 'county' => 'Timiș', 'slug' => 'lugoj-timis', 'latitude' => 45.6856, 'longitude' => 21.9031, 'is_active' => true],
            
            // Constanța
            ['city' => 'Constanța', 'county' => 'Constanța', 'slug' => 'constanta-constanta', 'latitude' => 44.1598, 'longitude' => 28.6348, 'is_active' => true],
            ['city' => 'Mangalia', 'county' => 'Constanța', 'slug' => 'mangalia-constanta', 'latitude' => 43.8153, 'longitude' => 28.5831, 'is_active' => true],
            ['city' => 'Medgidia', 'county' => 'Constanța', 'slug' => 'medgidia-constanta', 'latitude' => 44.2469, 'longitude' => 28.2769, 'is_active' => true],
            
            // Brașov
            ['city' => 'Brașov', 'county' => 'Brașov', 'slug' => 'brasov-brasov', 'latitude' => 45.6427, 'longitude' => 25.5887, 'is_active' => true],
            ['city' => 'Săcele', 'county' => 'Brașov', 'slug' => 'sacele-brasov', 'latitude' => 45.6167, 'longitude' => 25.7000, 'is_active' => true],
            ['city' => 'Codlea', 'county' => 'Brașov', 'slug' => 'codlea-brasov', 'latitude' => 45.7042, 'longitude' => 25.4514, 'is_active' => true],
            
            // Craiova
            ['city' => 'Craiova', 'county' => 'Dolj', 'slug' => 'craiova-dolj', 'latitude' => 44.3302, 'longitude' => 23.7949, 'is_active' => true],
            
            // Ploiești
            ['city' => 'Ploiești', 'county' => 'Prahova', 'slug' => 'ploiesti-prahova', 'latitude' => 44.9397, 'longitude' => 26.0173, 'is_active' => true],
            ['city' => 'Câmpina', 'county' => 'Prahova', 'slug' => 'campina-prahova', 'latitude' => 45.1272, 'longitude' => 25.7331, 'is_active' => true],
            
            // Galați
            ['city' => 'Galați', 'county' => 'Galați', 'slug' => 'galati-galati', 'latitude' => 45.4353, 'longitude' => 28.0080, 'is_active' => true],
            
            // Brăila
            ['city' => 'Brăila', 'county' => 'Brăila', 'slug' => 'braila-braila', 'latitude' => 45.2692, 'longitude' => 27.9575, 'is_active' => true],
            
            // Oradea
            ['city' => 'Oradea', 'county' => 'Bihor', 'slug' => 'oradea-bihor', 'latitude' => 47.0465, 'longitude' => 21.9189, 'is_active' => true],
            
            // Bacău
            ['city' => 'Bacău', 'county' => 'Bacău', 'slug' => 'bacau-bacau', 'latitude' => 46.5670, 'longitude' => 26.9146, 'is_active' => true],
            
            // Arad
            ['city' => 'Arad', 'county' => 'Arad', 'slug' => 'arad-arad', 'latitude' => 46.1866, 'longitude' => 21.3123, 'is_active' => true],
            
            // Pitești
            ['city' => 'Pitești', 'county' => 'Argeș', 'slug' => 'pitesti-arges', 'latitude' => 44.8565, 'longitude' => 24.8692, 'is_active' => true],
            
            // Sibiu
            ['city' => 'Sibiu', 'county' => 'Sibiu', 'slug' => 'sibiu-sibiu', 'latitude' => 45.7983, 'longitude' => 24.1256, 'is_active' => true],
            
            // Târgu Mureș
            ['city' => 'Târgu Mureș', 'county' => 'Mureș', 'slug' => 'targu-mures-mures', 'latitude' => 46.5425, 'longitude' => 24.5575, 'is_active' => true],
            
            // Baia Mare
            ['city' => 'Baia Mare', 'county' => 'Maramureș', 'slug' => 'baia-mare-maramures', 'latitude' => 47.6567, 'longitude' => 23.5678, 'is_active' => true],
        ];

        foreach ($locations as $location) {
            Location::updateOrCreate(
                ['slug' => $location['slug']],
                $location
            );
        }
    }
}
