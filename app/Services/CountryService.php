<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CountryService
{
    /**
     * Target country codes for supply chain monitoring (20 major trading nations).
     */
    private const TARGET_CODES = [
        // Original 20
        'ID','US','CN','DE','JP','GB','AU','SG','MY','TH','VN','IN','BR','RU','FR','IT','CA','KR','NL','AE',
        // Additional 30
        'SA','ZA','MX','AR','TR','EG','NG','PH','PK','BD','IR','PL','SE','BE','CH','ES','CO','CL','PE','NZ',
        'IL','QA','KW','NO','DK','FI','IE','PT','GR','CZ'
    ];

    public function fetchAndSync()
    {
        // Skip jika sudah ada data di database
        if (Country::count() > 0) {
            Log::info('Countries already exist in database. Skipping API fetch.');
            return 0;
        }

        // 1. Coba fetch dari REST Countries API
        $count = $this->fetchFromApi();

        // 2. Jika API gagal (deprecated/down), gunakan data fallback built-in
        if ($count === 0) {
            Log::warning('REST Countries API unavailable. Using built-in fallback data...');
            $count = $this->seedFromFallback();
        }

        return $count;
    }

    /**
     * Fetch dari REST Countries API (mencoba v3.1 lalu fallback)
     */
    private function fetchFromApi(): int
    {
        $codes = implode(',', self::TARGET_CODES);
        Log::info("Fetching countries ({$codes}) from REST Countries API...");

        try {
            $response = Http::timeout(30)->retry(2, 1000)->get("https://restcountries.com/v3.1/alpha?codes={$codes}");

            if (!$response->successful()) {
                Log::warning('REST Countries API returned status: ' . $response->status());
                return 0;
            }

            $json = $response->json();

            // Cek apakah response berisi error (API deprecated)
            if (isset($json['success']) && $json['success'] === false) {
                Log::warning('REST Countries API is deprecated: ' . ($json['errors'][0]['message'] ?? 'Unknown error'));
                return 0;
            }

            // Response valid: array of country objects
            if (!is_array($json) || empty($json)) {
                return 0;
            }

            $count = 0;
            foreach ($json as $data) {
                $name = $data['name']['common'] ?? null;
                $cca2 = $data['cca2'] ?? null;

                if ($name && $cca2) {
                    Country::updateOrCreate(
                        ['cca2' => $cca2],
                        [
                            'name'          => $name,
                            'cca3'          => $data['cca3'] ?? null,
                            'capital'       => $data['capital'][0] ?? null,
                            'region'        => $data['region'] ?? null,
                            'currency_code' => isset($data['currencies']) ? array_keys($data['currencies'])[0] : null,
                            'latitude'      => $data['latlng'][0] ?? null,
                            'longitude'     => $data['latlng'][1] ?? null,
                        ]
                    );
                    $count++;
                }
            }

            Log::info("Successfully synced {$count} countries from API.");
            return $count;

        } catch (\Exception $e) {
            Log::error('Exception in CountryService API fetch: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Fallback: seed dari data statis built-in ketika API tidak tersedia.
     * Data ini tetap berasal dari REST Countries API (di-cache secara manual).
     */
    private function seedFromFallback(): int
    {
        $countries = [
            // Original 20
            ['name' => 'Indonesia',            'cca2' => 'ID', 'cca3' => 'IDN', 'capital' => 'Jakarta',          'region' => 'Asia',    'currency_code' => 'IDR', 'latitude' =>  -0.7893, 'longitude' => 113.9213],
            ['name' => 'United States',         'cca2' => 'US', 'cca3' => 'USA', 'capital' => 'Washington, D.C.', 'region' => 'Americas','currency_code' => 'USD', 'latitude' =>  37.0902, 'longitude' => -95.7129],
            ['name' => 'China',                 'cca2' => 'CN', 'cca3' => 'CHN', 'capital' => 'Beijing',          'region' => 'Asia',    'currency_code' => 'CNY', 'latitude' =>  35.8617, 'longitude' => 104.1954],
            ['name' => 'Germany',               'cca2' => 'DE', 'cca3' => 'DEU', 'capital' => 'Berlin',           'region' => 'Europe',  'currency_code' => 'EUR', 'latitude' =>  51.1657, 'longitude' =>  10.4515],
            ['name' => 'Japan',                 'cca2' => 'JP', 'cca3' => 'JPN', 'capital' => 'Tokyo',            'region' => 'Asia',    'currency_code' => 'JPY', 'latitude' =>  36.2048, 'longitude' => 138.2529],
            ['name' => 'United Kingdom',        'cca2' => 'GB', 'cca3' => 'GBR', 'capital' => 'London',           'region' => 'Europe',  'currency_code' => 'GBP', 'latitude' =>  55.3781, 'longitude' =>  -3.4360],
            ['name' => 'Australia',             'cca2' => 'AU', 'cca3' => 'AUS', 'capital' => 'Canberra',         'region' => 'Oceania', 'currency_code' => 'AUD', 'latitude' => -25.2744, 'longitude' => 133.7751],
            ['name' => 'Singapore',             'cca2' => 'SG', 'cca3' => 'SGP', 'capital' => 'Singapore',        'region' => 'Asia',    'currency_code' => 'SGD', 'latitude' =>   1.3521, 'longitude' => 103.8198],
            ['name' => 'Malaysia',              'cca2' => 'MY', 'cca3' => 'MYS', 'capital' => 'Kuala Lumpur',     'region' => 'Asia',    'currency_code' => 'MYR', 'latitude' =>   4.2105, 'longitude' => 101.9758],
            ['name' => 'Thailand',              'cca2' => 'TH', 'cca3' => 'THA', 'capital' => 'Bangkok',          'region' => 'Asia',    'currency_code' => 'THB', 'latitude' =>  15.8700, 'longitude' => 100.9925],
            ['name' => 'Vietnam',               'cca2' => 'VN', 'cca3' => 'VNM', 'capital' => 'Hanoi',            'region' => 'Asia',    'currency_code' => 'VND', 'latitude' =>  14.0583, 'longitude' => 108.2772],
            ['name' => 'India',                 'cca2' => 'IN', 'cca3' => 'IND', 'capital' => 'New Delhi',        'region' => 'Asia',    'currency_code' => 'INR', 'latitude' =>  20.5937, 'longitude' =>  78.9629],
            ['name' => 'Brazil',                'cca2' => 'BR', 'cca3' => 'BRA', 'capital' => 'Brasília',         'region' => 'Americas','currency_code' => 'BRL', 'latitude' => -14.2350, 'longitude' => -51.9253],
            ['name' => 'Russia',                'cca2' => 'RU', 'cca3' => 'RUS', 'capital' => 'Moscow',           'region' => 'Europe',  'currency_code' => 'RUB', 'latitude' =>  61.5240, 'longitude' => 105.3188],
            ['name' => 'France',                'cca2' => 'FR', 'cca3' => 'FRA', 'capital' => 'Paris',            'region' => 'Europe',  'currency_code' => 'EUR', 'latitude' =>  46.2276, 'longitude' =>   2.2137],
            ['name' => 'Italy',                 'cca2' => 'IT', 'cca3' => 'ITA', 'capital' => 'Rome',             'region' => 'Europe',  'currency_code' => 'EUR', 'latitude' =>  41.8719, 'longitude' =>  12.5674],
            ['name' => 'Canada',                'cca2' => 'CA', 'cca3' => 'CAN', 'capital' => 'Ottawa',           'region' => 'Americas','currency_code' => 'CAD', 'latitude' =>  56.1304, 'longitude' => -106.3468],
            ['name' => 'South Korea',           'cca2' => 'KR', 'cca3' => 'KOR', 'capital' => 'Seoul',            'region' => 'Asia',    'currency_code' => 'KRW', 'latitude' =>  35.9078, 'longitude' => 127.7669],
            ['name' => 'Netherlands',           'cca2' => 'NL', 'cca3' => 'NLD', 'capital' => 'Amsterdam',        'region' => 'Europe',  'currency_code' => 'EUR', 'latitude' =>  52.1326, 'longitude' =>   5.2913],
            ['name' => 'United Arab Emirates',  'cca2' => 'AE', 'cca3' => 'ARE', 'capital' => 'Abu Dhabi',        'region' => 'Asia',    'currency_code' => 'AED', 'latitude' =>  23.4241, 'longitude' =>  53.8478],
            // Additional 30 (Approximated fallback data for stability)
            ['name' => 'Saudi Arabia',          'cca2' => 'SA', 'cca3' => 'SAU', 'capital' => 'Riyadh',           'region' => 'Asia',    'currency_code' => 'SAR', 'latitude' => 23.8859, 'longitude' => 45.0792],
            ['name' => 'South Africa',          'cca2' => 'ZA', 'cca3' => 'ZAF', 'capital' => 'Pretoria',         'region' => 'Africa',  'currency_code' => 'ZAR', 'latitude' => -30.5595, 'longitude' => 22.9375],
            ['name' => 'Mexico',                'cca2' => 'MX', 'cca3' => 'MEX', 'capital' => 'Mexico City',      'region' => 'Americas','currency_code' => 'MXN', 'latitude' => 23.6345, 'longitude' => -102.5528],
            ['name' => 'Argentina',             'cca2' => 'AR', 'cca3' => 'ARG', 'capital' => 'Buenos Aires',     'region' => 'Americas','currency_code' => 'ARS', 'latitude' => -38.4161, 'longitude' => -63.6167],
            ['name' => 'Turkey',                'cca2' => 'TR', 'cca3' => 'TUR', 'capital' => 'Ankara',           'region' => 'Asia',    'currency_code' => 'TRY', 'latitude' => 38.9637, 'longitude' => 35.2433],
            ['name' => 'Egypt',                 'cca2' => 'EG', 'cca3' => 'EGY', 'capital' => 'Cairo',            'region' => 'Africa',  'currency_code' => 'EGP', 'latitude' => 26.8206, 'longitude' => 30.8025],
            ['name' => 'Nigeria',               'cca2' => 'NG', 'cca3' => 'NGA', 'capital' => 'Abuja',            'region' => 'Africa',  'currency_code' => 'NGN', 'latitude' => 9.0820, 'longitude' => 8.6753],
            ['name' => 'Philippines',           'cca2' => 'PH', 'cca3' => 'PHL', 'capital' => 'Manila',           'region' => 'Asia',    'currency_code' => 'PHP', 'latitude' => 12.8797, 'longitude' => 121.7740],
            ['name' => 'Pakistan',              'cca2' => 'PK', 'cca3' => 'PAK', 'capital' => 'Islamabad',        'region' => 'Asia',    'currency_code' => 'PKR', 'latitude' => 30.3753, 'longitude' => 69.3451],
            ['name' => 'Bangladesh',            'cca2' => 'BD', 'cca3' => 'BGD', 'capital' => 'Dhaka',            'region' => 'Asia',    'currency_code' => 'BDT', 'latitude' => 23.6850, 'longitude' => 90.3563],
            ['name' => 'Iran',                  'cca2' => 'IR', 'cca3' => 'IRN', 'capital' => 'Tehran',           'region' => 'Asia',    'currency_code' => 'IRR', 'latitude' => 32.4279, 'longitude' => 53.6880],
            ['name' => 'Poland',                'cca2' => 'PL', 'cca3' => 'POL', 'capital' => 'Warsaw',           'region' => 'Europe',  'currency_code' => 'PLN', 'latitude' => 51.9194, 'longitude' => 19.1451],
            ['name' => 'Sweden',                'cca2' => 'SE', 'cca3' => 'SWE', 'capital' => 'Stockholm',        'region' => 'Europe',  'currency_code' => 'SEK', 'latitude' => 60.1282, 'longitude' => 18.6435],
            ['name' => 'Belgium',               'cca2' => 'BE', 'cca3' => 'BEL', 'capital' => 'Brussels',         'region' => 'Europe',  'currency_code' => 'EUR', 'latitude' => 50.5039, 'longitude' => 4.4699],
            ['name' => 'Switzerland',           'cca2' => 'CH', 'cca3' => 'CHE', 'capital' => 'Bern',             'region' => 'Europe',  'currency_code' => 'CHF', 'latitude' => 46.8182, 'longitude' => 8.2275],
            ['name' => 'Spain',                 'cca2' => 'ES', 'cca3' => 'ESP', 'capital' => 'Madrid',           'region' => 'Europe',  'currency_code' => 'EUR', 'latitude' => 40.4637, 'longitude' => -3.7492],
            ['name' => 'Colombia',              'cca2' => 'CO', 'cca3' => 'COL', 'capital' => 'Bogotá',           'region' => 'Americas','currency_code' => 'COP', 'latitude' => 4.5709, 'longitude' => -74.2973],
            ['name' => 'Chile',                 'cca2' => 'CL', 'cca3' => 'CHL', 'capital' => 'Santiago',         'region' => 'Americas','currency_code' => 'CLP', 'latitude' => -35.6751, 'longitude' => -71.5430],
            ['name' => 'Peru',                  'cca2' => 'PE', 'cca3' => 'PER', 'capital' => 'Lima',             'region' => 'Americas','currency_code' => 'PEN', 'latitude' => -9.1900, 'longitude' => -75.0152],
            ['name' => 'New Zealand',           'cca2' => 'NZ', 'cca3' => 'NZL', 'capital' => 'Wellington',       'region' => 'Oceania', 'currency_code' => 'NZD', 'latitude' => -40.9006, 'longitude' => 174.8860],
            ['name' => 'Israel',                'cca2' => 'IL', 'cca3' => 'ISR', 'capital' => 'Jerusalem',        'region' => 'Asia',    'currency_code' => 'ILS', 'latitude' => 31.0461, 'longitude' => 34.8516],
            ['name' => 'Qatar',                 'cca2' => 'QA', 'cca3' => 'QAT', 'capital' => 'Doha',             'region' => 'Asia',    'currency_code' => 'QAR', 'latitude' => 25.3548, 'longitude' => 51.1839],
            ['name' => 'Kuwait',                'cca2' => 'KW', 'cca3' => 'KWT', 'capital' => 'Kuwait City',      'region' => 'Asia',    'currency_code' => 'KWD', 'latitude' => 29.3117, 'longitude' => 47.4818],
            ['name' => 'Norway',                'cca2' => 'NO', 'cca3' => 'NOR', 'capital' => 'Oslo',             'region' => 'Europe',  'currency_code' => 'NOK', 'latitude' => 60.4720, 'longitude' => 8.4689],
            ['name' => 'Denmark',               'cca2' => 'DK', 'cca3' => 'DNK', 'capital' => 'Copenhagen',       'region' => 'Europe',  'currency_code' => 'DKK', 'latitude' => 56.2639, 'longitude' => 9.5018],
            ['name' => 'Finland',               'cca2' => 'FI', 'cca3' => 'FIN', 'capital' => 'Helsinki',         'region' => 'Europe',  'currency_code' => 'EUR', 'latitude' => 61.9241, 'longitude' => 25.7482],
            ['name' => 'Ireland',               'cca2' => 'IE', 'cca3' => 'IRL', 'capital' => 'Dublin',           'region' => 'Europe',  'currency_code' => 'EUR', 'latitude' => 53.1424, 'longitude' => -7.6921],
            ['name' => 'Portugal',              'cca2' => 'PT', 'cca3' => 'PRT', 'capital' => 'Lisbon',           'region' => 'Europe',  'currency_code' => 'EUR', 'latitude' => 39.3999, 'longitude' => -8.2245],
            ['name' => 'Greece',                'cca2' => 'GR', 'cca3' => 'GRC', 'capital' => 'Athens',           'region' => 'Europe',  'currency_code' => 'EUR', 'latitude' => 39.0742, 'longitude' => 21.8243],
            ['name' => 'Czechia',               'cca2' => 'CZ', 'cca3' => 'CZE', 'capital' => 'Prague',           'region' => 'Europe',  'currency_code' => 'CZK', 'latitude' => 49.8175, 'longitude' => 15.4730],
        ];

        $count = 0;
        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['cca2' => $country['cca2']],
                $country
            );
            $count++;
        }

        Log::info("Seeded {$count} countries from built-in fallback data.");
        return $count;
    }
}