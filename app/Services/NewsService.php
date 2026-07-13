<?php

namespace App\Services;

use App\Models\Country;
use App\Models\NewsCache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsService
{
    /**
     * Fetch general keywords news from GNews API and synchronize.
     */
    public function fetchAndSync()
    {
        $apiKey = env('GNEWS_API_KEY');
        if (!$apiKey) {
            Log::error('NewsService: GNews API Key not found in .env file');
            return 0;
        }

        Log::info('NewsService: Fetching general news from GNews API...');
        $count = 0;
        
        // Keywords required by PDF
        $keywords = ['logistics', 'trade', 'shipping', 'economy', 'supply chain'];
        
        $countries = Country::all();
        
        foreach ($keywords as $keyword) {
            try {
                $response = Http::timeout(30)->retry(2, 1000)->get('https://gnews.io/api/v4/search', [
                    'q' => $keyword,
                    'lang' => 'en',
                    'max' => 10,
                    'apikey' => $apiKey
                ]);

                if ($response->successful()) {
                    $articles = $response->json()['articles'] ?? [];

                    foreach ($articles as $article) {
                        // Check if title or description mentions any of our tracked countries
                        $countryId = null;
                        $titleLower = strtolower($article['title']);
                        $descLower = strtolower($article['description'] ?? '');

                        foreach ($countries as $c) {
                            $cNameLower = strtolower($c->name);
                            if (str_contains($titleLower, $cNameLower) || str_contains($descLower, $cNameLower)) {
                                $countryId = $c->id;
                                break;
                            }
                        }

                        NewsCache::updateOrCreate(
                            ['url' => $article['url']],
                            [
                                'country_id' => $countryId,
                                'title' => $article['title'],
                                'description' => $article['description'] ?? '',
                                'source' => $article['source']['name'] ?? 'Unknown',
                                'published_at' => $article['publishedAt'],
                                'sentiment' => null,
                                'sentiment_score' => null,
                            ]
                        );
                        $count++;
                    }
                }
            } catch (\Exception $e) {
                Log::error("NewsService: Error fetching news for keyword {$keyword}: " . $e->getMessage());
                continue;
            }
        }

        Log::info("NewsService: Successfully synced {$count} general news articles.");
        
        // Run database-driven sentiment analysis
        app(SentimentAnalysisService::class)->analyzeAllNews();
        
        return $count;
    }

    /**
     * Fetch country-specific news on-demand.
     */
    public function fetchForCountry(Country $country)
    {
        $apiKey = env('GNEWS_API_KEY');
        if (!$apiKey) {
            Log::error('NewsService: GNews API Key not found in .env file');
            return false;
        }

        try {
            Log::info("NewsService: Fetching live targeted news for {$country->name}...");
            $query = '"' . $country->name . '" AND (logistics OR trade OR shipping OR economy OR "supply chain")';
            
            $response = Http::timeout(30)->retry(2, 1000)->get('https://gnews.io/api/v4/search', [
                'q' => $query,
                'lang' => 'en',
                'max' => 5,
                'apikey' => $apiKey
            ]);

            if ($response->successful()) {
                $articles = $response->json()['articles'] ?? [];
                $count = 0;

                foreach ($articles as $article) {
                    NewsCache::updateOrCreate(
                        ['url' => $article['url']],
                        [
                            'country_id' => $country->id,
                            'title' => $article['title'],
                            'description' => $article['description'] ?? '',
                            'source' => $article['source']['name'] ?? 'Unknown',
                            'published_at' => $article['publishedAt'],
                            'sentiment' => null,
                            'sentiment_score' => null,
                        ]
                    );
                    $count++;
                }

                Log::info("NewsService: Successfully synced {$count} targeted news articles for {$country->name}.");
                
                // Run central database-driven sentiment analysis
                app(SentimentAnalysisService::class)->analyzeAllNews();
                
                return true;
            }
        } catch (\Exception $e) {
            Log::error("NewsService: Error fetching news for {$country->name}: " . $e->getMessage());
        }
        return false;
    }
}