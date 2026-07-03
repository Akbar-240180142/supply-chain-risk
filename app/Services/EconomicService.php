<?php

namespace App\Services;

use App\Models\Country;
use App\Models\EconomicIndicator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EconomicService
{
    public function fetchAndSync()
    {
        Log::info('Fetching economic data from World Bank API...');
        $countries = Country::whereNotNull('cca3')->get();
        $count = 0;
        $failed = 0;

        foreach ($countries as $country) {
            try {
                // Fetch GDP
                $gdpResponse = Http::timeout(60)->retry(2, 1000)->get("https://api.worldbank.org/v2/country/{$country->cca3}/indicator/NY.GDP.MKTP.CD", [
                    'format' => 'json',
                    'per_page' => 1,
                    'date' => '2022:2023'
                ]);

                // Fetch Inflation
                $infResponse = Http::timeout(60)->retry(2, 1000)->get("https://api.worldbank.org/v2/country/{$country->cca3}/indicator/FP.CPI.TOTL.ZG", [
                    'format' => 'json',
                    'per_page' => 1,
                    'date' => '2022:2023'
                ]);

                $gdp = null;
                $inflation = null;
                $year = 2023;

                if ($gdpResponse->successful() && isset($gdpResponse->json()[1][0])) {
                    $gdp = $gdpResponse->json()[1][0]['value'];
                    $year = $gdpResponse->json()[1][0]['date'];
                }

                if ($infResponse->successful() && isset($infResponse->json()[1][0])) {
                    $inflation = $infResponse->json()[1][0]['value'];
                }

                if ($gdp !== null || $inflation !== null) {
                    EconomicIndicator::updateOrCreate(
                        ['country_id' => $country->id, 'year' => $year],
                        [
                            'gdp' => $gdp,
                            'inflation_rate' => $inflation,
                        ]
                    );
                    $count++;
                } else {
                    $failed++;
                }
            } catch (\Exception $e) {
                Log::error("Exception fetching economic data for {$country->name}: " . $e->getMessage());
                $failed++;
                continue;
            }
        }

        Log::info("Successfully synced economic data for {$count} countries. Failed: {$failed}");
        return $count;
    }
}