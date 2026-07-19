<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\CurrencyRate;
use App\Models\Port;
use App\Services\CurrencyService;
use App\Services\EconomicService;
use App\Services\WeatherService;
use App\Services\NewsService;
use App\Services\RiskScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function getDashboardData()
    {
        // Cache the dashboard data for 10 minutes to prevent slow loading
        $data = Cache::remember('api_dashboard_data', 600, function () {
            // Auto-refresh ONLY when DB is completely empty (first-time setup)
            // For regular updates, use: php artisan app:sync-data

            // 1. Auto-refresh currency if no data at all
            try {
                if (DB::table('currency_rates')->count() === 0) {
                    Log::info('DashboardController: No currency data. Fetching...');
                    app(CurrencyService::class)->fetchAndSync();
                }
            } catch (\Exception $e) {
                Log::warning('DashboardController: Currency auto-refresh failed: ' . $e->getMessage());
            }

            // 2. Auto-refresh weather if no data at all
            try {
                if (DB::table('weather_cache')->count() === 0) {
                    Log::info('DashboardController: No weather data. Fetching...');
                    app(WeatherService::class)->fetchAndSync();
                }
            } catch (\Exception $e) {
                Log::warning('DashboardController: Weather auto-refresh failed: ' . $e->getMessage());
            }

            // 3. Auto-refresh economic data if no data at all
            try {
                if (DB::table('economic_indicators')->count() === 0) {
                    Log::info('DashboardController: No economic data. Fetching...');
                    app(EconomicService::class)->fetchAndSync();
                }
            } catch (\Exception $e) {
                Log::warning('DashboardController: Economic auto-refresh failed: ' . $e->getMessage());
            }

            // 4. Recalculate risk scores if missing for today
            try {
                $todayRiskCount = DB::table('risk_scores')->where('record_date', today())->count();
                $countryCount = Country::count();
                if ($todayRiskCount < $countryCount) {
                    Log::info('DashboardController: Recalculating risk scores...');
                    app(RiskScoringService::class)->calculateAllCountries();
                }
            } catch (\Exception $e) {
                Log::warning('DashboardController: Risk calculation failed: ' . $e->getMessage());
            }

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
                    'lat'  => $c->latitude,
                    'lng'  => $c->longitude,
                    'risk' => $risk ? $risk->total_risk_score : 0,
                    'level'=> $risk ? $risk->risk_level : 'Low'
                ];
            });

            return [
                'countries'       => $countries,
                'chart_labels'    => $countryNames,
                'chart_risk'      => $riskScores,
                'chart_inflation' => $inflationRates,
                'map_data'        => $mapData
            ];
        });

        return response()->json($data);
    }

    public function showCountry($id)
    {
        $country = Country::findOrFail($id);

        // Fetch on-demand ONLY if data is completely missing for this country
        // 1. Economic indicators
        $econCount = DB::table('economic_indicators')->where('country_id', $country->id)->count();
        if ($econCount === 0) {
            try {
                app(EconomicService::class)->fetchForCountry($country);
            } catch (\Exception $e) {
                Log::warning("DashboardController: Economic fetch failed for {$country->name}: " . $e->getMessage());
            }
        }

        // 2. Weather data
        $weatherExists = DB::table('weather_cache')->where('country_id', $country->id)->exists();
        if (!$weatherExists) {
            try {
                app(WeatherService::class)->fetchForCountry($country);
            } catch (\Exception $e) {
                Log::warning("DashboardController: Weather fetch failed for {$country->name}: " . $e->getMessage());
            }
        }

        // 3. Recalculate risk score (fast, reads from DB only)
        try {
            app(RiskScoringService::class)->calculateRiskForCountry($country->id);
        } catch (\Exception $e) {
            Log::warning("DashboardController: Risk calculation failed for {$country->name}: " . $e->getMessage());
        }

        // Load relations fresh
        $country->load([
            'riskScores' => function($q) {
                $q->orderBy('record_date', 'asc');
            },
            'economicIndicators' => function($q) {
                $q->latest('year')->limit(10);
            },
            'news' => function($q) {
                $q->where(function($q2) {
                    $q2->where('url', 'not like', '%example.com%')
                       ->orWhereNull('url');
                })->latest('published_at')->limit(10);
            }
        ]);

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
                'riskScores'         => function($q) { $q->latest('record_date')->limit(1); },
                'economicIndicators' => function($q) { $q->latest('year')->limit(1); }
            ])->findOrFail($country1Id);
            
            $country2 = Country::with([
                'riskScores'         => function($q) { $q->latest('record_date')->limit(1); },
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

            // Get weather from weather_cache
            $w1 = DB::table('weather_cache')->where('country_id', $country1->id)->first();
            $w2 = DB::table('weather_cache')->where('country_id', $country2->id)->first();
            
            $weatherStr1 = $w1 ? round($w1->temperature) . '°C, ' . ($w1->is_storm ? 'Storm ⛈️' : ($w1->rain > 0 ? 'Rain 🌧️' : 'Clear/Cloudy 🌤️')) : 'N/A';
            $weatherStr2 = $w2 ? round($w2->temperature) . '°C, ' . ($w2->is_storm ? 'Storm ⛈️' : ($w2->rain > 0 ? 'Rain 🌧️' : 'Clear/Cloudy 🌤️')) : 'N/A';
            
            return response()->json([
                'country1' => [
                    'name'          => $country1->name,
                    'risk_score'    => floatval($risk1->total_risk_score ?? 0),
                    'risk_level'    => $risk1->risk_level ?? 'Low',
                    'gdp'           => floatval($econ1->gdp ?? 0),
                    'inflation'     => floatval($econ1->inflation_rate ?? 0),
                    'population'    => intval($econ1->population ?? 0),
                    'currency'      => $country1->currency_code ?? 'USD',
                    'exchange_rate' => floatval($rate1 ?? 0),
                    'weather'       => $weatherStr1
                ],
                'country2' => [
                    'name'          => $country2->name,
                    'risk_score'    => floatval($risk2->total_risk_score ?? 0),
                    'risk_level'    => $risk2->risk_level ?? 'Low',
                    'gdp'           => floatval($econ2->gdp ?? 0),
                    'inflation'     => floatval($econ2->inflation_rate ?? 0),
                    'population'    => intval($econ2->population ?? 0),
                    'currency'      => $country2->currency_code ?? 'USD',
                    'exchange_rate' => floatval($rate2 ?? 0),
                    'weather'       => $weatherStr2
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('DashboardController: Compare Error: ' . $e->getMessage());
            return response()->json([
                'error'   => 'Failed to compare countries',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function ports()
    {
        $countries = Country::all();
        return view('ports', compact('countries'));
    }

    public function getPortsData(Request $request)
    {
        try {
            $query = DB::table('ports');

            // Apply database-side filtering
            if ($request->has('country_id') && !empty($request->input('country_id'))) {
                $query->where('country_id', $request->input('country_id'));
            }

            if ($request->has('status') && !empty($request->input('status'))) {
                $status = $request->input('status');
                if ($status === 'Active') {
                    $query->where(function($q) {
                        $q->where('is_active', true)->where('status', 'Active');
                    });
                } elseif ($status === 'Inactive') {
                    $query->where(function($q) {
                        $q->where('is_active', false)->orWhere('status', 'Inactive');
                    });
                } else {
                    $query->where('status', $status);
                }
            }

            if ($request->has('search') && !empty($request->input('search'))) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('port_name', 'like', '%' . $search . '%')
                      ->orWhere('name', 'like', '%' . $search . '%')
                      ->orWhere('code', 'like', '%' . $search . '%');
                });
            }

            // If no filters are applied, default to showing Major/Medium/Small ports or limit to 500 
            // to keep the frontend responsive and avoid browser rendering freeze
            $isFiltered = $request->has('country_id') || $request->has('status') || $request->has('search');
            
            // Build cache key based on filters
            $cacheKey = 'api_ports_data_' . md5(json_encode($request->all()));

            $portsData = Cache::remember($cacheKey, 600, function () use ($query, $isFiltered) {
                if (!$isFiltered) {
                    // Return max 500 ports prioritising bigger ones
                    $query->orderByRaw("CASE 
                        WHEN harbor_size = 'Major' THEN 1 
                        WHEN harbor_size = 'Medium' THEN 2 
                        WHEN harbor_size = 'Small' THEN 3 
                        ELSE 4 END ASC")
                        ->limit(500);
                }

                $ports = $query->get();

                // Auto-fetch from public dataset if ports are empty
                if ($ports->isEmpty() && !$isFiltered) {
                    Log::info('DashboardController: Ports table is empty. Fetching from tayljordan/ports dataset...');
                    try {
                        app(\App\Services\PortService::class)->fetchAndSync();
                        $ports = DB::table('ports')
                            ->orderByRaw("CASE 
                                WHEN harbor_size = 'Major' THEN 1 
                                WHEN harbor_size = 'Medium' THEN 2 
                                WHEN harbor_size = 'Small' THEN 3 
                                ELSE 4 END ASC")
                            ->limit(500)
                            ->get();
                    } catch (\Exception $ex) {
                        Log::error('DashboardController: Lazy ports fetching failed: ' . $ex->getMessage());
                    }
                }
                
                return $ports->map(function($port) {
                    $portName = $port->port_name ?? $port->name ?? 'Unknown Port';
                    $portCode = $port->code;
                    if (empty($portCode)) {
                        $words    = explode(' ', $portName);
                        $portCode = strtoupper(substr(implode('', array_map(function($w) { return substr($w, 0, 1); }, $words)), 0, 3));
                    }
                    $countryName = $port->country_name ?? 'Unknown';
                    $status      = 'Active';
                    if (isset($port->is_active) && !$port->is_active) {
                        $status = 'Inactive';
                    } elseif (isset($port->status)) {
                        $status = $port->status;
                    }
                    
                    return [
                        'id'         => $port->id,
                        'name'       => $portName,
                        'code'       => $portCode ?? 'N/A',
                        'country'    => $countryName,
                        'country_id' => $port->country_id,
                        'latitude'   => (float) $port->latitude,
                        'longitude'  => (float) $port->longitude,
                        'status'     => $status,
                        'size'       => $port->harbor_size ?? 'Medium'
                    ];
                });
            });

            return response()->json($portsData);
            
        } catch (\Exception $e) {
            Log::error('DashboardController: Ports API Error: ' . $e->getMessage());
            return response()->json([
                'error'   => 'Failed to load ports',
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
        // Auto-refresh jika data belum ada hari ini
        $todayCount = DB::table('currency_rates')->where('record_date', today())->count();
        if ($todayCount === 0) {
            try {
                app(CurrencyService::class)->fetchAndSync();
            } catch (\Exception $e) {
                Log::warning('DashboardController: Currency sync in apiCurrency failed: ' . $e->getMessage());
            }
        }

        $currency = DB::table('currency_rates')
            ->select('base_currency', 'target_currency', 'rate', 'record_date')
            ->latest('record_date')
            ->get();

        return response()->json($currency);
    }

    public function getEconomicTrends()
    {
        // Auto-refresh jika tidak ada data ekonomi sama sekali
        $econCount = DB::table('economic_indicators')->count();
        if ($econCount === 0) {
            try {
                Log::info('DashboardController: No economic data found. Triggering sync from World Bank API...');
                app(EconomicService::class)->fetchAndSync();
            } catch (\Exception $e) {
                Log::warning('DashboardController: Economic sync failed: ' . $e->getMessage());
            }
        }

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