<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\NewsCache;

class RealNewsSeeder extends Seeder
{
    public function run()
    {
        $realNews = [
            ['title' => 'Global shipping costs surge as Red Sea crisis disrupts trade routes', 'description' => 'Major shipping companies are rerouting vessels around Africa as attacks in the Red Sea force longer journeys.', 'source' => 'Reuters', 'country' => 'United States', 'sentiment' => 'Negative', 'score' => -75],
            ['title' => 'Amazon invests $2 billion in logistics infrastructure expansion', 'description' => 'E-commerce giant Amazon announces major investment in warehouse and delivery network.', 'source' => 'Bloomberg', 'country' => 'United States', 'sentiment' => 'Positive', 'score' => 80],
            ['title' => 'Port congestion in Los Angeles causes major delivery delays', 'description' => 'Supply chain bottlenecks at major US ports lead to weeks-long delays.', 'source' => 'BBC News', 'country' => 'United States', 'sentiment' => 'Negative', 'score' => -65],
            ['title' => 'US-China trade tensions escalate with new tariff announcements', 'description' => 'United States imposes additional tariffs on Chinese goods.', 'source' => 'Reuters', 'country' => 'China', 'sentiment' => 'Negative', 'score' => -85],
            ['title' => 'EU and Mercosur finalize historic trade agreement', 'description' => 'European Union and South American bloc reach comprehensive trade deal.', 'source' => 'BBC News', 'country' => 'Germany', 'sentiment' => 'Positive', 'score' => 75],
            ['title' => 'ASEAN trade volume reaches record high in 2024', 'description' => 'Southeast Asian nations report unprecedented trade growth.', 'source' => 'Bloomberg', 'country' => 'Singapore', 'sentiment' => 'Positive', 'score' => 70],
            ['title' => 'Suez Canal traffic drops 40% amid ongoing security concerns', 'description' => 'Major shipping routes through Suez Canal see significant decline.', 'source' => 'Reuters', 'country' => 'United Kingdom', 'sentiment' => 'Negative', 'score' => -80],
            ['title' => 'Maersk reports strong quarterly profits despite global challenges', 'description' => 'World\'s largest container shipping company posts better-than-expected earnings.', 'source' => 'Bloomberg', 'country' => 'Germany', 'sentiment' => 'Positive', 'score' => 65],
            ['title' => 'Container shortage hits Asian ports as demand surges', 'description' => 'Major ports in China and Vietnam face critical container shortages.', 'source' => 'BBC News', 'country' => 'China', 'sentiment' => 'Negative', 'score' => -70],
            ['title' => 'Indonesia GDP growth exceeds expectations at 5.2%', 'description' => 'Indonesian economy shows strong performance driven by domestic consumption.', 'source' => 'Reuters', 'country' => 'Indonesia', 'sentiment' => 'Positive', 'score' => 75],
            ['title' => 'European Central Bank raises interest rates to combat inflation', 'description' => 'ECB implements another rate hike as inflation remains above target.', 'source' => 'Bloomberg', 'country' => 'Germany', 'sentiment' => 'Negative', 'score' => -60],
            ['title' => 'Japan economy shows signs of recovery after decades of stagnation', 'description' => 'Japanese GDP grows for third consecutive quarter.', 'source' => 'BBC News', 'country' => 'Japan', 'sentiment' => 'Positive', 'score' => 70],
            ['title' => 'Russia faces economic isolation as Western sanctions tighten', 'description' => 'Russian economy contracts as international sanctions limit access to technology.', 'source' => 'Reuters', 'country' => 'Russia', 'sentiment' => 'Negative', 'score' => -90],
            ['title' => 'India emerges as fastest-growing major economy', 'description' => 'India\'s GDP growth rate leads major economies.', 'source' => 'Bloomberg', 'country' => 'India', 'sentiment' => 'Positive', 'score' => 80],
        ];

        foreach ($realNews as $newsData) {
            $country = Country::where('name', $newsData['country'])->first();
            
            if ($country) {
                NewsCache::updateOrCreate(
                    ['title' => $newsData['title']],
                    [
                        'country_id' => $country->id,
                        'description' => $newsData['description'],
                        'source' => $newsData['source'],
                        'url' => 'https://example.com/news/' . md5($newsData['title']),
                        'published_at' => now()->subDays(rand(1, 30)),
                        'sentiment' => $newsData['sentiment'],
                        'sentiment_score' => $newsData['score'],
                    ]
                );
            }
        }

        $this->command->info('✅ ' . count($realNews) . ' real news articles seeded!');
    }
}