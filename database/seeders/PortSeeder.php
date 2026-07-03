<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\Port;

class PortSeeder extends Seeder
{
    public function run()
    {
        $ports = [
            ['name' => 'Port of Shanghai', 'country' => 'China', 'lat' => 31.2304, 'lng' => 121.4737, 'size' => 'Large'],
            ['name' => 'Port of Singapore', 'country' => 'Singapore', 'lat' => 1.2644, 'lng' => 103.8220, 'size' => 'Large'],
            ['name' => 'Port of Rotterdam', 'country' => 'Netherlands', 'lat' => 51.9244, 'lng' => 4.4777, 'size' => 'Large'],
            ['name' => 'Port of Los Angeles', 'country' => 'United States', 'lat' => 33.7405, 'lng' => -118.2720, 'size' => 'Large'],
            ['name' => 'Port of Hamburg', 'country' => 'Germany', 'lat' => 53.5511, 'lng' => 9.9937, 'size' => 'Large'],
            ['name' => 'Port of Tokyo', 'country' => 'Japan', 'lat' => 35.6528, 'lng' => 139.8394, 'size' => 'Large'],
            ['name' => 'Tanjung Priok', 'country' => 'Indonesia', 'lat' => -6.1045, 'lng' => 106.8806, 'size' => 'Medium'],
            ['name' => 'Port Klang', 'country' => 'Malaysia', 'lat' => 3.0048, 'lng' => 101.3900, 'size' => 'Medium'],
            ['name' => 'Port of Dubai', 'country' => 'United Arab Emirates', 'lat' => 25.2697, 'lng' => 55.3095, 'size' => 'Large'],
            ['name' => 'Port of London', 'country' => 'United Kingdom', 'lat' => 51.5074, 'lng' => -0.1278, 'size' => 'Medium'],
            ['name' => 'Port of Mumbai', 'country' => 'India', 'lat' => 18.9647, 'lng' => 72.8258, 'size' => 'Medium'],
            ['name' => 'Port of Santos', 'country' => 'Brazil', 'lat' => -23.9618, 'lng' => -46.3322, 'size' => 'Medium'],
            ['name' => 'Port of Sydney', 'country' => 'Australia', 'lat' => -33.8688, 'lng' => 151.2093, 'size' => 'Medium'],
            ['name' => 'Laem Chabang', 'country' => 'Thailand', 'lat' => 13.0827, 'lng' => 100.8833, 'size' => 'Medium'],
            ['name' => 'Cai Mep', 'country' => 'Vietnam', 'lat' => 10.4833, 'lng' => 107.0667, 'size' => 'Small'],
        ];

        foreach ($ports as $portData) {
            $country = Country::where('name', $portData['country'])->first();
            
            if ($country) {
                Port::updateOrCreate(
                    ['port_name' => $portData['name']],
                    [
                        'country_id' => $country->id,
                        'country_name' => $portData['country'],
                        'latitude' => $portData['lat'],
                        'longitude' => $portData['lng'],
                        'harbor_size' => $portData['size'],
                        'is_active' => true
                    ]
                );
            }
        }

        $this->command->info('✅ ' . count($ports) . ' ports seeded successfully!');
    }
}