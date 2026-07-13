<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Country Comparison - Supply Chain Risk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f8f9fa; }
        .country-card { 
            border-left: 5px solid #0d6efd; 
            transition: all 0.3s;
            min-height: 400px;
        }
        .country-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .stat-box { 
            background: #f8f9fa; 
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 15px;
        }
        .stat-label { font-size: 0.85rem; color: #6c757d; text-transform: uppercase; margin-bottom: 5px; }
        .stat-value { font-size: 1.25rem; font-weight: bold; color: #0d6efd; }
        .compare-btn { padding: 12px 40px; font-size: 1.1rem; }
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
    <h2 class="mb-4">🔀 Country Comparison Engine</h2>
    
    <div class="card p-4 mb-4">
        <form id="comparisonForm" class="row align-items-end">
            <div class="col-md-5">
                <label class="form-label">Country 1:</label>
                <select name="country1" id="country1" class="form-select form-select-lg" required>
                    <option value="">-- Select Country --</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2 text-center">
                <button type="submit" class="btn btn-primary compare-btn">
                    ⚡ Compare
                </button>
            </div>
            
            <div class="col-md-5">
                <label class="form-label">Country 2:</label>
                <select name="country2" id="country2" class="form-select form-select-lg" required>
                    <option value="">-- Select Country --</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <div id="comparisonResults" style="display: none;">
        <div class="row">
            <div class="col-md-6">
                <div class="country-card card p-4" id="card1">
                    <h3 id="name1" class="mb-3">--</h3>
                    <div class="mb-3">
                        <div class="stat-label">Risk Score</div>
                        <div class="stat-value" id="risk1">--</div>
                        <span class="badge mt-2" id="riskBadge1">--</span>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="stat-box">
                                <div class="stat-label">GDP</div>
                                <div class="stat-value" id="gdp1">--</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box">
                                <div class="stat-label">Inflation</div>
                                <div class="stat-value" id="inflation1">--</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="stat-box">
                                <div class="stat-label">Population</div>
                                <div class="stat-value" id="population1">--</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box">
                                <div class="stat-label">Currency</div>
                                <div class="stat-value" id="currency1">--</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="stat-box">
                                <div class="stat-label">Exchange Rate (vs USD)</div>
                                <div class="stat-value" id="exchange1">--</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box">
                                <div class="stat-label">Weather</div>
                                <div class="stat-value" id="weather1">N/A</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="country-card card p-4" id="card2" style="border-left-color: #dc3545;">
                    <h3 id="name2" class="mb-3">--</h3>
                    <div class="mb-3">
                        <div class="stat-label">Risk Score</div>
                        <div class="stat-value" id="risk2">--</div>
                        <span class="badge mt-2" id="riskBadge2">--</span>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="stat-box">
                                <div class="stat-label">GDP</div>
                                <div class="stat-value" id="gdp2">--</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box">
                                <div class="stat-label">Inflation</div>
                                <div class="stat-value" id="inflation2">--</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="stat-box">
                                <div class="stat-label">Population</div>
                                <div class="stat-value" id="population2">--</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box">
                                <div class="stat-label">Currency</div>
                                <div class="stat-value" id="currency2">--</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="stat-box">
                                <div class="stat-label">Exchange Rate (vs USD)</div>
                                <div class="stat-value" id="exchange2">--</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box">
                                <div class="stat-label">Weather</div>
                                <div class="stat-value" id="weather2">N/A</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card p-4">
                    <h5 class="card-title mb-3">📊 Risk Comparison</h5>
                    <canvas id="riskChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-4">
                    <h5 class="card-title mb-3">💰 Economic Comparison</h5>
                    <canvas id="economicChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let riskChartInstance = null;
let economicChartInstance = null;

document.getElementById('comparisonForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const country1 = document.getElementById('country1').value;
    const country2 = document.getElementById('country2').value;
    
    if (!country1 || !country2 || country1 === country2) {
        alert('Please select two different countries!');
        return;
    }
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const response = await fetch('/api/compare', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ country1, country2 })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.error) {
            throw new Error(data.message || data.error);
        }
        
        displayComparison(data);
        
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to fetch comparison data: ' + error.message);
    }
});

