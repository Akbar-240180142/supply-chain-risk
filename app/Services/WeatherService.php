<?php

namespace App\Services;

use App\Models\Country;
use App\Models\WeatherCache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    /**
     * Fetch weather for all tracked countries in a single batched API call.
     */
    public function fetchAndSync()
    {
        Log::info('WeatherService: Fetching weather data from Open-Meteo API (batched)...');
        
        $countries = Country::whereNotNull('latitude')->whereNotNull('longitude')->get();
        if ($countries->isEmpty()) {
            Log::info('WeatherService: No countries with coordinates found.');
            return 0;
        }

        // Combine all coordinates into comma-separated strings for batch request
        $latitudes = $countries->pluck('latitude')->implode(',');
        $longitudes = $countries->pluck('longitude')->implode(',');

        try {
            $response = Http::timeout(30)->retry(2, 1000)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $latitudes,
                'longitude' => $longitudes,
                'current' => 'temperature_2m,wind_speed_10m,rain,weather_code',
                'timezone' => 'auto'
            ]);

            if ($response->successful()) {
                $results = $response->json();
                $count = 0;

                // Open-Meteo returns an array of objects if multiple coordinate pairs are queried.
                // If only one pair is returned, it might be a single object.
                $dataArray = is_array($results) && isset($results[0]) ? $results : [$results];

                foreach ($countries as $index => $country) {
                    $currentData = $dataArray[$index]['current'] ?? null;
                    if ($currentData) {
                        $isStorm = in_array($currentData['weather_code'], [95, 96, 99]);

                        WeatherCache::updateOrCreate(
                            ['country_id' => $country->id],
                            [
                                'temperature' => floatval($currentData['temperature_2m']),
                                'rain' => floatval($currentData['rain']),
                                'wind_speed' => floatval($currentData['wind_speed_10m']),
                                'is_storm' => $isStorm,
                                'fetched_at' => now(),
                            ]
                        );
                        $count++;
                    }
                }

                Log::info("WeatherService: Successfully batched synced weather for {$count} countries.");
                return $count;
            } else {
                Log::error('WeatherService: Failed to fetch weather. Status code: ' . $response->status());
                return 0;
            }
        } catch (\Exception $e) {
            Log::error('WeatherService: Exception during weather sync: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Fetch weather on-demand for a single country.
     */
    public function fetchForCountry(Country $country)
    {
        if (!$country->latitude || !$country->longitude) {
            return false;
        }

        try {
            Log::info("WeatherService: Fetching weather for {$country->name}...");
            $response = Http::timeout(10)->retry(2, 1000)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $country->latitude,
                'longitude' => $country->longitude,
                'current' => 'temperature_2m,wind_speed_10m,rain,weather_code',
                'timezone' => 'auto'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $currentData = $data['current'] ?? null;

                if ($currentData) {
                    $isStorm = in_array($currentData['weather_code'], [95, 96, 99]);

                    WeatherCache::updateOrCreate(
                        ['country_id' => $country->id],
                        [
                            'temperature' => floatval($currentData['temperature_2m']),
                            'rain' => floatval($currentData['rain']),
                            'wind_speed' => floatval($currentData['wind_speed_10m']),
                            'is_storm' => $isStorm,
                            'fetched_at' => now(),
                        ]
                    );
                    return true;
                }
            }
        } catch (\Exception $e) {
            Log::error("WeatherService: Failed to fetch weather for {$country->name}: " . $e->getMessage());
        }
        return false;
    }
}