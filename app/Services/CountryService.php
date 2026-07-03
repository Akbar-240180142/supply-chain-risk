<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CountryService
{
    public function fetchAndSync()
    {
        // Skip jika sudah ada data di database
        if (Country::count() > 0) {
            Log::info('Countries already exist in database. Skipping API fetch.');
            return 0;
        }

        Log::info('Fetching countries from REST Countries API...');
        
        try {
            $response = Http::timeout(120)->retry(3, 2000)->get('https://restcountries.com/v3.1/all');

            if ($response->successful()) {
                $countries = $response->json();
                $count = 0;

                foreach ($countries as $data) {
                    $name = $data['name']['common'] ?? null;
                    $cca2 = $data['cca2'] ?? null;

                    if ($name && $cca2) {
                        Country::updateOrCreate(
                            ['cca2' => $cca2],
                            [
                                'name' => $name,
                                'cca3' => $data['cca3'] ?? null,
                                'capital' => $data['capital'][0] ?? null,
                                'region' => $data['region'] ?? null,
                                'currency_code' => isset($data['currencies']) ? array_keys($data['currencies'])[0] : null,
                                'latitude' => $data['latlng'][0] ?? null,
                                'longitude' => $data['latlng'][1] ?? null,
                            ]
                        );
                        $count++;
                    }
                }

                Log::info("Successfully synced {$count} countries.");
                return $count;
            } else {
                Log::error('Failed to fetch countries: ' . $response->status());
                return 0;
            }
        } catch (\Exception $e) {
            Log::error('Exception in CountryService: ' . $e->getMessage());
            return 0;
        }
    }
}