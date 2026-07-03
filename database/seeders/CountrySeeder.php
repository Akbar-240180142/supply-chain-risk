<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    public function run()
    {
        $countries = [
            ['name' => 'Indonesia', 'cca2' => 'ID', 'cca3' => 'IDN', 'capital' => 'Jakarta', 'region' => 'Asia', 'currency_code' => 'IDR', 'latitude' => -0.7893, 'longitude' => 113.9213],
            ['name' => 'United States', 'cca2' => 'US', 'cca3' => 'USA', 'capital' => 'Washington, D.C.', 'region' => 'Americas', 'currency_code' => 'USD', 'latitude' => 37.0902, 'longitude' => -95.7129],
            ['name' => 'China', 'cca2' => 'CN', 'cca3' => 'CHN', 'capital' => 'Beijing', 'region' => 'Asia', 'currency_code' => 'CNY', 'latitude' => 35.8617, 'longitude' => 104.1954],
            ['name' => 'Germany', 'cca2' => 'DE', 'cca3' => 'DEU', 'capital' => 'Berlin', 'region' => 'Europe', 'currency_code' => 'EUR', 'latitude' => 51.1657, 'longitude' => 10.4515],
            ['name' => 'Japan', 'cca2' => 'JP', 'cca3' => 'JPN', 'capital' => 'Tokyo', 'region' => 'Asia', 'currency_code' => 'JPY', 'latitude' => 36.2048, 'longitude' => 138.2529],
            ['name' => 'United Kingdom', 'cca2' => 'GB', 'cca3' => 'GBR', 'capital' => 'London', 'region' => 'Europe', 'currency_code' => 'GBP', 'latitude' => 55.3781, 'longitude' => -3.4360],
            ['name' => 'Australia', 'cca2' => 'AU', 'cca3' => 'AUS', 'capital' => 'Canberra', 'region' => 'Oceania', 'currency_code' => 'AUD', 'latitude' => -25.2744, 'longitude' => 133.7751],
            ['name' => 'Singapore', 'cca2' => 'SG', 'cca3' => 'SGP', 'capital' => 'Singapore', 'region' => 'Asia', 'currency_code' => 'SGD', 'latitude' => 1.3521, 'longitude' => 103.8198],
            ['name' => 'Malaysia', 'cca2' => 'MY', 'cca3' => 'MYS', 'capital' => 'Kuala Lumpur', 'region' => 'Asia', 'currency_code' => 'MYR', 'latitude' => 4.2105, 'longitude' => 101.9758],
            ['name' => 'Thailand', 'cca2' => 'TH', 'cca3' => 'THA', 'capital' => 'Bangkok', 'region' => 'Asia', 'currency_code' => 'THB', 'latitude' => 15.8700, 'longitude' => 100.9925],
            ['name' => 'Vietnam', 'cca2' => 'VN', 'cca3' => 'VNM', 'capital' => 'Hanoi', 'region' => 'Asia', 'currency_code' => 'VND', 'latitude' => 14.0583, 'longitude' => 108.2772],
            ['name' => 'India', 'cca2' => 'IN', 'cca3' => 'IND', 'capital' => 'New Delhi', 'region' => 'Asia', 'currency_code' => 'INR', 'latitude' => 20.5937, 'longitude' => 78.9629],
            ['name' => 'Brazil', 'cca2' => 'BR', 'cca3' => 'BRA', 'capital' => 'Brasília', 'region' => 'Americas', 'currency_code' => 'BRL', 'latitude' => -14.2350, 'longitude' => -51.9253],
            ['name' => 'Russia', 'cca2' => 'RU', 'cca3' => 'RUS', 'capital' => 'Moscow', 'region' => 'Europe', 'currency_code' => 'RUB', 'latitude' => 61.5240, 'longitude' => 105.3188],
            ['name' => 'France', 'cca2' => 'FR', 'cca3' => 'FRA', 'capital' => 'Paris', 'region' => 'Europe', 'currency_code' => 'EUR', 'latitude' => 46.2276, 'longitude' => 2.2137],
            ['name' => 'Italy', 'cca2' => 'IT', 'cca3' => 'ITA', 'capital' => 'Rome', 'region' => 'Europe', 'currency_code' => 'EUR', 'latitude' => 41.8719, 'longitude' => 12.5674],
            ['name' => 'Canada', 'cca2' => 'CA', 'cca3' => 'CAN', 'capital' => 'Ottawa', 'region' => 'Americas', 'currency_code' => 'CAD', 'latitude' => 56.1304, 'longitude' => -106.3468],
            ['name' => 'South Korea', 'cca2' => 'KR', 'cca3' => 'KOR', 'capital' => 'Seoul', 'region' => 'Asia', 'currency_code' => 'KRW', 'latitude' => 35.9078, 'longitude' => 127.7669],
            ['name' => 'Netherlands', 'cca2' => 'NL', 'cca3' => 'NLD', 'capital' => 'Amsterdam', 'region' => 'Europe', 'currency_code' => 'EUR', 'latitude' => 52.1326, 'longitude' => 5.2913],
            ['name' => 'United Arab Emirates', 'cca2' => 'AE', 'cca3' => 'ARE', 'capital' => 'Abu Dhabi', 'region' => 'Asia', 'currency_code' => 'AED', 'latitude' => 23.4241, 'longitude' => 53.8478],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['cca2' => $country['cca2']],
                $country
            );
        }

        $this->command->info('✅ ' . count($countries) . ' Countries seeded successfully!');
    }
}