<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Country;
use App\Models\EconomicIndicator;
use Illuminate\Support\Facades\Log;

class EconomicService
{
    public function fetchAndSync()
    {
        $countries = Country::all();
        $count = 0;
        $failedCountries = [];

        foreach ($countries as $country) {
            if (!$country->cca2) continue;

            try {
                // Fetch GDP & Inflation dari World Bank API
                $gdpUrl = "https://api.worldbank.org/v2/country/{$country->cca2}/indicator/NY.GDP.MKTP.CD?format=json&date=2019:2024&per_page=100";
                $inflationUrl = "https://api.worldbank.org/v2/country/{$country->cca2}/indicator/FP.CPI.TOTL.ZG?format=json&date=2019:2024&per_page=100";

                $gdpResponse = Http::timeout(60)->get($gdpUrl);
                $inflationResponse = Http::timeout(60)->get($inflationUrl);

                if ($gdpResponse->successful() && $inflationResponse->successful()) {
                    $gdpData = $gdpResponse->json()[1] ?? [];
                    $inflationData = $inflationResponse->json()[1] ?? [];

                    if (is_array($gdpData) && count($gdpData) > 0) {
                        foreach ($gdpData as $item) {
                            if ($item['value'] === null) continue;

                            $year = $item['date'];
                            $gdp = floatval($item['value']);
                            
                            $inflationItem = collect($inflationData)->firstWhere('date', $year);
                            $inflation = $inflationItem && $inflationItem['value'] !== null 
                                         ? floatval($inflationItem['value']) 
                                         : 0.00;

                            EconomicIndicator::updateOrCreate(
                                ['country_id' => $country->id, 'year' => $year],
                                ['gdp' => $gdp, 'inflation_rate' => $inflation]
                            );
                            $count++;
                        }
                    } else {
                        $failedCountries[] = $country->name . " (no data)";
                    }
                } else {
                    $failedCountries[] = $country->name . " (API error)";
                }
            } catch (\Exception $e) {
                Log::error("Error fetching {$country->name}: " . $e->getMessage());
                $failedCountries[] = $country->name . " (exception)";
                continue;
            }
        }

        if (count($failedCountries) > 0) {
            Log::warning("Failed countries: " . implode(', ', $failedCountries));
        }

        return $count;
    }
}