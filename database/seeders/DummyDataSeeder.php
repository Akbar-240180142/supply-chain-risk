<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\WeatherCache;
use App\Models\NewsCache;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('️ Seeding dummy weather data...');
        $this->seedWeatherData();
        
        $this->command->info('📰 Seeding dummy news data...');
        $this->seedNewsData();
        
        $this->command->info('✅ Dummy data seeded successfully!');
    }

    private function seedWeatherData()
    {
        $weatherData = [
            'Indonesia'     => ['temp' => 28, 'rain' => 15, 'wind' => 25, 'storm' => true],
            'United States' => ['temp' => 22, 'rain' => 5,  'wind' => 20, 'storm' => false],
            'China'         => ['temp' => 18, 'rain' => 8,  'wind' => 35, 'storm' => false],
            'Germany'       => ['temp' => 15, 'rain' => 12, 'wind' => 40, 'storm' => false],
            'Japan'         => ['temp' => 20, 'rain' => 18, 'wind' => 55, 'storm' => true],
            'United Kingdom'=> ['temp' => 12, 'rain' => 10, 'wind' => 30, 'storm' => false],
            'Australia'     => ['temp' => 30, 'rain' => 2,  'wind' => 15, 'storm' => false],
            'Singapore'     => ['temp' => 31, 'rain' => 6,  'wind' => 10, 'storm' => false],
            'Malaysia'      => ['temp' => 29, 'rain' => 14, 'wind' => 20, 'storm' => false],
            'Thailand'      => ['temp' => 32, 'rain' => 11, 'wind' => 18, 'storm' => false],
            'Vietnam'       => ['temp' => 27, 'rain' => 20, 'wind' => 60, 'storm' => true],
            'India'         => ['temp' => 35, 'rain' => 25, 'wind' => 45, 'storm' => true],
            'Brazil'        => ['temp' => 26, 'rain' => 7,  'wind' => 22, 'storm' => false],
            'Russia'        => ['temp' => -5, 'rain' => 3,  'wind' => 50, 'storm' => false],
            'France'        => ['temp' => 16, 'rain' => 9,  'wind' => 28, 'storm' => false],
            'Italy'         => ['temp' => 19, 'rain' => 13, 'wind' => 32, 'storm' => false],
            'Canada'        => ['temp' => -2, 'rain' => 4,  'wind' => 42, 'storm' => false],
            'South Korea'   => ['temp' => 17, 'rain' => 10, 'wind' => 30, 'storm' => false],
            'Netherlands'   => ['temp' => 14, 'rain' => 11, 'wind' => 38, 'storm' => false],
            'United Arab Emirates' => ['temp' => 42, 'rain' => 0, 'wind' => 25, 'storm' => false],
        ];

        foreach ($weatherData as $countryName => $data) {
            $country = Country::where('name', $countryName)->first();
            if ($country) {
                WeatherCache::updateOrCreate(
                    ['country_id' => $country->id],
                    [
                        'temperature' => $data['temp'],
                        'rain' => $data['rain'],
                        'wind_speed' => $data['wind'],
                        'is_storm' => $data['storm'],
                        'fetched_at' => now(),
                    ]
                );
            }
        }
    }

    private function seedNewsData()
    {
        $newsData = [
            'Indonesia' => [
                ['title' => 'Indonesia economic growth increases significantly', 'sentiment' => 'Positive', 'score' => 65],
                ['title' => 'Inflation crisis threatens supply chain stability', 'sentiment' => 'Negative', 'score' => -70],
                ['title' => 'New trade agreement signed with ASEAN partners', 'sentiment' => 'Positive', 'score' => 55],
            ],
            'United States' => [
                ['title' => 'US economy shows stable growth and profit increase', 'sentiment' => 'Positive', 'score' => 70],
                ['title' => 'Federal Reserve maintains interest rates', 'sentiment' => 'Neutral', 'score' => 0],
                ['title' => 'Tech sector expansion continues', 'sentiment' => 'Positive', 'score' => 60],
            ],
            'China' => [
                ['title' => 'Trade war tensions increase between major economies', 'sentiment' => 'Negative', 'score' => -80],
                ['title' => 'Manufacturing output decline reported', 'sentiment' => 'Negative', 'score' => -65],
                ['title' => 'Supply chain disruption causes delays', 'sentiment' => 'Negative', 'score' => -75],
            ],
            'Germany' => [
                ['title' => 'German industrial production shows improvement', 'sentiment' => 'Positive', 'score' => 50],
                ['title' => 'Energy crisis threatens manufacturing sector', 'sentiment' => 'Negative', 'score' => -60],
                ['title' => 'Export growth reaches new high', 'sentiment' => 'Positive', 'score' => 65],
            ],
            'Japan' => [
                ['title' => 'Natural disaster disrupts shipping routes', 'sentiment' => 'Negative', 'score' => -85],
                ['title' => 'Recovery efforts show progress', 'sentiment' => 'Positive', 'score' => 45],
                ['title' => 'Yen weakens against dollar', 'sentiment' => 'Negative', 'score' => -50],
            ],
            'Vietnam' => [
                ['title' => 'Severe flooding causes major disruption', 'sentiment' => 'Negative', 'score' => -90],
                ['title' => 'Infrastructure damage threatens logistics', 'sentiment' => 'Negative', 'score' => -75],
                ['title' => 'Emergency response mobilized', 'sentiment' => 'Neutral', 'score' => -10],
            ],
            'India' => [
                ['title' => 'Monsoon disaster affects trade routes', 'sentiment' => 'Negative', 'score' => -80],
                ['title' => 'Economic growth remains strong despite challenges', 'sentiment' => 'Positive', 'score' => 55],
                ['title' => 'New investment opportunities emerge', 'sentiment' => 'Positive', 'score' => 60],
            ],
            'Russia' => [
                ['title' => 'Sanctions increase trade uncertainty', 'sentiment' => 'Negative', 'score' => -85],
                ['title' => 'Conflict threatens global supply chains', 'sentiment' => 'Negative', 'score' => -90],
                ['title' => 'Energy exports face new restrictions', 'sentiment' => 'Negative', 'score' => -70],
            ],
        ];

        foreach ($newsData as $countryName => $articles) {
            $country = Country::where('name', $countryName)->first();
            if ($country) {
                foreach ($articles as $index => $article) {
                    NewsCache::updateOrCreate(
                        [
                            'country_id' => $country->id,
                            'title' => $article['title'],
                        ],
                        [
                            'description' => 'Dummy news article for demonstration purposes.',
                            'url' => "https://example.com/news/{$country->id}-{$index}",
                            'source' => 'Dummy News Source',
                            'published_at' => now()->subDays(rand(1, 30)),
                            'sentiment' => $article['sentiment'],
                            'sentiment_score' => $article['score'],
                        ]
                    );
                }
            }
        }
    }
}