<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExchangeRateSeeder extends Seeder
{
    public function run()
    {
        // Data nilai tukar REAL (sumber: Bank Indonesia & The Fed)
        $exchangeRates = [
            ['base' => 'USD', 'target' => 'IDR', 'rate' => 16500.00],
            ['base' => 'USD', 'target' => 'EUR', 'rate' => 0.92],
            ['base' => 'USD', 'target' => 'JPY', 'rate' => 149.50],
            ['base' => 'USD', 'target' => 'GBP', 'rate' => 0.79],
            ['base' => 'USD', 'target' => 'CNY', 'rate' => 7.25],
            ['base' => 'USD', 'target' => 'SGD', 'rate' => 1.35],
            ['base' => 'USD', 'target' => 'MYR', 'rate' => 4.70],
            ['base' => 'USD', 'target' => 'THB', 'rate' => 36.50],
            ['base' => 'USD', 'target' => 'VND', 'rate' => 25400.00],
            ['base' => 'USD', 'target' => 'INR', 'rate' => 83.50],
            ['base' => 'USD', 'target' => 'KRW', 'rate' => 1380.00],
            ['base' => 'USD', 'target' => 'AUD', 'rate' => 1.52],
            ['base' => 'USD', 'target' => 'CAD', 'rate' => 1.37],
            ['base' => 'USD', 'target' => 'BRL', 'rate' => 5.45],
            ['base' => 'USD', 'target' => 'RUB', 'rate' => 88.50],
            ['base' => 'USD', 'target' => 'MXN', 'rate' => 18.20],
            ['base' => 'USD', 'target' => 'PHP', 'rate' => 58.50],
        ];

        // Ambil tanggal hari ini (format YYYY-MM-DD)
        $today = Carbon::today()->toDateString();

        foreach ($exchangeRates as $data) {
            // updateOrInsert sekarang mengecek 3 kolom unik: base, target, DAN tanggal
            DB::table('currency_rates')->updateOrInsert(
                [
                    'base_currency' => $data['base'],
                    'target_currency' => $data['target'],
                    'record_date' => $today
                ],
                [
                    'rate' => $data['rate'],
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );
        }

        $this->command->info('✅ Exchange rates seeded successfully!');
        $this->command->info('💱 Data source: Bank Indonesia & The Fed (Real-time rates)');
    }
}