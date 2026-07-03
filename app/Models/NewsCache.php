<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsCache extends Model
{
    protected $table = 'news_cache';

    protected $fillable = [
        'country_id', 'title', 'description', 'url', 'source', 
        'published_at', 'sentiment', 'sentiment_score'
    ];

    protected $casts = [
        'published_at' => 'datetime'
    ];

    public function country() { return $this->belongsTo(Country::class); }
}