<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Global Supply Chain Risk Intelligence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #60a5fa;
            --secondary: #7c3aed;
            --accent: #06b6d4;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --bg-main: #f8fafc;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            --radius-2xl: 1.5rem;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; 
            background: var(--bg-main); 
            color: var(--gray-800); 
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ============ NAVBAR ============ */
        .navbar-modern {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 1rem 2rem;
            box-shadow: var(--shadow-lg);
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
        }

        .navbar-brand-modern {
            font-size: 1.4rem;
            font-weight: 700;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .navbar-brand-modern:hover {
            transform: translateY(-2px);
            color: var(--primary-light) !important;
        }

        .nav-link-modern {
            color: rgba(255, 255, 255, 0.85) !important;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: var(--radius-md);
            transition: all 0.25s ease;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .nav-link-modern:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white !important;
            transform: translateY(-1px);
        }

        .nav-link-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white !important;
            box-shadow: 0 4px 6px rgba(245, 158, 11, 0.3);
        }

        .nav-link-warning:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            transform: translateY(-2px);
        }

        /* ============ CARDS ============ */
        .card-modern {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .card-modern:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .card-header-modern {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(to right, var(--gray-50), white);
        }

        .card-title-modern {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--gray-800);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 0;
        }

        .card-body-modern {
            padding: 1.5rem;
        }

        /* ============ STAT CARDS ============ */
        .stat-card {
            background: white;
            border-radius: var(--radius-xl);
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 1rem;
        }

        .stat-icon-primary { background: rgba(37, 99, 235, 0.1); }
        .stat-icon-success { background: rgba(16, 185, 129, 0.1); }
        .stat-icon-warning { background: rgba(245, 158, 11, 0.1); }
        .stat-icon-danger { background: rgba(239, 68, 68, 0.1); }

        .stat-value {
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--gray-900);
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }

        /* ============ MAPS ============ */
        #map, #weatherMap { 
            height: 500px; 
            width: 100%; 
            border-radius: var(--radius-lg); 
        }

        /* ============ BADGES ============ */
        .badge-modern {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.025em;
        }

        .badge-success-modern { background: rgba(16, 185, 129, 0.1); color: var(--success); }
        .badge-warning-modern { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
        .badge-danger-modern { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
        .badge-info-modern { background: rgba(59, 130, 246, 0.1); color: var(--info); }
        .badge-dark-modern { background: rgba(17, 24, 39, 0.1); color: var(--gray-900); }

        /* ============ TABLES ============ */
        .table-modern {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-modern thead th {
            background: var(--gray-50);
            padding: 0.875rem 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gray-600);
            border-bottom: 2px solid var(--gray-200);
        }

        .table-modern tbody td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid var(--gray-100);
            transition: all 0.15s ease;
            font-size: 0.9rem;
        }

        .table-modern tbody tr:hover {
            background: var(--gray-50);
        }

        .table-modern tbody tr:last-child td {
            border-bottom: none;
        }

        /* ============ BUTTONS ============ */
        .btn-watch {
            transition: all 0.3s ease;
            min-width: 40px;
            height: 40px;
            border-radius: var(--radius-md);
            font-size: 1.1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--warning);
        }

        .btn-watch:hover {
            transform: scale(1.1) rotate(15deg);
        }

        .btn-watch.active {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            color: white !important;
            border-color: transparent !important;
            box-shadow: 0 4px 6px rgba(245, 158, 11, 0.3);
        }

        /* ============ MARKERS ============ */
        .weather-marker {
            transition: transform 0.2s ease;
        }

        .weather-marker:hover {
            transform: scale(1.2);
        }

        .leaflet-popup-content-wrapper {
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            border: none;
        }

        .leaflet-popup-content {
            margin: 1rem 1.25rem;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
        }

        .leaflet-popup-content h6 {
            font-weight: 600;
            color: var(--gray-800);
        }

        /* ============ ANIMATIONS ============ */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease-out;
        }

        .animate-delay-1 { animation-delay: 0.1s; }
        .animate-delay-2 { animation-delay: 0.2s; }
        .animate-delay-3 { animation-delay: 0.3s; }
        .animate-delay-4 { animation-delay: 0.4s; }

        /* ============ SCROLLBAR ============ */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-100);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gray-400);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--gray-500);
        }

        /* ============ COUNTRY LINK ============ */
        .country-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .country-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 768px) {
            .stat-value { font-size: 1.75rem; }
            .navbar-modern { padding: 1rem; }
            .navbar-brand-modern { font-size: 1.1rem; }
            .nav-link-modern { padding: 0.4rem 0.75rem; font-size: 0.8rem; }
        }
    </style>
