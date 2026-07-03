<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Country Comparison - Supply Chain Risk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; margin-bottom: 20px; }
        .comparison-card { border-left: 4px solid #0d6efd; }
        .risk-low { color: #198754; font-weight: bold; }
        .risk-medium { color: #ffc107; font-weight: bold; }
        .risk-high { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">🌍 Supply Chain Risk Intelligence</a>
        <span class="navbar-text text-white">Country Comparison Engine</span>
    </div>
</nav>

<div class="container">
    <!-- Selector Negara -->
    <div class="card p-4 mb-4">
        <h4 class="mb-3"> Compare Two Countries</h4>
        <div class="row">
            <div class="col-md-5">
                <label class="form-label">Country 1:</label>
                <select id="country1" class="form-select">
                    <option value="">Select country...</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100" onclick="compareCountries()">Compare</button>
            </div>
            <div class="col-md-5">
                <label class="form-label">Country 2:</label>
                <select id="country2" class="form-select">
                    <option value="">Select country...</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Hasil Comparison -->
    <div id="comparisonResult" style="display: none;">
        <div class="row">
            <!-- Country 1 Card -->
            <div class="col-md-6">
                <div class="card p-4 comparison-card" id="card1">
                    <h3 id="name1" class="mb-3"></h3>
                    <div class="mb-3">
                        <small class="text-muted">Risk Score</small>
                        <h2 id="risk1" class="mb-0"></h2>
                        <span id="level1" class="badge"></span>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">GDP</small>
                            <p id="gdp1" class="fw-bold mb-0"></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Inflation</small>
                            <p id="inflation1" class="fw-bold mb-0"></p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Currency</small>
                            <p id="currency1" class="fw-bold mb-0"></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Weather Risk</small>
                            <p id="weather1" class="fw-bold mb-0"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Country 2 Card -->
            <div class="col-md-6">
                <div class="card p-4 comparison-card" id="card2" style="border-left-color: #dc3545;">
                    <h3 id="name2" class="mb-3"></h3>
                    <div class="mb-3">
                        <small class="text-muted">Risk Score</small>
                        <h2 id="risk2" class="mb-0"></h2>
                        <span id="level2" class="badge"></span>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">GDP</small>
                            <p id="gdp2" class="fw-bold mb-0"></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Inflation</small>
                            <p id="inflation2" class="fw-bold mb-0"></p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Currency</small>
                            <p id="currency2" class="fw-bold mb-0"></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Weather Risk</small>
                            <p id="weather2" class="fw-bold mb-0"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comparison Chart -->
        <div class="card p-4 mt-4">
            <h4 class="mb-3"> Risk Comparison Chart</h4>
            <canvas id="comparisonChart"></canvas>
        </div>
    </div>
</div>

<script>
let allCountries = [];
let comparisonChart = null;

// Load countries on page load
fetch('/api/dashboard-data')
    .then(response => response.json())
    .then(data => {
        allCountries = data.countries;
        populateSelects();
    });

function populateSelects() {
    const select1 = document.getElementById('country1');
    const select2 = document.getElementById('country2');
    
    allCountries.forEach(country => {
        select1.add(new Option(country.name, country.id));
        select2.add(new Option(country.name, country.id));
    });
}

function compareCountries() {
    const id1 = document.getElementById('country1').value;
    const id2 = document.getElementById('country2').value;
    
    if (!id1 || !id2) {
        alert('Please select both countries!');
        return;
    }
    
    if (id1 === id2) {
        alert('Please select different countries!');
        return;
    }
    
    const country1 = allCountries.find(c => c.id == id1);
    const country2 = allCountries.find(c => c.id == id2);
    
    displayComparison(country1, country2);
}

function displayComparison(c1, c2) {
    document.getElementById('comparisonResult').style.display = 'block';
    
    // Country 1
    const risk1 = c1.risk_scores[0] || { total_risk_score: 0, risk_level: 'Low', weather_risk: 0 };
    const econ1 = c1.economic_indicators[0] || { gdp: 0, inflation_rate: 0 };
    
    document.getElementById('name1').textContent = c1.name;
    document.getElementById('risk1').textContent = risk1.total_risk_score;
    document.getElementById('level1').textContent = risk1.risk_level;
    document.getElementById('level1').className = `badge ${getBadgeClass(risk1.risk_level)}`;
    document.getElementById('gdp1').textContent = econ1.gdp ? `$${(econ1.gdp/1e9).toFixed(1)}B` : 'N/A';
    document.getElementById('inflation1').textContent = econ1.inflation_rate ? `${econ1.inflation_rate}%` : 'N/A';
    document.getElementById('currency1').textContent = c1.currency_code || 'N/A';
    document.getElementById('weather1').textContent = risk1.weather_risk || 0;
    
    // Country 2
    const risk2 = c2.risk_scores[0] || { total_risk_score: 0, risk_level: 'Low', weather_risk: 0 };
    const econ2 = c2.economic_indicators[0] || { gdp: 0, inflation_rate: 0 };
    
    document.getElementById('name2').textContent = c2.name;
    document.getElementById('risk2').textContent = risk2.total_risk_score;
    document.getElementById('level2').textContent = risk2.risk_level;
    document.getElementById('level2').className = `badge ${getBadgeClass(risk2.risk_level)}`;
    document.getElementById('gdp2').textContent = econ2.gdp ? `$${(econ2.gdp/1e9).toFixed(1)}B` : 'N/A';
    document.getElementById('inflation2').textContent = econ2.inflation_rate ? `${econ2.inflation_rate}%` : 'N/A';
    document.getElementById('currency2').textContent = c2.currency_code || 'N/A';
    document.getElementById('weather2').textContent = risk2.weather_risk || 0;
    
    // Update Chart
    updateChart(c1.name, c2.name, risk1, risk2);
}

function getBadgeClass(level) {
    if (level === 'Low') return 'bg-success';
    if (level === 'Medium') return 'bg-warning text-dark';
    if (level === 'High' || level === 'Critical') return 'bg-danger';
    return 'bg-secondary';
}

function updateChart(name1, name2, risk1, risk2) {
    const ctx = document.getElementById('comparisonChart').getContext('2d');
    
    if (comparisonChart) {
        comparisonChart.destroy();
    }
    
    comparisonChart = new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Weather Risk', 'Inflation Risk', 'Currency Risk', 'News Risk', 'Total Risk'],
            datasets: [
                {
                    label: name1,
                    data: [risk1.weather_risk, risk1.inflation_risk, risk1.currency_risk, risk1.news_risk, risk1.total_risk_score],
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    borderColor: '#0d6efd',
                    borderWidth: 2
                },
                {
                    label: name2,
                    data: [risk2.weather_risk, risk2.inflation_risk, risk2.currency_risk, risk2.news_risk, risk2.total_risk_score],
                    backgroundColor: 'rgba(220, 53, 69, 0.2)',
                    borderColor: '#dc3545',
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
}
</script>
</body>
</html>