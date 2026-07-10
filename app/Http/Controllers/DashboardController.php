<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\CurrencyRate;
use App\Models\Port;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function getDashboardData()
    {
        $countries = Country::with(['riskScores' => function($q) {
                $q->latest('record_date')->limit(1);
            }, 'economicIndicators' => function($q) {
                $q->latest('year')->limit(1);
            }])->get();

        $countryNames = $countries->pluck('name');
        $riskScores = $countries->map(fn($c) => $c->riskScores->first()?->total_risk_score ?? 0);
        $inflationRates = $countries->map(fn($c) => $c->economicIndicators->first()?->inflation_rate ?? 0);

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

        $currentExchangeRate = DB::table('currency_rates')
            ->where('base_currency', 'USD')
            ->where('target_currency', $country->currency_code)
            ->latest('record_date')
            ->value('rate');

        return view('country-detail', compact('country', 'currentExchangeRate'));
    }

    public function comparison()
    {
        $countries = Country::all();
        return view('comparison', compact('countries'));
    }

    public function compareCountries(Request $request)
    {
        try {
            $country1Id = $request->input('country1');
            $country2Id = $request->input('country2');
            
            $country1 = Country::with([
                'riskScores' => function($q) { $q->latest('record_date')->limit(1); },
                'economicIndicators' => function($q) { $q->latest('year')->limit(1); }
            ])->findOrFail($country1Id);
            
            $country2 = Country::with([
                'riskScores' => function($q) { $q->latest('record_date')->limit(1); },
                'economicIndicators' => function($q) { $q->latest('year')->limit(1); }
            ])->findOrFail($country2Id);
            
            $risk1 = $country1->riskScores->first();
            $risk2 = $country2->riskScores->first();
            $econ1 = $country1->economicIndicators->first();
            $econ2 = $country2->economicIndicators->first();
            
            $rate1 = DB::table('currency_rates')
                ->where('base_currency', 'USD')
                ->where('target_currency', $country1->currency_code)
                ->latest('record_date')
                ->value('rate');
            
            $rate2 = DB::table('currency_rates')
                ->where('base_currency', 'USD')
                ->where('target_currency', $country2->currency_code)
                ->latest('record_date')
                ->value('rate');
            
            return response()->json([
                'country1' => [
                    'name' => $country1->name,
                    'risk_score' => floatval($risk1->total_risk_score ?? 0),
                    'risk_level' => $risk1->risk_level ?? 'Low',
                    'gdp' => floatval($econ1->gdp ?? 0),
                    'inflation' => floatval($econ1->inflation_rate ?? 0),
                    'population' => intval($econ1->population ?? 0),
                    'currency' => $country1->currency_code ?? 'USD',
                    'exchange_rate' => floatval($rate1 ?? 0),
                ],
                'country2' => [
                    'name' => $country2->name,
                    'risk_score' => floatval($risk2->total_risk_score ?? 0),
                    'risk_level' => $risk2->risk_level ?? 'Low',
                    'gdp' => floatval($econ2->gdp ?? 0),
                    'inflation' => floatval($econ2->inflation_rate ?? 0),
                    'population' => intval($econ2->population ?? 0),
                    'currency' => $country2->currency_code ?? 'USD',
                    'exchange_rate' => floatval($rate2 ?? 0),
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Compare Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to compare countries',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function ports()
    {
        $countries = Country::all();
        return view('ports', compact('countries'));
    }

    public function getPortsData()
    {
        try {
            $ports = DB::table('ports')->get();
            
            $portsData = $ports->map(function($port) {
                $portName = $port->port_name ?? $port->name ?? 'Unknown Port';
                $portCode = $port->code;
                if (empty($portCode)) {
                    $words = explode(' ', $portName);
                    $portCode = strtoupper(substr(implode('', array_map(function($w) { return substr($w, 0, 1); }, $words)), 0, 3));
                }
                $countryName = $port->country_name ?? 'Unknown';
                $status = 'Active';
                if (isset($port->is_active) && !$port->is_active) {
                    $status = 'Inactive';
                } elseif (isset($port->status)) {
                    $status = $port->status;
                }
                
                return [
                    'id' => $port->id,
                    'name' => $portName,
                    'code' => $portCode ?? 'N/A',
                    'country' => $countryName,
                    'country_id' => $port->country_id,
                    'latitude' => (float) $port->latitude,
                    'longitude' => (float) $port->longitude,
                    'status' => $status
                ];
            });
            
            return response()->json($portsData);
            
        } catch (\Exception $e) {
            \Log::error('Ports API Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to load ports',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ============ REST API ENDPOINTS ============
    
    public function apiCountries()
    {
        $countries = Country::select('id', 'name', 'capital', 'region', 'currency_code', 'latitude', 'longitude')->get();
        return response()->json($countries);
    }

    public function apiRisk()
    {
        $risk = RiskScore::with('country:id,name')->latest('record_date')->get();
        return response()->json($risk);
    }

    public function apiCurrency()
    {
        $currency = DB::table('currency_rates')
            ->select('base_currency', 'target_currency', 'rate', 'record_date')
            ->latest('record_date')
            ->get();
        return response()->json($currency);
    }

    public function getEconomicTrends()
    {
        $trends = DB::table('economic_indicators')
            ->join('countries', 'economic_indicators.country_id', '=', 'countries.id')
            ->select(
                'countries.name as country_name',
                'economic_indicators.year',
                'economic_indicators.gdp',
                'economic_indicators.inflation_rate'
            )
            ->where('economic_indicators.year', '>=', date('Y') - 5)
            ->orderBy('countries.name')
            ->orderBy('economic_indicators.year')
            ->get();

        return response()->json($trends);
    }
}