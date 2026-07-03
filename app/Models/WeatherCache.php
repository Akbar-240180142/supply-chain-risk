<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherCache extends Model
{
    protected $table = 'weather_cache'; 
    
    protected $fillable = [
        'country_id', 'temperature', 'rain', 'wind_speed', 
        'is_storm', 'fetched_at'
    ];

    protected $casts = [
        'fetched_at' => 'datetime',
        'is_storm' => 'boolean'
    ];

    public function country() { return $this->belongsTo(Country::class); }
}