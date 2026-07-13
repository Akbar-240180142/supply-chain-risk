<?php

namespace App\Services;

use App\Models\Port;
use App\Models\Country;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PortService
{
    /**
     * Fetch port data from tayljordan/ports GitHub dataset and sync to DB.
     * Only imports ports for countries we track in our system.
     */
    public function fetchAndSync()
    {
        Log::info('PortService: Fetching port locations from tayljordan/ports dataset...');

        try {
            // Correct URL: ports.json is at the repo root, branch = main
            $response = Http::timeout(60)->retry(2, 2000)->get(
                'https://raw.githubusercontent.com/tayljordan/ports/main/ports.json'
            );

            if (!$response->successful()) {
                Log::error('PortService: Failed to fetch ports. Status: ' . $response->status());
                return 0;
            }

            $data = $response->json();
            $portsArray = $data['ports'] ?? [];

            if (empty($portsArray)) {
                Log::warning('PortService: No ports found in API response.');
                return 0;
            }

            // Build country lookup map (case-insensitive)
            $countries = Country::all();
            $countryMap = [];
            foreach ($countries as $c) {
                $countryMap[strtolower($c->name)] = $c;
            }

            // Also handle common name variations
            $aliases = [
                'united states of america' => 'united states',
                'usa'                      => 'united states',
                'korea, republic of'       => 'south korea',
                'south korea'              => 'south korea',
                'republic of korea'        => 'south korea',
                'uae'                      => 'united arab emirates',
                'uk'                       => 'united kingdom',
                'great britain'            => 'united kingdom',
                'russian federation'       => 'russia',
                'viet nam'                 => 'vietnam',
                'holland'                  => 'netherlands',
                'brasil'                   => 'brazil',
            ];

            $count = 0;

            foreach ($portsArray as $portItem) {
                $countryName = $portItem['country'] ?? null;
                if (!$countryName) continue;

                // Try exact match first, then alias
                $key = strtolower(trim($countryName));
                $matchedCountry = $countryMap[$key] ?? null;

                if (!$matchedCountry && isset($aliases[$key])) {
                    $matchedCountry = $countryMap[$aliases[$key]] ?? null;
                }

                // Skip if not one of our monitored countries
                if (!$matchedCountry) continue;

                $portName = $portItem['wpi_port_name'] ?? $portItem['point_of_interest'] ?? 'Unknown Port';
                $portSize = $portItem['port_size'] ?? 'Medium';

                // Generate port code from WPI ID
                $wpiId = $portItem['wpi_port_id'] ?? $count;
                $portCode = 'WPI' . str_pad($wpiId, 5, '0', STR_PAD_LEFT);
                if (strlen($portCode) > 10) {
                    $portCode = substr($portCode, 0, 10);
                }

                Port::updateOrCreate(
                    [
                        'port_name' => $portName,
                        'country_id' => $matchedCountry->id,
                    ],
                    [
                        'name'         => $portName,
                        'code'         => $portCode,
                        'country_name' => $matchedCountry->name,
                        'latitude'     => floatval($portItem['latitude'] ?? 0),
                        'longitude'    => floatval($portItem['longitude'] ?? 0),
                        'harbor_size'  => $portSize,
                        'is_active'    => true,
                        'status'       => 'Active',
                    ]
                );

                $count++;
            }

            Log::info("PortService: Successfully synced {$count} ports from API.");
            return $count;

        } catch (\Exception $e) {
            Log::error('PortService: Exception during port fetch: ' . $e->getMessage());
            return 0;
        }
    }
}
