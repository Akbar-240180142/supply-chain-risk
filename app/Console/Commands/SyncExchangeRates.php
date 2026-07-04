<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SyncExchangeRates extends Command
{
    protected $signature = 'currency:sync';
    protected $description = 'Sync real-time exchange rates from API';

    public function handle()
    {
        $this->info('🔄 Fetching real-time exchange rates from API...');

        try {
            // API gratis tanpa key
            $response = Http::timeout(30)->get('https://open.er-api.com/v6/latest/USD');

            if ($response->successful()) {
                $data = $response->json();
                
                // Cek apakah API berhasil
                if (($data['result'] ?? '') !== 'success') {
                    $this->error('❌ API returned an error.');
                    return 1;
                }

                $rates = $data['rates'];
                $apiDate = $data['time_last_update_utc'] ?? 'Unknown';
                $this->info("✅ API Response received. Last update: {$apiDate}");

                $today = Carbon::today()->toDateString();
                $count = 0;

                // Simpan ke database
                foreach ($rates as $targetCurrency => $rate) {
                    DB::table('currency_rates')->updateOrInsert(
                        [
                            'base_currency' => 'USD',
                            'target_currency' => $targetCurrency,
                            'record_date' => $today
                        ],
                        [
                            'rate' => $rate,
                            'updated_at' => now(),
                            'created_at' => now()
                        ]
                    );
                    $count++;
                }

                $idrRate = $rates['IDR'] ?? 0;
                $this->info("✅ Successfully synced {$count} exchange rates!");
                $this->info("💱 1 USD = " . number_format($idrRate, 2) . " IDR");
                
                return 0;
            } else {
                $this->error('❌ Failed to connect to API. Status: ' . $response->status());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}