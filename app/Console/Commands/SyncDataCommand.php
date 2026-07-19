<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CountryService;
use App\Services\PortService;
use App\Services\WeatherService;
use App\Services\NewsService;
use App\Services\CurrencyService;
use App\Services\EconomicService;
use App\Services\RiskScoringService;
use App\Services\SentimentAnalysisService;
use Illuminate\Support\Facades\Cache;

class SyncDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and sync all data (Countries, Ports, Weather, News, Economics) from external APIs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting full data sync...');

        $this->info('1. Syncing Countries...');
        app(CountryService::class)->fetchAndSync();

        $this->info('2. Syncing Ports...');
        app(PortService::class)->fetchAndSync();

        $this->info('3. Syncing Economic Data...');
        app(EconomicService::class)->fetchAndSync();

        $this->info('4. Syncing Currency Rates...');
        app(CurrencyService::class)->fetchAndSync();

        $this->info('5. Syncing Weather...');
        app(WeatherService::class)->fetchAndSync();

        $this->info('6. Syncing News...');
        app(NewsService::class)->fetchAndSync();

        $this->info('7. Analyzing News Sentiment...');
        $sentimentCount = app(SentimentAnalysisService::class)->analyzeAllNews();
        $this->info("   Analyzed {$sentimentCount} news articles.");

        $this->info('8. Recalculating Risk Scores...');
        app(RiskScoringService::class)->calculateAllCountries();

        // Clear cache so dashboard uses fresh data
        Cache::forget('api_dashboard_data');
        Cache::flush();

        $this->info('✅ Full data sync completed successfully!');
    }
}
