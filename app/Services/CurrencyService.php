<?php

namespace App\Services;

use App\Models\CurrencyRate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    public function fetchAndSync()
    {
        Log::info('Fetching currency rates from ExchangeRate API...');
        
        try {
            $response = Http::timeout(30)->retry(3, 2000)->get('https://api.exchangerate-api.com/v4/latest/USD');

            if ($response->successful()) {
                $rates = $response->json()['rates'];
                $count = 0;

                foreach ($rates as $currency => $rate) {
                    CurrencyRate::updateOrCreate(
                        ['base_currency' => 'USD', 'target_currency' => $currency, 'record_date' => today()],
                        ['rate' => $rate]
                    );
                    $count++;
                }
                
                Log::info("Successfully synced {$count} currency rates.");
                return $count;
            } else {
                Log::error('Failed to fetch currency rates: HTTP ' . $response->status());
                return 0;
            }
        } catch (\Exception $e) {
            Log::error('Exception in CurrencyService: ' . $e->getMessage());
            Log::warning('Skipping currency sync due to connection error. Using existing data if available.');
            return 0;
        }
    }
}