function displayComparison(data) {
    document.getElementById('comparisonResults').style.display = 'block';
    
    const c1 = data.country1;
    const c2 = data.country2;
    
    // Update Country 1
    document.getElementById('name1').textContent = c1.name;
    document.getElementById('risk1').textContent = parseFloat(c1.risk_score).toFixed(2);
    document.getElementById('riskBadge1').textContent = c1.risk_level;
    document.getElementById('riskBadge1').className = 'badge mt-2 ' + getRiskBadgeClass(c1.risk_level);
    document.getElementById('gdp1').textContent = '$' + (parseFloat(c1.gdp) / 1000000000).toFixed(1) + 'B';
    document.getElementById('inflation1').textContent = parseFloat(c1.inflation).toFixed(2) + '%';
    document.getElementById('population1').textContent = formatNumber(parseInt(c1.population));
    document.getElementById('currency1').textContent = c1.currency;
    document.getElementById('exchange1').textContent = '1 USD = ' + parseFloat(c1.exchange_rate).toFixed(2) + ' ' + c1.currency;
    document.getElementById('weather1').textContent = c1.weather || 'N/A';
    
    // Update Country 2
    document.getElementById('name2').textContent = c2.name;
    document.getElementById('risk2').textContent = parseFloat(c2.risk_score).toFixed(2);
    document.getElementById('riskBadge2').textContent = c2.risk_level;
    document.getElementById('riskBadge2').className = 'badge mt-2 ' + getRiskBadgeClass(c2.risk_level);
    document.getElementById('gdp2').textContent = '$' + (parseFloat(c2.gdp) / 1000000000).toFixed(1) + 'B';
    document.getElementById('inflation2').textContent = parseFloat(c2.inflation).toFixed(2) + '%';
    document.getElementById('population2').textContent = formatNumber(parseInt(c2.population));
    document.getElementById('currency2').textContent = c2.currency;
    document.getElementById('exchange2').textContent = '1 USD = ' + parseFloat(c2.exchange_rate).toFixed(2) + ' ' + c2.currency;
    document.getElementById('weather2').textContent = c2.weather || 'N/A';
    
    updateCharts(c1, c2);
    document.getElementById('comparisonResults').scrollIntoView({ behavior: 'smooth' });
}

function getRiskBadgeClass(level) {
    const classes = {
        'Low': 'bg-success',
        'Medium': 'bg-warning text-dark',
        'High': 'bg-danger',
        'Critical': 'bg-dark'
    };
    return classes[level] || 'bg-secondary';
}

function formatNumber(num) {
    if (num >= 1000000000) {
        return (num / 1000000000).toFixed(2) + 'B';
    } else if (num >= 1000000) {
        return (num / 1000000).toFixed(2) + 'M';
    }
    return num.toLocaleString();
}

function updateCharts(c1, c2) {
    if (riskChartInstance) riskChartInstance.destroy();
    if (economicChartInstance) economicChartInstance.destroy();
    
    const riskCtx = document.getElementById('riskChart').getContext('2d');
    riskChartInstance = new Chart(riskCtx, {
        type: 'bar',
        data: {
            labels: ['Risk Score'],
            datasets: [
                {
                    label: c1.name,
                    data: [parseFloat(c1.risk_score)],
                    backgroundColor: 'rgba(13, 110, 253, 0.7)'
                },
                {
                    label: c2.name,
                    data: [parseFloat(c2.risk_score)],
                    backgroundColor: 'rgba(220, 53, 69, 0.7)'
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, max: 100 }
            }
        }
    });
    
    const economicCtx = document.getElementById('economicChart').getContext('2d');
    economicChartInstance = new Chart(economicCtx, {
        type: 'radar',
        data: {
            labels: ['GDP (B)', 'Inflation (%)', 'Risk Score'],
            datasets: [
                {
                    label: c1.name,
                    data: [
                        parseFloat(c1.gdp) / 1000000000,
                        parseFloat(c1.inflation),
                        parseFloat(c1.risk_score)
                    ],
                    borderColor: 'rgba(13, 110, 253, 0.7)',
                    backgroundColor: 'rgba(13, 110, 253, 0.2)'
                },
                {
                    label: c2.name,
                    data: [
                        parseFloat(c2.gdp) / 1000000000,
                        parseFloat(c2.inflation),
                        parseFloat(c2.risk_score)
                    ],
                    borderColor: 'rgba(220, 53, 69, 0.7)',
                    backgroundColor: 'rgba(220, 53, 69, 0.2)'
                }
            ]
        },
        options: {
            responsive: true
        }
    });
}
</script>
</body>
</html>