<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Country;
use App\Models\EconomicIndicator;
use Illuminate\Support\Facades\Log;

class EconomicService
{
    /**
     * Sync economic indicators for all tracked countries.
     */
    public function fetchAndSync()
    {
        $countries = Country::all();
        $count = 0;
        $failedCountries = [];

        foreach ($countries as $country) {
            if ($this->fetchForCountry($country)) {
                $count++;
            } else {
                $failedCountries[] = $country->name;
            }
        }

        if (count($failedCountries) > 0) {
            Log::warning("EconomicService: Failed countries: " . implode(', ', $failedCountries));
        }

        return $count;
    }

    /**
     * Fetch and cache economic indicators for a single country.
     */
    public function fetchForCountry(Country $country)
    {
        if (!$country->cca2) {
            return false;
        }

        try {
            Log::info("EconomicService: Fetching GDP, Inflation, Population, Exports, and Imports for {$country->name}...");
            
            // Fetch indicators from World Bank API for 2019-2024
            $gdpUrl = "https://api.worldbank.org/v2/country/{$country->cca2}/indicator/NY.GDP.MKTP.CD?format=json&date=2019:2024&per_page=100";
            $inflationUrl = "https://api.worldbank.org/v2/country/{$country->cca2}/indicator/FP.CPI.TOTL.ZG?format=json&date=2019:2024&per_page=100";
            $populationUrl = "https://api.worldbank.org/v2/country/{$country->cca2}/indicator/SP.POP.TOTL?format=json&date=2019:2024&per_page=100";
            $exportsUrl = "https://api.worldbank.org/v2/country/{$country->cca2}/indicator/NE.EXP.GNFS.CD?format=json&date=2019:2024&per_page=100";
            $importsUrl = "https://api.worldbank.org/v2/country/{$country->cca2}/indicator/NE.IMP.GNFS.CD?format=json&date=2019:2024&per_page=100";

            $gdpResponse = Http::timeout(20)->get($gdpUrl);
            $inflationResponse = Http::timeout(20)->get($inflationUrl);
            $populationResponse = Http::timeout(20)->get($populationUrl);
            $exportsResponse = Http::timeout(20)->get($exportsUrl);
            $importsResponse = Http::timeout(20)->get($importsUrl);

            if ($gdpResponse->successful() && $inflationResponse->successful()) {
                $gdpData = $gdpResponse->json()[1] ?? [];
                $inflationData = $inflationResponse->json()[1] ?? [];
                $populationData = $populationResponse->successful() ? ($populationResponse->json()[1] ?? []) : [];
                $exportsData = $exportsResponse->successful() ? ($exportsResponse->json()[1] ?? []) : [];
                $importsData = $importsResponse->successful() ? ($importsResponse->json()[1] ?? []) : [];

                if (is_array($gdpData) && count($gdpData) > 0) {
                    foreach ($gdpData as $item) {
                        if ($item['value'] === null) continue;

                        $year = $item['date'];
                        $gdp = floatval($item['value']);
                        
                        // Get inflation data
                        $inflationItem = collect($inflationData)->firstWhere('date', $year);
                        $inflation = $inflationItem && $inflationItem['value'] !== null 
                                     ? floatval($inflationItem['value']) 
                                     : 0.00;

                        // Get population data
                        $populationItem = collect($populationData)->firstWhere('date', $year);
                        $population = $populationItem && $populationItem['value'] !== null 
                                      ? intval($populationItem['value']) 
                                      : 0;

                        // Get exports data
                        $exportsItem = collect($exportsData)->firstWhere('date', $year);
                        $exports = $exportsItem && $exportsItem['value'] !== null 
                                   ? floatval($exportsItem['value']) 
                                   : 0.00;

                        // Get imports data
                        $importsItem = collect($importsData)->firstWhere('date', $year);
                        $imports = $importsItem && $importsItem['value'] !== null 
                                   ? floatval($importsItem['value']) 
                                   : 0.00;

                        EconomicIndicator::updateOrCreate(
                            ['country_id' => $country->id, 'year' => $year],
                            [
                                'gdp'            => $gdp, 
                                'inflation_rate' => $inflation,
                                'population'     => $population,
                                'exports'        => $exports,
                                'imports'        => $imports,
                            ]
                        );
                    }
                    return true;
                }
            }
        } catch (\Exception $e) {
            Log::error("EconomicService: Error fetching {$country->name}: " . $e->getMessage());
        }
        return false;
    }
}