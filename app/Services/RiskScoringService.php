<?php

namespace App\Services;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\WeatherCache;
use App\Models\EconomicIndicator;
use App\Models\NewsCache;
use App\Models\CurrencyRate;

class RiskScoringService
{
    public function calculateRiskForCountry($countryId)
    {
        $country = Country::findOrFail($countryId);
        
        $weatherRisk = $this->calculateWeatherRisk($country);
        $inflationRisk = $this->calculateInflationRisk($country);
        $newsRisk = $this->calculateNewsRisk($country);
        $currencyRisk = $this->calculateCurrencyRisk($country);

        $totalRisk = ($weatherRisk * 0.30) + 
                     ($inflationRisk * 0.20) + 
                     ($newsRisk * 0.40) + 
                     ($currencyRisk * 0.10);

        if ($totalRisk < 25) {
            $riskLevel = 'Low';
        } elseif ($totalRisk < 50) {
            $riskLevel = 'Medium';
        } elseif ($totalRisk < 75) {
            $riskLevel = 'High';
        } else {
            $riskLevel = 'Critical';
        }

        RiskScore::updateOrCreate(
            ['country_id' => $countryId, 'record_date' => today()],
            [
                'weather_risk' => round($weatherRisk, 2),
                'inflation_risk' => round($inflationRisk, 2),
                'currency_risk' => round($currencyRisk, 2),
                'news_risk' => round($newsRisk, 2),
                'total_risk_score' => round($totalRisk, 2),
                'risk_level' => $riskLevel
            ]
        );

        return [
            'country' => $country->name,
            'total_risk' => round($totalRisk, 2),
            'risk_level' => $riskLevel
        ];
    }

    private function calculateWeatherRisk($country)
    {
        $weather = WeatherCache::where('country_id', $country->id)->first();
        
        if (!$weather) return 0;

        $risk = 0;
        if ($weather->is_storm) $risk += 50;
        if ($weather->rain > 10) $risk += 20;
        elseif ($weather->rain > 5) $risk += 10;
        if ($weather->wind_speed > 50) $risk += 30;
        elseif ($weather->wind_speed > 30) $risk += 15;

        return min($risk, 100);
    }

    private function calculateInflationRisk($country)
    {
        $economic = EconomicIndicator::where('country_id', $country->id)
            ->latest('year')
            ->first();
        
        if (!$economic || $economic->inflation_rate === null) return 0;

        $inflation = $economic->inflation_rate;

        if ($inflation > 10) return 100;
        elseif ($inflation > 5) return 75;
        elseif ($inflation > 3) return 50;
        elseif ($inflation > 0) return 25;
        else return 10;
    }

    private function calculateNewsRisk($country)
    {
        $news = NewsCache::where('country_id', $country->id)
            ->whereNotNull('sentiment')
            ->get();

        if ($news->isEmpty()) return 0;

        $negativeCount = $news->where('sentiment', 'Negative')->count();
        return ($negativeCount / $news->count()) * 100;
    }

    private function calculateCurrencyRisk($country)
    {
        if (!$country->currency_code) return 0;

        $rate = CurrencyRate::where('base_currency', 'USD')
            ->where('target_currency', $country->currency_code)
            ->first();

        if (!$rate) return 0;

        if ($rate->rate > 10000) return 60;
        elseif ($rate->rate > 1000) return 40;
        elseif ($rate->rate > 100) return 20;
        else return 10;
    }

    public function calculateAllCountries()
    {
        $countries = Country::all();
        $results = [];

        foreach ($countries as $country) {
            $results[] = $this->calculateRiskForCountry($country->id);
        }

        return $results;
    }
}