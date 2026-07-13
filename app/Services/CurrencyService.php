<?php

namespace App\Services;

use App\Models\CurrencyRate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    public function fetchAndSync()
    {
        Log::info('Fetching currency rates from open.er-api.com...');
        
        try {
            // Gunakan open.er-api.com — gratis, tanpa API key
            $response = Http::timeout(30)->retry(3, 2000)->get('https://open.er-api.com/v6/latest/USD');

            if ($response->successful()) {
                $data = $response->json();

                if (($data['result'] ?? '') !== 'success') {
                    Log::error('open.er-api.com returned error result.');
                    return $this->fallbackToExchangeRateApi();
                }

                $rates = $data['rates'];
                $count = 0;

                foreach ($rates as $currency => $rate) {
                    CurrencyRate::updateOrCreate(
                        ['base_currency' => 'USD', 'target_currency' => $currency, 'record_date' => today()],
                        ['rate' => $rate]
                    );
                    $count++;
                }
                
                Log::info("Successfully synced {$count} currency rates from open.er-api.com.");
                return $count;
            } else {
                Log::warning('open.er-api.com failed (HTTP ' . $response->status() . '), trying fallback...');
                return $this->fallbackToExchangeRateApi();
            }
        } catch (\Exception $e) {
            Log::error('Exception in CurrencyService: ' . $e->getMessage());
            Log::warning('Trying fallback currency API...');
            return $this->fallbackToExchangeRateApi();
        }
    }

    private function fallbackToExchangeRateApi()
    {
        try {
            $response = Http::timeout(30)->retry(2, 2000)->get('https://api.exchangerate-api.com/v4/latest/USD');

            if ($response->successful()) {
                $rates = $response->json()['rates'] ?? [];
                $count = 0;

                foreach ($rates as $currency => $rate) {
                    CurrencyRate::updateOrCreate(
                        ['base_currency' => 'USD', 'target_currency' => $currency, 'record_date' => today()],
                        ['rate' => $rate]
                    );
                    $count++;
                }

                Log::info("Fallback: synced {$count} currency rates from exchangerate-api.com.");
                return $count;
            }
        } catch (\Exception $e) {
            Log::error('Fallback currency API also failed: ' . $e->getMessage());
        }

        // Jika semua API gagal, return 0 tapi jangan crash — data lama masih ada di DB
        Log::warning('All currency APIs failed. Using existing DB data.');
        return 0;
    }

    /**
     * Cek apakah data currency sudah up-to-date (hari ini).
     */
    public function isDataFresh(): bool
    {
        return CurrencyRate::where('record_date', today())->exists();
    }

    /**
     * Refresh currency jika data sudah lebih dari 1 hari.
     */
    public function autoRefreshIfStale(): void
    {
        if (!$this->isDataFresh()) {
            Log::info('Currency data is stale. Auto-refreshing...');
            $this->fetchAndSync();
        }
    }
}