<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Intelligence - Supply Chain Risk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; margin-bottom: 20px; }
        .sentiment-positive { border-left: 4px solid #198754; }
        .sentiment-negative { border-left: 4px solid #dc3545; }
        .sentiment-neutral { border-left: 4px solid #6c757d; }
        .badge-positive { background-color: #198754; }
        .badge-negative { background-color: #dc3545; }
        .badge-neutral { background-color: #6c757d; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">🌍 Supply Chain Risk Intelligence</a>
        <span class="navbar-text text-white">📰 News Intelligence</span>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Filter & Stats -->
        <div class="col-md-3">
            <div class="card p-3">
                <h5 class="card-title">📊 Sentiment Overview</h5>
                <canvas id="sentimentChart"></canvas>
                <hr>
                <div class="mb-3">
                    <label class="form-label">Filter by Sentiment:</label>
                    <select id="sentimentFilter" class="form-select" onchange="filterNews()">
                        <option value="all">All News</option>
                        <option value="Positive">Positive</option>
                        <option value="Negative">Negative</option>
                        <option value="Neutral">Neutral</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Filter by Country:</label>
                    <select id="countryFilter" class="form-select" onchange="filterNews()">
                        <option value="all">All Countries</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- News List -->
        <div class="col-md-9">
            <div class="card p-3">
                <h5 class="card-title mb-3">📰 Latest News</h5>
                <div id="newsContainer">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let allNews = [];
let allCountries = [];

// Load data on page load
fetch('/api/news')
    .then(response => response.json())
    .then(data => {
        allNews = data;
        extractCountries();
        displayNews(allNews);
        initSentimentChart();
    });

function extractCountries() {
    const countries = [...new Set(allNews.map(news => news.country.name))];
    allCountries = countries;
    const select = document.getElementById('countryFilter');
    countries.forEach(country => {
        select.add(new Option(country, country));
    });
}

function initSentimentChart() {
    const positive = allNews.filter(n => n.sentiment === 'Positive').length;
    const negative = allNews.filter(n => n.sentiment === 'Negative').length;
    const neutral = allNews.filter(n => n.sentiment === 'Neutral').length;

    const ctx = document.getElementById('sentimentChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Positive', 'Negative', 'Neutral'],
            datasets: [{
                data: [positive, negative, neutral],
                backgroundColor: ['#198754', '#dc3545', '#6c757d']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

function filterNews() {
    const sentimentFilter = document.getElementById('sentimentFilter').value;
    const countryFilter = document.getElementById('countryFilter').value;

    let filtered = allNews;

    if (sentimentFilter !== 'all') {
        filtered = filtered.filter(n => n.sentiment === sentimentFilter);
    }

    if (countryFilter !== 'all') {
        filtered = filtered.filter(n => n.country.name === countryFilter);
    }

    displayNews(filtered);
}

function displayNews(news) {
    const container = document.getElementById('newsContainer');
    
    if (news.length === 0) {
        container.innerHTML = '<div class="text-center py-5 text-muted">No news found</div>';
        return;
    }

    let html = '';
    news.forEach(item => {
        let sentimentClass = 'sentiment-neutral';
        let badgeClass = 'badge-neutral';
        
        if (item.sentiment === 'Positive') {
            sentimentClass = 'sentiment-positive';
            badgeClass = 'badge-positive';
        } else if (item.sentiment === 'Negative') {
            sentimentClass = 'sentiment-negative';
            badgeClass = 'badge-negative';
        }

        const date = new Date(item.published_at).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });

        html += `
            <div class="card p-3 mb-3 ${sentimentClass}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title mb-0">${item.title}</h6>
                    <span class="badge ${badgeClass}">${item.sentiment}</span>
                </div>
                <p class="text-muted mb-2">${item.description || 'No description available'}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        🌍 ${item.country.name} | 📅 ${date} | 📰 ${item.source}
                    </small>
                    <small class="text-muted">
                        Score: ${item.sentiment_score}
                    </small>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}
</script>
</body>
</html>