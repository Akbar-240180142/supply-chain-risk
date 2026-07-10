<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Global Supply Chain Risk Intelligence - Modern Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            --bg-card: #ffffff;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-main); color: var(--gray-800); line-height: 1.6; }
        
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
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .navbar-brand-modern:hover { transform: translateY(-2px); color: var(--primary-light); }

        .nav-link-modern {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: var(--radius-md);
            transition: all 0.25s ease;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link-modern:hover { background: rgba(255, 255, 255, 0.1); color: white; transform: translateY(-1px); }

        .card-modern {
            background: var(--bg-card);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--gray-200);
            transition: all 0.25s ease;
            overflow: hidden;
        }

        .card-modern:hover { transform: translateY(-4px); box-shadow: var(--shadow-xl); }

        .card-header-modern {
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title-modern {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-800);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-body-modern { padding: 1.5rem; }

        .stat-card {
            background: var(--bg-card);
            border-radius: var(--radius-xl);
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--gray-200);
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
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

        .stat-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-xl); }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-icon-primary { background: rgba(37, 99, 235, 0.1); color: var(--primary); }
        .stat-icon-success { background: rgba(16, 185, 129, 0.1); color: var(--success); }
        .stat-icon-warning { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
        .stat-icon-danger { background: rgba(239, 68, 68, 0.1); color: var(--danger); }

        .stat-value { font-size: 2rem; font-weight: 700; color: var(--gray-900); line-height: 1; margin-bottom: 0.5rem; }
        .stat-label { font-size: 0.875rem; color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 500; }

        .badge-modern {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .badge-success { background: rgba(16, 185, 129, 0.1); color: var(--success); }
        .badge-warning { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
        .badge-danger { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
        .badge-info { background: rgba(59, 130, 246, 0.1); color: var(--info); }

        .table-modern { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-modern thead th {
            background: var(--gray-50);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gray-600);
            border-bottom: 2px solid var(--gray-200);
        }
        .table-modern tbody td { padding: 1rem; border-bottom: 1px solid var(--gray-100); transition: all 0.15s ease; }
        .table-modern tbody tr:hover { background: var(--gray-50); }
        .table-modern tbody tr:last-child td { border-bottom: none; }

        #map, #weatherMap { height: 500px; border-radius: var(--radius-xl); }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fadeIn { animation: fadeIn 0.5s ease-out; }
    </style>
</head>
<body>

<nav class="navbar-modern">
    <div class="container-fluid">
        <a class="navbar-brand-modern" href="/">
            <span></span>
            <span>Supply Chain Risk Intelligence</span>
        </a>
        <div class="d-flex gap-2 flex-wrap">
            <a href="/comparison" class="nav-link-modern"><span>🔀</span> Comparison</a>
            <a href="/ports" class="nav-link-modern"><span></span> Ports</a>
            <a href="/news" class="nav-link-modern"><span>📰</span> News</a>
            <a href="/watchlist" class="nav-link-modern"><span>⭐</span> Watchlist</a>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 py-4">
    <!-- Stats Row -->
    <div class="row g-4 mb-4 animate-fadeIn">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon stat-icon-primary">🌍</div>
                <div class="stat-value" id="totalCountries">0</div>
                <div class="stat-label">Countries Monitored</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon stat-icon-success">📊</div>
                <div class="stat-value" id="avgRiskScore">0</div>
                <div class="stat-label">Average Risk Score</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon stat-icon-warning">⚠️</div>
                <div class="stat-value" id="highRiskCountries">0</div>
                <div class="stat-label">High Risk Countries</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon stat-icon-danger">🌊</div>
                <div class="stat-value" id="weatherAlerts">0</div>
                <div class="stat-label">Weather Alerts</div>
            </div>
        </div>
    </div>

    <!-- Maps Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card-modern animate-fadeIn">
                <div class="card-header-modern">
                    <h5 class="card-title-modern"><span>🗺️</span> Global Risk Map</h5>
                    <div class="d-flex gap-2">
                        <span class="badge-modern badge-success">Low</span>
                        <span class="badge-modern badge-warning">Medium</span>
                        <span class="badge-modern badge-danger">High</span>
                    </div>
                </div>
                <div class="card-body-modern">
                    <div id="map"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card-modern animate-fadeIn" style="animation-delay: 0.1s;">
                <div class="card-header-modern">
                    <h5 class="card-title-modern"><span></span> Country Risk List</h5>
                </div>
                <div class="card-body-modern" style="max-height: 500px; overflow-y: auto;">
                    <table class="table-modern" id="countryTable">
                        <thead>
                            <tr><th>Country</th><th>Score</th><th>Level</th></tr>
                        </thead>
                        <tbody id="countryTableBody">
                            <tr><td colspan="3" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Weather Map -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card-modern animate-fadeIn" style="animation-delay: 0.2s;">
                <div class="card-header-modern">
                    <h5 class="card-title-modern"><span>️</span> Global Weather Monitoring</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge-modern badge-info">☁️ Normal</span>
                        <span class="badge-modern badge-info">🌧️ Rain</span>
                        <span class="badge-modern badge-warning">⛈️ Storm</span>
                        <span class="badge-modern badge-danger">💨 High Wind</span>
                    </div>
                </div>
                <div class="card-body-modern">
                    <div id="weatherMap"></div>
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

document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('map').setView([20, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const weatherMap = L.map('weatherMap').setView([20, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(weatherMap);

    Promise.all([
        fetch('/api/watchlist').then(r => r.json()),
        fetch('/api/dashboard-data').then(r => r.json())
    ])
    .then(([watchlistData, dashboardData]) => {
        watchlistIds = watchlistData.map(item => item.country_id);
        
        let totalRisk = 0;
        let highRisk = 0;
        
        const tbody = document.getElementById('countryTableBody');
        tbody.innerHTML = '';
        
        dashboardData.countries.forEach(country => {
            const risk = country.risk_scores[0] || { total_risk_score: 0, risk_level: 'Low' };
            totalRisk += risk.total_risk_score;
            if(risk.risk_level === 'High' || risk.risk_level === 'Critical') highRisk++;
            
            let badgeClass = 'badge-success';
            if(risk.risk_level === 'Medium') badgeClass = 'badge-warning';
            if(risk.risk_level === 'High' || risk.risk_level === 'Critical') badgeClass = 'badge-danger';

            const row = `
                <tr>
                    <td><a href="/country/${country.id}" class="text-decoration-none fw-bold text-primary">${country.name}</a></td>
                    <td>${risk.total_risk_score}</td>
                    <td><span class="badge-modern ${badgeClass}">${risk.risk_level}</span></td>
                </tr>
            `;
            tbody.innerHTML += row;
        });

        document.getElementById('totalCountries').textContent = dashboardData.countries.length;
        document.getElementById('avgRiskScore').textContent = (totalRisk / dashboardData.countries.length).toFixed(1);
        document.getElementById('highRiskCountries').textContent = highRisk;
        document.getElementById('weatherAlerts').textContent = Math.floor(Math.random() * 5);

        dashboardData.map_data.forEach(item => {
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
    })
    .catch(error => console.error('Error:', error));
});
</script>
</body>
</html>