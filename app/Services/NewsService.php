<?php

namespace App\Services;

use App\Models\Country;
use App\Models\NewsCache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsService
{
    public function fetchAndSync()
    {
        $apiKey = env('GNEWS_API_KEY');
        if (!$apiKey) {
            Log::error('GNews API Key not found in .env file');
            return 0;
        }

        Log::info('Fetching news from GNews API...');
        $count = 0;
        
        // Keywords yang WAJIB ada sesuai PDF
        $keywords = ['logistics', 'trade', 'shipping', 'economy', 'supply chain'];
        
        // Ambil berita untuk setiap keyword (tanpa filter negara agar lebih banyak)
        foreach ($keywords as $keyword) {
            try {
                $response = Http::timeout(30)->retry(2, 1000)->get('https://gnews.io/api/v4/search', [
                    'q' => $keyword,
                    'lang' => 'en',
                    'max' => 10, // Ambil 10 berita per keyword
                    'apikey' => $apiKey
                ]);

                if ($response->successful()) {
                    $articles = $response->json()['articles'] ?? [];

                    foreach ($articles as $article) {
                        // Cari country_id berdasarkan keyword di judul/deskripsi
                        $countryId = null;
                        $country = Country::where(function($q) use ($article) {
                            $q->where('name', 'like', '%' . $article['title'] . '%')
                              ->orWhere('name', 'like', '%' . ($article['description'] ?? '') . '%');
                        })->first();
                        
                        if ($country) {
                            $countryId = $country->id;
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
                Log::error("Error fetching news for keyword {$keyword}: " . $e->getMessage());
                continue;
            }
        }

        Log::info("Successfully synced {$count} news articles.");
        
        // Analisis sentiment untuk berita baru
        $this->analyzeNewNews();
        
        return $count;
    }
    
    private function analyzeNewNews()
    {
        $news = NewsCache::whereNull('sentiment')->get();
        
        foreach ($news as $item) {
            $text = strtolower($item->title . ' ' . ($item->description ?? ''));
            
            $positiveWords = ['growth', 'increase', 'profit', 'stable', 'improve', 'success', 'gain', 'positive', 'opportunity', 'advance', 'progress', 'recover', 'strong', 'rise', 'expansion', 'benefit', 'efficient', 'partnership', 'agreement', 'deal', 'investment'];
            $negativeWords = ['war', 'crisis', 'inflation', 'delay', 'disaster', 'decrease', 'decline', 'fall', 'loss', 'negative', 'risk', 'threat', 'conflict', 'tension', 'sanction', 'recession', 'collapse', 'crash', 'failure', 'disrupt', 'shortage', 'unstable', 'uncertainty', 'bankrupt', 'debt', 'emergency'];
            
            $positiveScore = 0;
            $negativeScore = 0;
            
            foreach ($positiveWords as $word) {
                if (strpos($text, $word) !== false) $positiveScore++;
            }
            foreach ($negativeWords as $word) {
                if (strpos($text, $word) !== false) $negativeScore++;
            }
            
            $totalWords = $positiveScore + $negativeScore;
            
            if ($totalWords === 0) {
                $sentiment = 'Neutral';
                $score = 0;
            } else {
                $sentimentScore = (($positiveScore - $negativeScore) / $totalWords) * 100;
                
                if ($sentimentScore > 10) {
                    $sentiment = 'Positive';
                    $score = $sentimentScore;
                } elseif ($sentimentScore < -10) {
                    $sentiment = 'Negative';
                    $score = $sentimentScore;
                } else {
                    $sentiment = 'Neutral';
                    $score = 0;
                }
            }
            
            $item->update([
                'sentiment' => $sentiment,
                'sentiment_score' => round($score, 2)
            ]);
        }
    }

    // Fungsi untuk fetch real-time news (tanpa simpan ke database)
    public function getRealTimeNews()
    {
        $apiKey = env('GNEWS_API_KEY');
        if (!$apiKey) {
            return [];
        }

        $keywords = ['logistics', 'trade', 'shipping', 'economy'];
        $allNews = [];

        foreach ($keywords as $keyword) {
            try {
                $response = Http::timeout(30)->get('https://gnews.io/api/v4/search', [
                    'q' => $keyword,
                    'lang' => 'en',
                    'max' => 5,
                    'apikey' => $apiKey
                ]);

                if ($response->successful()) {
                    $articles = $response->json()['articles'] ?? [];
                    
                    foreach ($articles as $article) {
                        $allNews[] = [
                            'title' => $article['title'],
                            'description' => $article['description'] ?? 'No description',
                            'source' => $article['source']['name'] ?? 'Unknown',
                            'published_at' => $article['publishedAt'],
                            'url' => $article['url'],
                            'category' => $keyword,
                            'sentiment' => 'Neutral', // Bisa ditambah analisis real-time
                            'sentiment_score' => 0
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error fetching real-time news: " . $e->getMessage());
            }
        }

        return $allNews;
    }
}