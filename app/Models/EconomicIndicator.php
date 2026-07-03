<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EconomicIndicator extends Model
{
    protected $fillable = [
        'country_id', 'year', 'gdp', 'inflation_rate', 
        'population', 'exports', 'imports'
    ];

    public function country() { return $this->belongsTo(Country::class); }
}