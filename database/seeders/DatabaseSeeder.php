<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DictionarySeeder::class,
            CountrySeeder::class,
            PortSeeder::class,
            UserSeeder::class,
            EconomicHistorySeeder::class,
            ExchangeRateSeeder::class,
            HistoricalRiskSeeder::class,
            RealNewsSeeder::class,
            ShipmentSeeder::class,
        ]);
    }
}