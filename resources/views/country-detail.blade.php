<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $country->name }} - Risk Detail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: none; margin-bottom: 20px; }
        .stat-value { font-size: 1.5rem; font-weight: bold; color: #0d6efd; }
        .stat-label { font-size: 0.85rem; color: #6c757d; text-transform: uppercase; }
        .risk-badge { font-size: 1rem; padding: 5px 15px; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="/">🌍 Supply Chain Risk Intelligence</a>
        <a href="/" class="btn btn-outline-light btn-sm">← Back to Dashboard</a>
    </div>
</nav>

<div class="container">
    <!-- Header Negara -->
    <div class="card p-4 mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-1">{{ $country->name }}</h2>
                <p class="text-muted mb-0">{{ $country->capital }} • {{ $country->region }}</p>
            </div>
            <div class="col-md-4 text-md-end">
                @php
                    $latestRisk = $country->riskScores->last();
                    $score = $latestRisk ? $latestRisk->total_risk_score : 0;
                    $level = $latestRisk ? $latestRisk->risk_level : 'Low';
                    
                    $badgeClass = 'bg-success';
                    if($level === 'Medium') $badgeClass = 'bg-warning text-dark';
                    if($level === 'High') $badgeClass = 'bg-danger';
                    if($level === 'Critical') $badgeClass = 'bg-dark';
                @endphp
                <div class="stat-label">Current Risk Score</div>
                <div class="stat-value">{{ number_format($score, 2) }}</div>
                <span class="badge {{ $badgeClass }} risk-badge">{{ $level }} Risk</span>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Kolom Kiri: Grafik & Berita -->
        <div class="col-md-8">
            <!-- Grafik Risk Score -->
            <div class="card p-4">
                <h5 class="card-title mb-3">📈 Risk Score History</h5>
                <canvas id="riskHistoryChart" height="100"></canvas>
            </div>

            <!-- Berita Terbaru -->
            <div class="card p-4">
                <h5 class="card-title mb-3">📰 Latest News Intelligence</h5>
                @if($country->news->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($country->news as $news)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $news->title }}</h6>
                                        <small class="text-muted">{{ $news->source }} • {{ \Carbon\Carbon::parse($news->published_at)->format('M d, Y') }}</small>
                                    </div>
                                    @php
                                        $newsBadge = $news->sentiment === 'Positive' ? 'bg-success' : ($news->sentiment === 'Negative' ? 'bg-danger' : 'bg-secondary');
                                    @endphp
                                    <span class="badge {{ $newsBadge }}">{{ $news->sentiment }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No recent news available for this country.</p>
                @endif
            </div>
        </div>

        <!-- Kolom Kanan: Weather, Info & Ekonomi -->
        <div class="col-md-4">
            
            <!-- 🌤️ Live Weather Widget -->
            <div class="card p-4 mb-4 shadow-sm">
                <h5 class="card-title mb-3">🌤️ Live Weather</h5>
                <div class="text-center py-4" id="weatherLoading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted small">Fetching data...</p>
                </div>
                <div id="weatherContent" style="display: none;">
                    <div class="text-center mb-3">
                        <div style="font-size: 3rem; font-weight: bold; color: #0d6efd;" id="temperature">--°C</div>
                        <div class="text-muted" id="weatherDesc">--</div>
                    </div>
                    <div class="row text-center">
                        <div class="col-4 border-end">
                            <div class="text-muted small">Wind</div>
                            <div class="fw-bold" id="windSpeed">-- km/h</div>
                        </div>
                        <div class="col-4 border-end">
                            <div class="text-muted small">Humidity</div>
                            <div class="fw-bold" id="humidity">--%</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small">Rain</div>
                            <div class="fw-bold" id="rainfall">-- mm</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Country Info -->
            <div class="card p-4">
                <h5 class="card-title mb-3">🌍 Country Info</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-3">
                        <div class="stat-label">Capital City</div>
                        <div class="fw-bold">{{ $country->capital }}</div>
                    </li>
                    <li class="mb-3">
                        <div class="stat-label">Region</div>
                        <div class="fw-bold">{{ $country->region }}</div>
                    </li>
                    <li class="mb-3">
                        <div class="stat-label">Currency</div>
                        <div class="fw-bold">{{ $country->currency_code }}</div>
                    </li>
                    @if($currentExchangeRate)
                    <li class="mb-3">
                        <div class="stat-label">Exchange Rate (vs USD)</div>
                        <div class="fw-bold text-primary">1 USD = {{ number_format($currentExchangeRate, 2) }} {{ $country->currency_code }}</div>
                    </li>
                    @endif
                    <li>
                        <div class="stat-label">Coordinates</div>
                        <div class="fw-bold">{{ $country->latitude }}, {{ $country->longitude }}</div>
                    </li>
                </ul>
            </div>

            <!-- Economic Indicators -->
            <div class="card p-4">
                <h5 class="card-title mb-3">💰 Economic Indicators</h5>
                @if($country->economicIndicators->count() > 0)
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Year</th>
                                <th>GDP</th>
                                <th>Inflation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($country->economicIndicators as $econ)
                                <tr>
                                    <td>{{ $econ->year }}</td>
                                    <td>${{ number_format($econ->gdp / 1000000000, 1) }}B</td>
                                    <td>{{ $econ->inflation_rate }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">No economic data available.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // Risk Score Chart
    const dates = @json($country->riskScores->pluck('record_date'));
    const scores = @json($country->riskScores->pluck('total_risk_score'));

    const ctx = document.getElementById('riskHistoryChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates.map(d => new Date(d).toLocaleDateString()),
            datasets: [{
                label: 'Total Risk Score',
                data: scores,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointRadius: 4,
                pointBackgroundColor: '#0d6efd'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: { display: true, text: 'Risk Score (0-100)' }
                }
            }
        }
    });

    // Fetch weather data from Open-Meteo API
    async function fetchWeather() {
        const lat = {{ $country->latitude }};
        const lon = {{ $country->longitude }};
        
        try {
            const response = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m&daily=precipitation_sum&timezone=auto`);
            const data = await response.json();
            
            document.getElementById('temperature').textContent = Math.round(data.current.temperature_2m) + '°C';
            document.getElementById('windSpeed').textContent = data.current.wind_speed_10m + ' km/h';
            document.getElementById('humidity').textContent = data.current.relative_humidity_2m + '%';
            document.getElementById('rainfall').textContent = (data.daily.precipitation_sum[0] || 0) + ' mm';
            
            const weatherCode = data.current.weather_code;
            const codes = {
                0: '☀️ Clear Sky', 1: '🌤️ Mainly Clear', 2: '⛅ Partly Cloudy', 3: '☁️ Overcast',
                45: '🌫️ Fog', 48: '🌫️ Rime Fog', 51: '🌦️ Light Drizzle', 53: '🌦️ Drizzle',
                55: '🌧️ Dense Drizzle', 61: '🌧️ Slight Rain', 63: '🌧️ Moderate Rain',
                65: '🌧️ Heavy Rain', 71: '🌨️ Slight Snow', 73: '🌨️ Snow', 75: '❄️ Heavy Snow',
                95: '⛈️ Thunderstorm', 96: '⛈️ Thunderstorm + Hail'
            };
            document.getElementById('weatherDesc').textContent = codes[weatherCode] || '🌡️ Weather';
            
            document.getElementById('weatherLoading').style.display = 'none';
            document.getElementById('weatherContent').style.display = 'block';
        } catch (error) {
            document.getElementById('weatherLoading').innerHTML = '<p class="text-muted">Unable to load weather data</p>';
        }
    }
    fetchWeather();
</script>
</body>
</html>