</head>
<body>

<!-- Modern Navbar -->
<nav class="navbar-modern">
    <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap gap-2">
        <a class="navbar-brand-modern" href="/">
            <span style="font-size: 1.75rem;">🌍</span>
            <span>Supply Chain Risk Intelligence</span>
        </a>
        <div class="d-flex gap-2 flex-wrap">
            <a href="#" class="nav-link-modern" data-bs-toggle="offcanvas" data-bs-target="#trackingOffcanvas">
                <span>📦</span> Track Package
            </a>
            <a href="/comparison" class="nav-link-modern">
                <span>🔀</span> Comparison
            </a>
            <a href="/ports" class="nav-link-modern">
                <span>🚢</span> Ports
            </a>
            <a href="/news" class="nav-link-modern">
                <span>📰</span> News
            </a>
            <a href="/watchlist" class="nav-link-modern nav-link-warning">
                <span>⭐</span> My Watchlist
            </a>
            
            <div class="d-flex align-items-center ms-3 ps-3 border-start border-secondary">
                @auth
                    <span class="text-light me-3">👤 {{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-light" style="border-radius: var(--radius-md);">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="nav-link-modern">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-primary ms-2" style="border-radius: var(--radius-md);">Register</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 py-4">
    <!-- Stats Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card animate-fadeInUp">
                <div class="stat-icon stat-icon-primary">🌍</div>
                <div class="stat-value" id="totalCountries">0</div>
                <div class="stat-label">Countries Monitored</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card animate-fadeInUp animate-delay-1">
                <div class="stat-icon stat-icon-success">📊</div>
                <div class="stat-value" id="avgRiskScore">0</div>
                <div class="stat-label">Average Risk Score</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card animate-fadeInUp animate-delay-2">
                <div class="stat-icon stat-icon-warning">⚠️</div>
                <div class="stat-value" id="highRiskCountries">0</div>
                <div class="stat-label">High Risk Countries</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card animate-fadeInUp animate-delay-3">
                <div class="stat-icon stat-icon-danger">⭐</div>
                <div class="stat-value" id="watchlistCount">0</div>
                <div class="stat-label">In Watchlist</div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Global Risk Map -->
            <div class="card-modern animate-fadeInUp animate-delay-2">
                <div class="card-header-modern">
                    <h5 class="card-title-modern">
                        <span>🗺️</span> Global Risk Map
                    </h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge-modern badge-success-modern">🟢 Low</span>
                        <span class="badge-modern badge-warning-modern">🟡 Medium</span>
                        <span class="badge-modern badge-danger-modern">🔴 High</span>
                    </div>
                </div>
                <div class="card-body-modern">
                    <div id="map"></div>
                </div>
            </div>

            <!-- Weather Map -->
            <div class="card-modern animate-fadeInUp animate-delay-3">
                <div class="card-header-modern">
                    <h5 class="card-title-modern">
                        <span>🌦️</span> Global Weather Monitoring
                    </h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge-modern badge-info-modern">☁️ Normal</span>
                        <span class="badge-modern badge-info-modern">🌧️ Rain</span>
                        <span class="badge-modern badge-warning-modern">⛈️ Storm</span>
                        <span class="badge-modern badge-danger-modern">💨 High Wind</span>
                    </div>
                </div>
                <div class="card-body-modern">
                    <div id="weatherMap"></div>
                </div>
            </div>

            <!-- Weather Table -->
            <div class="card-modern animate-fadeInUp animate-delay-4">
                <div class="card-header-modern">
                    <h5 class="card-title-modern">
                        <span>📊</span> Weather Details
                    </h5>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#weatherModal">
                        See All Details
                    </button>
                </div>
                <div class="card-body-modern">
                    <div class="table-responsive">
                        <table class="table-modern" id="weatherTable">
                            <thead>
                                <tr>
                                    <th>Country</th>
                                    <th>Temperature</th>
                                    <th>Condition</th>
                                    <th>Rainfall</th>
                                    <th>Wind Speed</th>
                                    <th>Risk Level</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="6" class="text-center text-muted">Loading weather data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card-modern animate-fadeInUp animate-delay-4">
                        <div class="card-header-modern">
                            <h5 class="card-title-modern">
                                <span>📊</span> Risk Score Distribution
                            </h5>
                        </div>
                        <div class="card-body-modern">
                            <canvas id="riskChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-modern animate-fadeInUp animate-delay-4">
                        <div class="card-header-modern">
                            <h5 class="card-title-modern">
                                <span>💱</span> Currency Exchange Rates
                            </h5>
                        </div>
                        <div class="card-body-modern">
                            <canvas id="currencyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2: GDP & Inflation Trends (BARU!) -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card-modern animate-fadeInUp animate-delay-4">
                        <div class="card-header-modern">
                            <h5 class="card-title-modern">
                                <span>📈</span> GDP Trend (Top 5 Countries)
                            </h5>
                        </div>
                        <div class="card-body-modern">
                            <canvas id="gdpChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-modern animate-fadeInUp animate-delay-4">
                        <div class="card-header-modern">
                            <h5 class="card-title-modern">
                                <span>📉</span> Inflation Trend (Top 5 Countries)
                            </h5>
                        </div>
                        <div class="card-body-modern">
                            <canvas id="inflationChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card-modern animate-fadeInUp animate-delay-2" style="position: sticky; top: 100px;">
                <div class="card-header-modern">
                    <h5 class="card-title-modern">
                        <span>📋</span> Country Risk List
                    </h5>
                </div>
                <div class="card-body-modern p-0">
                    <div class="table-responsive" style="max-height: 800px; overflow-y: auto;">
                        <table class="table-modern">
                            <thead style="position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th>Country</th>
                                    <th>Score</th>
                                    <th>Level</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="countryTableBody">
                                <tr><td colspan="4" class="text-center text-muted">Loading data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Weather Modal -->
<div class="modal fade" id="weatherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span>📊</span> Full Weather Details by Country</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table-modern" id="weatherModalTable">
                        <thead style="position: sticky; top: 0; z-index: 10; background: var(--gray-50);">
                            <tr>
                                <th>Country</th>
                                <th>Temperature</th>
                                <th>Condition</th>
                                <th>Rainfall</th>
                                <th>Wind Speed</th>
                                <th>Risk Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="6" class="text-center text-muted">Loading weather data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let watchlistIds = [];
let allCountriesData = [];
let weatherMap;
let weatherMarkers = [];

document.addEventListener('DOMContentLoaded', function() {
    // Init risk map
    const map = L.map('map').setView([20, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Init weather map
    weatherMap = L.map('weatherMap').setView([20, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(weatherMap);

    // Load watchlist, dashboard data, and live currency rates from APIs
    Promise.all([
        fetch('/api/watchlist').then(r => r.json()).catch(() => []),
        fetch('/api/dashboard-data').then(r => r.json()).catch(() => ({ countries: [], chart_labels: [], chart_risk: [], map_data: [] })),
        fetch('/api/currency').then(r => r.json()).catch(() => [])
    ])
    .then(([watchlistData, dashboardData, currencyDataList]) => {
        watchlistIds = watchlistData.map(item => item.country_id) || [];
        allCountriesData = dashboardData.countries || [];
        
        // Build rates mapping dynamically from API data
        const liveRates = {};
        if (Array.isArray(currencyDataList)) {
            currencyDataList.forEach(item => {
                liveRates[item.target_currency] = parseFloat(item.rate);
            });
        }
        
        // Update stats dengan validasi
        let totalRisk = 0;
        let highRisk = 0;
        
        if (allCountriesData && allCountriesData.length > 0) {
            allCountriesData.forEach(c => {
                const risk = c.risk_scores && c.risk_scores[0] ? c.risk_scores[0] : { total_risk_score: 0, risk_level: 'Low' };
                const score = parseFloat(risk.total_risk_score) || 0;
                totalRisk += score;
                
                if (risk.risk_level === 'High' || risk.risk_level === 'Critical') {
                    highRisk++;
                }
            });
            
            const avgRisk = totalRisk / allCountriesData.length;
            document.getElementById('avgRiskScore').textContent = isNaN(avgRisk) ? '0' : avgRisk.toFixed(1);
        } else {
            document.getElementById('avgRiskScore').textContent = '0';
        }
        
        document.getElementById('totalCountries').textContent = allCountriesData ? allCountriesData.length : 0;
        document.getElementById('highRiskCountries').textContent = highRisk;
        document.getElementById('watchlistCount').textContent = watchlistIds ? watchlistIds.length : 0;
        
        updateTable(allCountriesData);
        initChart(dashboardData.chart_labels || [], dashboardData.chart_risk || []);
        initMapMarkers(dashboardData.map_data || []);
        initCurrencyChart(allCountriesData, liveRates);
        
        // Load weather data
        loadWeatherData(dashboardData.map_data || []);
        
        // Load Economic Trends (GDP & Inflation)
        loadEconomicTrends();
    })
    .catch(error => {
        console.error('Error loading data:', error);
        document.getElementById('avgRiskScore').textContent = '0';
        document.getElementById('totalCountries').textContent = '0';
        document.getElementById('highRiskCountries').textContent = '0';
        document.getElementById('watchlistCount').textContent = '0';
    });

    // Fungsi Economic Trends (GDP & Inflation)
    function loadEconomicTrends() {
        fetch('/api/economic-trends')
            .then(r => r.json())
            .then(data => {
                if (!data || data.length === 0) return;
                
                // Group data by country, ambil top 5
                const countries = [...new Set(data.map(d => d.country_name))].slice(0, 5);
                const years = [...new Set(data.map(d => d.year))].sort();
                
                const colors = ['#2563eb', '#10b981', '#f59e0b', '#ef4444', '#7c3aed'];
                
                // GDP Chart (Line)
                const gdpCtx = document.getElementById('gdpChart').getContext('2d');
                new Chart(gdpCtx, {
                    type: 'line',
                    data: {
                        labels: years,
                        datasets: countries.map((country, index) => ({
                            label: country,
                            data: years.map(year => {
                                const item = data.find(d => d.country_name === country && d.year == year);
                                return item ? (item.gdp / 1000000000) : 0; // Convert to Billions
                            }),
                            borderColor: colors[index],
                            backgroundColor: colors[index] + '20',
                            borderWidth: 2.5,
                            tension: 0.4,
                            fill: false,
                            pointBackgroundColor: colors[index],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }))
                    },
                    options: {
                        responsive: true,
                        plugins: { 
                            legend: { position: 'bottom', labels: { padding: 15, font: { size: 11 } } },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': $' + context.parsed.y.toFixed(1) + 'B';
                                    }
                                }
                            }
                        },
                        scales: { 
                            y: { 
                                title: { display: true, text: 'GDP (Billions USD)', font: { weight: '600' } },
                                grid: { color: 'rgba(0,0,0,0.05)' }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });

                // Inflation Chart (Bar)
                const infCtx = document.getElementById('inflationChart').getContext('2d');
                new Chart(infCtx, {
                    type: 'bar',
                    data: {
                        labels: years,
                        datasets: countries.map((country, index) => ({
                            label: country,
                            data: years.map(year => {
                                const item = data.find(d => d.country_name === country && d.year == year);
                                return item ? item.inflation_rate : 0;
                            }),
                            backgroundColor: colors[index] + '90',
                            borderColor: colors[index],
                            borderWidth: 1,
                            borderRadius: 4
                        }))
                    },
                    options: {
                        responsive: true,
                        plugins: { 
                            legend: { position: 'bottom', labels: { padding: 15, font: { size: 11 } } },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + '%';
                                    }
                                }
                            }
                        },
                        scales: { 
                            y: { 
                                title: { display: true, text: 'Inflation Rate (%)', font: { weight: '600' } },
                                grid: { color: 'rgba(0,0,0,0.05)' }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            })
            .catch(err => console.error('Error loading economic trends:', err));
    }

    // Fungsi Weather
    function loadWeatherData(mapData) {
        if (!mapData || mapData.length === 0) return;
        
        const weatherPromises = mapData.map(async (country) => {
            try {
                const weatherResponse = await fetch(
                    `https://api.open-meteo.com/v1/forecast?latitude=${country.lat}&longitude=${country.lng}&current=temperature_2m,weather_code,precipitation,wind_speed_10m&daily=precipitation_sum&timezone=auto`
                );
                const weatherData = await weatherResponse.json();
                
                return {
                    name: country.name,
                    lat: country.lat,
                    lng: country.lng,
                    risk: country.risk,
                    riskLevel: country.level,
                    temperature: weatherData.current.temperature_2m,
                    weatherCode: weatherData.current.weather_code,
                    precipitation: weatherData.current.precipitation,
                    windSpeed: weatherData.current.wind_speed_10m,
                    dailyRain: weatherData.daily.precipitation_sum[0] || 0
                };
            } catch (error) {
                console.error(`Error fetching weather for ${country.name}:`, error);
                return null;
            }
        });
        
        Promise.all(weatherPromises).then(weatherData => {
            const validWeatherData = weatherData.filter(w => w !== null);
            displayWeatherOnMap(validWeatherData);
            updateWeatherTable(validWeatherData);
        });
    }

    function getWeatherInfo(code) {
        const weatherCodes = {
            0: { condition: 'Clear Sky', icon: '☀️', color: 'success', risk: 'Low' },
            1: { condition: 'Mainly Clear', icon: '🌤️', color: 'success', risk: 'Low' },
            2: { condition: 'Partly Cloudy', icon: '⛅', color: 'primary', risk: 'Low' },
            3: { condition: 'Overcast', icon: '☁️', color: 'primary', risk: 'Low' },
            45: { condition: 'Fog', icon: '🌫️', color: 'warning', risk: 'Medium' },
            48: { condition: 'Rime Fog', icon: '🌫️', color: 'warning', risk: 'Medium' },
            51: { condition: 'Light Drizzle', icon: '🌦️', color: 'info', risk: 'Low' },
            53: { condition: 'Drizzle', icon: '🌦️', color: 'info', risk: 'Low' },
            55: { condition: 'Dense Drizzle', icon: '🌧️', color: 'info', risk: 'Medium' },
            61: { condition: 'Slight Rain', icon: '🌧️', color: 'info', risk: 'Medium' },
            63: { condition: 'Moderate Rain', icon: '🌧️', color: 'warning', risk: 'Medium' },
            65: { condition: 'Heavy Rain', icon: '🌧️', color: 'warning', risk: 'High' },
            71: { condition: 'Slight Snow', icon: '🌨️', color: 'info', risk: 'Medium' },
            73: { condition: 'Snow', icon: '🌨️', color: 'warning', risk: 'Medium' },
            75: { condition: 'Heavy Snow', icon: '❄️', color: 'danger', risk: 'High' },
            95: { condition: 'Thunderstorm', icon: '⛈️', color: 'danger', risk: 'High' },
            96: { condition: 'Thunderstorm + Hail', icon: '⛈️', color: 'danger', risk: 'High' }
        };
        
        return weatherCodes[code] || { condition: 'Unknown', icon: '❓', color: 'secondary', risk: 'Low' };
    }

    function getMarkerColor(color) {
        const colors = {
            'blue': '#0d6efd',
            'red': '#dc3545',
            'orange': '#ffc107',
            'cyan': '#0dcaf0',
            'green': '#198754'
        };
        return colors[color] || '#6c757d';
    }

    function displayWeatherOnMap(weatherData) {
        weatherMarkers.forEach(marker => weatherMap.removeLayer(marker));
        weatherMarkers = [];
        
        weatherData.forEach(country => {
            const weatherInfo = getWeatherInfo(country.weatherCode);
            
            let markerColor = 'blue';
            if (weatherInfo.risk === 'High') {
                markerColor = 'red';
            } else if (weatherInfo.risk === 'Medium') {
                markerColor = 'orange';
            } else if (country.precipitation > 0 || country.dailyRain > 2) {
                markerColor = 'cyan';
            }
            
            const iconHtml = `
                <div style="
                    background-color: ${getMarkerColor(markerColor)};
                    width: 30px;
                    height: 30px;
                    border-radius: 50%;
                    border: 3px solid white;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 16px;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.3);
                ">${weatherInfo.icon}</div>
            `;
            
            const customIcon = L.divIcon({
                html: iconHtml,
                className: 'weather-marker',
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });
            
            const marker = L.marker([country.lat, country.lng], { icon: customIcon })
                .addTo(weatherMap)
                .bindPopup(`
                    <div style="min-width: 200px;">
                        <h6 style="margin: 0 0 10px 0; font-weight: 600;">${country.name}</h6>
                        <div style="margin-bottom: 5px;"><strong>Temperature:</strong> ${Math.round(country.temperature)}°C</div>
                        <div style="margin-bottom: 5px;"><strong>Condition:</strong> ${weatherInfo.icon} ${weatherInfo.condition}</div>
                        <div style="margin-bottom: 5px;"><strong>Rainfall:</strong> ${country.precipitation} mm</div>
                        <div style="margin-bottom: 5px;"><strong>Wind Speed:</strong> ${country.windSpeed} km/h</div>
                        <div style="margin-bottom: 5px;"><strong>Daily Rain:</strong> ${country.dailyRain.toFixed(1)} mm</div>
                        <div><strong>Risk Level:</strong> <span class="badge bg-${weatherInfo.color}">${weatherInfo.risk}</span></div>
                    </div>
                `);
            
            weatherMarkers.push(marker);
        });
    }

    function updateWeatherTable(weatherData) {
        // Sort by extreme weather conditions (rain or wind)
        weatherData.sort((a, b) => (b.precipitation + b.windSpeed) - (a.precipitation + a.windSpeed));

        // 1. Populate Main Table (Top 5)
        const tbody = document.querySelector('#weatherTable tbody');
        tbody.innerHTML = '';
        const top5 = weatherData.slice(0, 5);
        
        top5.forEach(country => {
            const weatherInfo = getWeatherInfo(country.weatherCode);
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><strong>${country.name}</strong></td>
                <td>${Math.round(country.temperature)}°C</td>
                <td>${weatherInfo.icon} ${weatherInfo.condition}</td>
                <td>${country.precipitation} mm</td>
                <td>${country.windSpeed} km/h</td>
                <td><span class="badge-modern badge-${weatherInfo.color === 'success' ? 'success' : (weatherInfo.color === 'warning' ? 'warning' : (weatherInfo.color === 'danger' ? 'danger' : 'info'))}-modern">${weatherInfo.risk} Risk</span></td>
            `;
            tbody.appendChild(row);
        });

        // 2. Populate Modal Table (All Countries)
        const modalTbody = document.querySelector('#weatherModalTable tbody');
        if(modalTbody) {
            modalTbody.innerHTML = '';
            weatherData.forEach(country => {
                const weatherInfo = getWeatherInfo(country.weatherCode);
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><strong>${country.name}</strong></td>
                    <td>${Math.round(country.temperature)}°C</td>
                    <td>${weatherInfo.icon} ${weatherInfo.condition}</td>
                    <td>${country.precipitation} mm (Daily: ${country.dailyRain.toFixed(1)} mm)</td>
                    <td>${country.windSpeed} km/h</td>
                    <td><span class="badge-modern badge-${weatherInfo.color === 'success' ? 'success' : (weatherInfo.color === 'warning' ? 'warning' : (weatherInfo.color === 'danger' ? 'danger' : 'info'))}-modern">${weatherInfo.risk} Risk</span></td>
                `;
                modalTbody.appendChild(row);
            });
        }
    }

    // Fungsi Update Tabel
    window.updateTable = function(countries) {
        const tbody = document.getElementById('countryTableBody');
        tbody.innerHTML = '';
        
        if (!countries || countries.length === 0) return;
        
        countries.forEach(country => {
            const risk = country.risk_scores && country.risk_scores[0] ? country.risk_scores[0] : { total_risk_score: 0, risk_level: 'Low' };
            let badgeClass = 'badge-success-modern';
            if(risk.risk_level === 'Medium') badgeClass = 'badge-warning-modern';
            if(risk.risk_level === 'High') badgeClass = 'badge-danger-modern';
            if(risk.risk_level === 'Critical') badgeClass = 'badge-dark-modern';

            const isInWatchlist = watchlistIds.includes(country.id);
            
            const row = `
                <tr id="row-${country.id}">
                    <td><a href="/country/${country.id}" class="country-link">${country.name}</a></td>
                    <td><strong>${risk.total_risk_score || 0}</strong></td>
                    <td><span class="badge-modern ${badgeClass}">${risk.risk_level || 'Low'}</span></td>
                    <td>
                        <button class="btn btn-sm ${isInWatchlist ? 'btn-watch active' : 'btn-watch'}" 
                                onclick="toggleWatchlist(${country.id})" 
                                id="btn-watch-${country.id}"
                                title="${isInWatchlist ? 'Remove from watchlist' : 'Add to watchlist'}">
                            ${isInWatchlist ? '⭐' : '☆'}
                        </button>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    };

    // Fungsi Toggle Watchlist
    window.toggleWatchlist = function(countryId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const button = document.getElementById(`btn-watch-${countryId}`);
        
        fetch('/api/watchlist/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ country_id: countryId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'added' || data.status === 'guest_toggled') {
                if (data.status === 'guest_toggled' && watchlistIds.includes(countryId)) {
                    // Guest remove fallback
                    watchlistIds = watchlistIds.filter(id => id !== countryId);
                    button.classList.remove('active');
                    button.innerHTML = '☆';
                    document.getElementById('watchlistCount').textContent = watchlistIds.length;
                    return;
                }
                
                watchlistIds.push(countryId);
                button.classList.remove('btn-outline-warning');
                button.classList.add('active');
                button.innerHTML = '⭐';
                document.getElementById('watchlistCount').textContent = watchlistIds.length;
                
                if (data.status === 'guest_toggled') {
                    // Optional: Show a small toast/alert for guest users
                    console.log('Added to temporary guest watchlist (will reset on refresh)');
                }
            } else if (data.status === 'removed') {
                watchlistIds = watchlistIds.filter(id => id !== countryId);
                button.classList.remove('active');
                button.innerHTML = '☆';
                document.getElementById('watchlistCount').textContent = watchlistIds.length;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        });
    };

    // Fungsi Chart dan Map
    function initChart(labels, riskData) {
        if (!labels || !riskData || labels.length === 0) return;
        
        const ctx = document.getElementById('riskChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Risk Score (0-100)',
                    data: riskData,
                    backgroundColor: riskData.map(score => 
                        score < 25 ? 'rgba(16, 185, 129, 0.7)' : 
                        score < 50 ? 'rgba(245, 158, 11, 0.7)' : 
                        score < 75 ? 'rgba(239, 68, 68, 0.7)' : 'rgba(124, 58, 237, 0.7)'
                    ),
                    borderColor: riskData.map(score => 
                        score < 25 ? '#10b981' : 
                        score < 50 ? '#f59e0b' : 
                        score < 75 ? '#ef4444' : '#7c3aed'
                    ),
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: { 
                    y: { beginAtZero: true, max: 100, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    function initCurrencyChart(countries, rates) {
        if (!countries || countries.length === 0) return;
        
        const ctx = document.getElementById('currencyChart').getContext('2d');
        const currencyData = countries.filter(c => c.currency_code && c.currency_code !== 'USD').slice(0, 10);
        
        // Dynamic rates from ExchangeRate API with static fallback defaults
        const defaultRates = {
            'IDR': 15500, 'CNY': 7.25, 'EUR': 0.92, 'JPY': 155, 
            'GBP': 0.79, 'AUD': 1.52, 'SGD': 1.35, 'MYR': 4.70,
            'THB': 36.5, 'VND': 24500, 'INR': 83.5, 'BRL': 5.15,
            'RUB': 92, 'KRW': 1350, 'AED': 3.67, 'CAD': 1.36
        };
        const activeRates = rates && Object.keys(rates).length > 0 ? rates : defaultRates;

        const labels = currencyData.map(c => c.currency_code);
        const data = currencyData.map(c => activeRates[c.currency_code] || defaultRates[c.currency_code] || 0);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Exchange Rate (per 1 USD)',
                    data: data,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: { 
                responsive: true, 
                plugins: { legend: { display: false } },
                scales: { 
                    y: { beginAtZero: false, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    function initMapMarkers(mapData) {
        if (!mapData || mapData.length === 0) return;
        
        mapData.forEach(item => {
            let color = '#10b981';
            if(item.level === 'Medium') color = '#f59e0b';
            if(item.level === 'High' || item.level === 'Critical') color = '#ef4444';

            L.circleMarker([item.lat, item.lng], {
                radius: 9,
                fillColor: color,
                color: '#fff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.85
            }).addTo(map).bindPopup(`
                <div style="min-width: 180px;">
                    <h6 style="margin: 0 0 8px 0; font-weight: 600;">${item.name}</h6>
                    <div style="margin-bottom: 4px;"><strong>Risk Score:</strong> ${item.risk}</div>
                    <div><strong>Level:</strong> <span class="badge bg-${item.level === 'Low' ? 'success' : (item.level === 'Medium' ? 'warning' : 'danger')}">${item.level}</span></div>
                </div>
            `);
        });
    }
});
</script>

<!-- Tracking Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="trackingOffcanvas" aria-labelledby="trackingOffcanvasLabel" style="width: 450px;">
  <div class="offcanvas-header bg-dark text-white">
    <h5 class="offcanvas-title" id="trackingOffcanvasLabel"><i class="bi bi-box-seam"></i> Lacak Paket</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body p-4 bg-white">
    <form id="trackingForm" class="mb-4">
        @csrf
        <label class="form-label fw-bold">Nomor Resi</label>
        <div class="input-group">
            <input type="text" id="trackingNumber" name="tracking_number" class="form-control" placeholder="Cth: TA-2026-0001" required>
            <button class="btn btn-primary" type="submit" id="btnTrack">
                <span id="trackBtnText">Lacak</span>
                <span class="spinner-border spinner-border-sm d-none" id="trackSpinner" role="status" aria-hidden="true"></span>
            </button>
        </div>
        <div id="trackingError" class="text-danger small mt-2 d-none"></div>
    </form>

    <div id="trackingResultContainer">
        <!-- Hasil AJAX akan dimasukkan ke sini -->
        <div class="text-center text-muted mt-5" id="trackingEmptyState">
            <i class="bi bi-box display-4 mb-2"></i>
            <p>Masukkan resi untuk melacak perjalanan paket Anda dan mengetahui risiko di lokasi terkininya.</p>
        </div>
    </div>
  </div>
</div>

<script>
// Logic untuk form tracking
document.addEventListener('DOMContentLoaded', function() {
    const trackingForm = document.getElementById('trackingForm');
    if (trackingForm) {
        trackingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const trackingNumber = document.getElementById('trackingNumber').value;
            const btnText = document.getElementById('trackBtnText');
            const spinner = document.getElementById('trackSpinner');
            const btn = document.getElementById('btnTrack');
            const errorDiv = document.getElementById('trackingError');
            const resultContainer = document.getElementById('trackingResultContainer');
            
            // Reset state
            errorDiv.classList.add('d-none');
            btn.disabled = true;
            btnText.classList.add('d-none');
            spinner.classList.remove('d-none');
            
            fetch('{{ route("api.tracking.search") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'text/html'
                },
                body: JSON.stringify({ tracking_number: trackingNumber })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.text();
            })
            .then(html => {
                resultContainer.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                errorDiv.textContent = error.error || 'Terjadi kesalahan saat melacak paket.';
                errorDiv.classList.remove('d-none');
                resultContainer.innerHTML = `
                    <div class="text-center text-muted mt-5" id="trackingEmptyState">
                        <i class="bi bi-box display-4 mb-2"></i>
                        <p>Masukkan resi untuk melacak perjalanan paket Anda dan mengetahui risiko di lokasi terkininya.</p>
                    </div>
                `;
            })
            .finally(() => {
                btn.disabled = false;
                btnText.classList.remove('d-none');
                spinner.classList.add('d-none');
            });
        });
    }
});
</script>

</body>
</html>