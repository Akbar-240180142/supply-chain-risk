<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RiskScoringService;
use App\Services\SentimentAnalysisService;

class CalculateRisk extends Command
{
    protected $signature = 'risk:calculate';
    protected $description = 'Calculate risk scores for all countries';

    public function handle()
    {
        $this->info(' Starting Risk Calculation...');
        $this->newLine();

        // Step 1: Sentiment Analysis
        $this->info('1. Running Sentiment Analysis on News...');
        $sentimentCount = app(SentimentAnalysisService::class)->analyzeAllNews();
        $this->info("   ✅ Analyzed {$sentimentCount} news articles.");
        $this->newLine();

        // Step 2: Risk Scoring
        $this->info('2. Calculating Risk Scores for All Countries...');
        $riskResults = app(RiskScoringService::class)->calculateAllCountries();
        $this->info("   ✅ Calculated risk for " . count($riskResults) . " countries.");
        $this->newLine();

        // Step 3: Display Results
        $this->info('📊 Risk Score Results:');
        $this->table(
            ['Country', 'Total Risk', 'Risk Level'],
            collect($riskResults)->map(function ($result) {
                return [
                    $result['country'],
                    $result['total_risk'],
                    $result['risk_level']
                ];
            })->toArray()
        );

        $this->newLine();
        $this->info('🎉 Risk Calculation Completed!');
        
        return Command::SUCCESS;
    }
}