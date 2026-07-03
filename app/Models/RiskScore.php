<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskScore extends Model
{
    protected $fillable = [
        'country_id', 'record_date', 'weather_risk', 'inflation_risk', 
        'currency_risk', 'news_risk', 'total_risk_score', 'risk_level'
    ];

    public function country() { return $this->belongsTo(Country::class); }
}