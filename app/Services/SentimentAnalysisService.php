<?php

namespace App\Services;

use App\Models\NewsCache;
use App\Models\PositiveWord;
use App\Models\NegativeWord;

class SentimentAnalysisService
{
    protected $positiveWords = [];
    protected $negativeWords = [];

    public function __construct()
    {
        $this->positiveWords = PositiveWord::pluck('word')->toArray();
        $this->negativeWords = NegativeWord::pluck('word')->toArray();
    }

    public function analyze($text)
    {
        $text = strtolower($text);
        $words = str_word_count($text, 1);
        
        $positiveScore = 0;
        $negativeScore = 0;

        foreach ($words as $word) {
            if (in_array($word, $this->positiveWords)) {
                $positiveScore++;
            }
            if (in_array($word, $this->negativeWords)) {
                $negativeScore++;
            }
        }

        $totalWords = $positiveScore + $negativeScore;
        
        if ($totalWords === 0) {
            return [
                'sentiment' => 'Neutral',
                'score' => 0,
            ];
        }

        $sentimentScore = (($positiveScore - $negativeScore) / $totalWords) * 100;
        
        if ($sentimentScore > 10) {
            $sentiment = 'Positive';
        } elseif ($sentimentScore < -10) {
            $sentiment = 'Negative';
        } else {
            $sentiment = 'Neutral';
        }

        return [
            'sentiment' => $sentiment,
            'score' => round($sentimentScore, 2),
        ];
    }

    public function analyzeAllNews()
    {
        $news = NewsCache::whereNull('sentiment')->get();
        $count = 0;

        foreach ($news as $item) {
            $text = $item->title . ' ' . ($item->description ?? '');
            $result = $this->analyze($text);

            $item->update([
                'sentiment' => $result['sentiment'],
                'sentiment_score' => $result['score']
            ]);
            $count++;
        }

        return $count;
    }
}