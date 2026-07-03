<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyRate extends Model
{
    protected $fillable = [
        'base_currency', 'target_currency', 'rate', 'record_date'
    ];

    protected $casts = [
        'record_date' => 'date'
    ];
}