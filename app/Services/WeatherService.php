<?php

namespace App\Services;

use App\Models\Country;
use App\Models\WeatherCache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    public function fetchAndSync()
    {
        Log::info('Fetching weather data from Open-Meteo API...');
        $countries = Country::whereNotNull('latitude')->get();
        $count = 0;
        $failed = 0;

        foreach ($countries as $country) {
            try {
                $response = Http::timeout(45)->retry(2, 1500)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => $country->latitude,
                    'longitude' => $country->longitude,
                    'current' => 'temperature_2m,wind_speed_10m,rain,weather_code',
                    'timezone' => 'auto'
                ]);

                if ($response->successful()) {
                    $data = $response->json()['current'];
                    $isStorm = in_array($data['weather_code'], [95, 96, 99]);

                    WeatherCache::updateOrCreate(
                        ['country_id' => $country->id],
                        [
                            'temperature' => $data['temperature_2m'],
                            'rain' => $data['rain'],
                            'wind_speed' => $data['wind_speed_10m'],
                            'is_storm' => $isStorm,
                            'fetched_at' => now(),
                        ]
                    );
                    $count++;
                } else {
                    $failed++;
                }
            } catch (\Exception $e) {
                Log::error("Weather error for {$country->name}: " . $e->getMessage());
                $failed++;
                continue;
            }
        }

        Log::info("Weather sync complete. Success: {$count}, Failed: {$failed}");
        return $count;
    }
}