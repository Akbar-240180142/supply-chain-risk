<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\CurrencyRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    // API Endpoint untuk diambil via AJAX
    public function getDashboardData()
    {
        // Ambil semua negara beserta risk score terbaru dan economic indicator
        $countries = Country::with(['riskScores' => function($q) {
                $q->latest('record_date')->limit(1);
            }, 'economicIndicators' => function($q) {
                $q->latest('year')->limit(1);
            }])->get();

        // Format data untuk Chart.js dan Tabel
        $countryNames = $countries->pluck('name');
        $riskScores = $countries->map(fn($c) => $c->riskScores->first()?->total_risk_score ?? 0);
        $inflationRates = $countries->map(fn($c) => $c->economicIndicators->first()?->inflation_rate ?? 0);

        // Data untuk Peta (Leaflet)
        $mapData = $countries->map(function($c) {
            $risk = $c->riskScores->first();
            return [
                'name' => $c->name,
                'lat' => $c->latitude,
                'lng' => $c->longitude,
                'risk' => $risk ? $risk->total_risk_score : 0,
                'level' => $risk ? $risk->risk_level : 'Low'
            ];
        });

        return response()->json([
            'countries' => $countries,
            'chart_labels' => $countryNames,
            'chart_risk' => $riskScores,
            'chart_inflation' => $inflationRates,
            'map_data' => $mapData
        ]);
    }
public function showCountry($id)
{
    $country = Country::with([
        'riskScores' => function($q) {
            $q->orderBy('record_date', 'asc');
        },
        'economicIndicators' => function($q) {
            $q->latest('year')->limit(10);
        },
        'news' => function($q) {
            $q->latest('published_at')->limit(10);
        }

    ])->findOrFail($id);

        // Ambil exchange rate terbaru dari database (USD to Local Currency)
    $currentExchangeRate = DB::table('currency_rates')
        ->where('base_currency', 'USD')
        ->where('target_currency', $country->currency_code)
        ->latest('record_date') // Ambil yang tanggalnya paling baru
        ->value('rate');

    return view('country-detail', compact('country', 'currentExchangeRate'));
}
}