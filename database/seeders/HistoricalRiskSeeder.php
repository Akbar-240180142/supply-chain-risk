<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\RiskScore;

class HistoricalRiskSeeder extends Seeder
{
    public function run()
    {
        $countries = Country::all();
        
        foreach ($countries as $country) {
            // Generate 6 bulan data historis
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                
                // Generate random variation dari risk score
                $baseScore = $country->riskScores()->latest()->first();
                $currentScore = $baseScore ? $baseScore->total_risk_score : rand(10, 70);
                $variation = rand(-10, 10);
                $score = max(0, min(100, $currentScore + $variation));
                
                // Tentukan risk level
                if ($score < 25) $level = 'Low';
                elseif ($score < 50) $level = 'Medium';
                elseif ($score < 75) $level = 'High';
                else $level = 'Critical';
                
                RiskScore::updateOrCreate(
                    [
                        'country_id' => $country->id,
                        'record_date' => $date->format('Y-m-d')
                    ],
                    [
                        'weather_risk' => rand(0, 100),
                        'inflation_risk' => rand(0, 100),
                        'currency_risk' => rand(0, 100),
                        'news_risk' => rand(0, 100),
                        'total_risk_score' => round($score, 2),
                        'risk_level' => $level
                    ]
                );
            }
        }
        
        $this->command->info('✅ Historical risk data generated for ' . $countries->count() . ' countries!');
    }
}