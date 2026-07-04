<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Global Supply Chain Risk Intelligence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; margin-bottom: 20px; }
        #map { height: 500px; width: 100%; border-radius: 8px; }
        .risk-low { color: #198754; font-weight: bold; }
        .risk-medium { color: #ffc107; font-weight: bold; }
        .risk-high { color: #fd7e14; font-weight: bold; }
        .risk-critical { color: #dc3545; font-weight: bold; }
        .btn-watch { 
            transition: all 0.3s;
            min-width: 35px;
        }
        .btn-watch.active {
            background-color: #ffc107 !important;
            color: #000 !important;
            border-color: #ffc107 !important;
        }
        .btn-watch:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">🌍 Global Supply Chain Risk Intelligence</span>
        <div>
            <a href="/comparison" class="btn btn-outline-light me-2">Country Comparison</a>
            <a href="/ports" class="btn btn-outline-light me-2">Port Locations</a>
            <a href="/news" class="btn btn-outline-light me-2">News Intelligence</a>
            <a href="/watchlist" class="btn btn-outline-warning">⭐ My Watchlist</a>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card p-3">
                <h5 class="card-title">🗺️ Global Risk Map</h5>
                <div id="map"></div>
            </div>
            <div class="card p-3">
                <h5 class="card-title">📊 Country Risk Scores</h5>
                <canvas id="riskChart"></canvas>
            </div>
            <div class="card p-3">
                <h5 class="card-title">💱 Currency Exchange Rates (vs USD)</h5>
                <canvas id="currencyChart"></canvas>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3">
                <h5 class="card-title">📋 Country Risk List</h5>
                <div class="table-responsive" style="max-height: 800px; overflow-y: auto;">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Country</th>
                                <th>Risk Score</th>
                                <th>Level</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="countryTableBody">
                            <tr><td colspan="4" class="text-center">Loading data...</td></tr>
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

document.addEventListener('DOMContentLoaded', function() {
    // Init map
    const map = L.map('map').setView([20, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Load watchlist DAN dashboard data bersamaan
    Promise.all([
        fetch('/api/watchlist').then(r => r.json()),
        fetch('/api/dashboard-data').then(r => r.json())
    ])
    .then(([watchlistData, dashboardData]) => {
        // Simpan watchlist IDs
        watchlistIds = watchlistData.map(item => item.country_id);
        console.log('✅ Watchlist loaded:', watchlistIds);
        
        // Simpan semua data negara
        allCountriesData = dashboardData.countries;
        
        // Render semua
        updateTable(allCountriesData);
        initChart(dashboardData.chart_labels, dashboardData.chart_risk);
        initMapMarkers(dashboardData.map_data);
        initCurrencyChart(allCountriesData);
    })
    .catch(error => console.error('Error:', error));

    // Fungsi Update Tabel
    window.updateTable = function(countries) {
        const tbody = document.getElementById('countryTableBody');
        tbody.innerHTML = '';
        
        countries.forEach(country => {
            const risk = country.risk_scores[0] || { total_risk_score: 0, risk_level: 'Low' };
            let badgeClass = 'bg-success';
            if(risk.risk_level === 'Medium') badgeClass = 'bg-warning text-dark';
            if(risk.risk_level === 'High') badgeClass = 'bg-danger';
            if(risk.risk_level === 'Critical') badgeClass = 'bg-dark';

            // Cek apakah sudah di watchlist
            const isInWatchlist = watchlistIds.includes(country.id);
            
            const row = `
                <tr id="row-${country.id}">
                    <td><a href="/country/${country.id}" class="text-decoration-none fw-bold text-primary">${country.name}</a></td>
                    <td>${risk.total_risk_score}</td>
                    <td><span class="badge ${badgeClass}">${risk.risk_level}</span></td>
                    <td>
                        <button class="btn btn-sm ${isInWatchlist ? 'btn-warning active' : 'btn-outline-warning'} btn-watch" 
                                onclick="toggleWatchlist(${country.id})" 
                                id="btn-watch-${country.id}">
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
        
        console.log('Toggling country:', countryId);
        
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
            console.log('Response:', data);
            
            if (data.status === 'added') {
                // Tambah ke array
                watchlistIds.push(countryId);
                // Update tombol
                button.classList.remove('btn-outline-warning');
                button.classList.add('btn-warning', 'active');
                button.innerHTML = '⭐';
                alert('✅ Added to watchlist!');
            } else if (data.status === 'removed') {
                // Hapus dari array
                watchlistIds = watchlistIds.filter(id => id !== countryId);
                // Update tombol
                button.classList.remove('btn-warning', 'active');
                button.classList.add('btn-outline-warning');
                button.innerHTML = '☆';
                alert('❌ Removed from watchlist!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        });
    };

    // Fungsi Chart dan Map (sama seperti sebelumnya)
    function initChart(labels, riskData) {
        const ctx = document.getElementById('riskChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Risk Score (0-100)',
                    data: riskData,
                    backgroundColor: riskData.map(score => 
                        score < 25 ? '#198754' : 
                        score < 50 ? '#ffc107' : 
                        score < 75 ? '#fd7e14' : '#dc3545'
                    ),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true, max: 100 } }
            }
        });
    }

    function initCurrencyChart(countries) {
        const ctx = document.getElementById('currencyChart').getContext('2d');
        const currencyData = countries.filter(c => c.currency_code && c.currency_code !== 'USD').slice(0, 10);
        const rates = {
            'IDR': 15500, 'CNY': 7.25, 'EUR': 0.92, 'JPY': 155, 
            'GBP': 0.79, 'AUD': 1.52, 'SGD': 1.35, 'MYR': 4.70,
            'THB': 36.5, 'VND': 24500, 'INR': 83.5, 'BRL': 5.15,
            'RUB': 92, 'KRW': 1350, 'AED': 3.67, 'CAD': 1.36
        };
        const labels = currencyData.map(c => c.currency_code);
        const data = currencyData.map(c => rates[c.currency_code] || 0);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Exchange Rate (per 1 USD)',
                    data: data,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: false } } }
        });
    }

    function initMapMarkers(mapData) {
        mapData.forEach(item => {
            let color = 'green';
            if(item.level === 'Medium') color = 'orange';
            if(item.level === 'High' || item.level === 'Critical') color = 'red';

            L.circleMarker([item.lat, item.lng], {
                radius: 8,
                fillColor: color,
                color: '#fff',
                weight: 1,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(map).bindPopup(`<b>${item.name}</b><br>Risk Score: ${item.risk}<br>Level: ${item.level}`);
        });
    }
});
</script>
</body>
</html>