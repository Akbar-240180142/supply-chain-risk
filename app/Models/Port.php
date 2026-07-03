<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Port extends Model
{
    protected $fillable = [
        'port_name', 'country_id', 'country_name', 
        'latitude', 'longitude', 'harbor_size', 'is_active'
    ];

    public function country() { return $this->belongsTo(Country::class); }
}