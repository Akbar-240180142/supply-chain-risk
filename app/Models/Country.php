<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name', 'cca2', 'cca3', 'capital', 'region', 
        'currency_code', 'latitude', 'longitude'
    ];

    public function ports() { return $this->hasMany(Port::class); }
    public function economicIndicators() { return $this->hasMany(EconomicIndicator::class); }
    public function riskScores() { return $this->hasMany(RiskScore::class); }
    public function news() { return $this->hasMany(NewsCache::class); }
    public function weather() { return $this->hasMany(WeatherCache::class); }
    public function watchlists() { return $this->hasMany(Watchlist::class); }
}