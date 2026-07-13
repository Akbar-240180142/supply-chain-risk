<?php

namespace App\Console\Commands;

use App\Services\NewsService;
use App\Services\PortService;
use Illuminate\Console\Command;
use App\Services\CountryService;
use App\Services\WeatherService;
use App\Services\EconomicService;
use App\Services\CurrencyService;

class SyncExternalData extends Command
{
    protected $signature = 'data:sync';
    protected $description = 'Sync all external API data (Countries, Weather, Economic, Currency, News)';

    public function handle()
    {   
        $this->info('🚀 Starting Global Supply Chain Data Sync...');
        $this->newLine();

        $this->info('1. Syncing Countries (REST Countries API)...');
        $countryCount = app(CountryService::class)->fetchAndSync();
        $this->info("   ✅ Synced {$countryCount} countries.");
        $this->newLine();

        $this->info('2. Syncing Weather (Open-Meteo API)...');
        $weatherCount = app(WeatherService::class)->fetchAndSync();
        $this->info("   ✅ Synced weather for {$weatherCount} countries.");
        $this->newLine();

        $this->info('3. Syncing Economic Data (World Bank API)...');
        $econCount = app(EconomicService::class)->fetchAndSync();
        $this->info("   ✅ Synced economic data for {$econCount} countries.");
        $this->newLine();

        $this->info('4. Syncing Currency Rates (ExchangeRate API)...');
        $currCount = app(CurrencyService::class)->fetchAndSync();
        $this->info("   ✅ Synced {$currCount} currency rates.");
        $this->newLine();

        // News harus dijalankan paling terakhir karena butuh data negara yang sudah ada
        $this->info('5. Syncing News (GNews API)...');
        $newsCount = app(NewsService::class)->fetchAndSync();
        $this->info("   ✅ Synced {$newsCount} news articles.");
        $this->newLine();

        $this->info('6. Syncing Ports (tayljordan/ports GitHub Dataset)...');
        $portCount = app(PortService::class)->fetchAndSync();
        $this->info("   ✅ Synced {$portCount} ports.");
        $this->newLine();

        $this->info('🎉 Data Sync Completed Successfully!');
        return Command::SUCCESS;
    }
}