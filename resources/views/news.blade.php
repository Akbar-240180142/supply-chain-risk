<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>News Intelligence - Supply Chain Risk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; margin-bottom: 20px; }
        .sentiment-positive { border-left: 4px solid #198754; }
        .sentiment-negative { border-left: 4px solid #dc3545; }
        .sentiment-neutral  { border-left: 4px solid #6c757d; }
        .badge-positive { background-color: #198754; }
        .badge-negative { background-color: #dc3545; }
        .badge-neutral  { background-color: #6c757d; }
        #syncBtn { min-width: 120px; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">🌍 Supply Chain Risk Intelligence</a>
        <span class="navbar-text text-white">📰 News Intelligence</span>
    </div>
</nav>

@if(!isset($isRealData) || !$isRealData)
<div class="alert alert-warning alert-dismissible fade show mb-0 rounded-0" role="alert" style="border-radius:0!important;">
    <strong>⚠️ Data Berita Saat Ini Menggunakan Data Seeder.</strong>
    Untuk menampilkan berita nyata, isi <code>GNEWS_API_KEY</code> di file <code>.env</code> dengan key dari
    <a href="https://gnews.io" target="_blank">gnews.io</a> (gratis), lalu klik tombol <strong>Sync Berita</strong>.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@else
<div class="alert alert-success alert-dismissible fade show mb-0 rounded-0" role="alert" style="border-radius:0!important;">
    <strong>✅ Menampilkan berita real dari GNews API.</strong>
    Data diperbarui secara otomatis. Klik <strong>Sync Berita</strong> untuk update terbaru.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

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
                        <option value="Global">Global</option>
                        @foreach($countries as $c)
                            <option value="{{ $c->name }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                @if(isset($hasRealKey) && $hasRealKey)
                <hr>
                <button id="syncBtn" class="btn btn-primary btn-sm w-100" onclick="syncNews()">
                    🔄 Sync Berita
                </button>
                <div id="syncStatus" class="mt-2 small text-muted"></div>
                @endif
            </div>
        </div>

        <!-- News List -->
        <div class="col-md-9">
            <div class="card p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">📰 Latest News</h5>
                    <span id="newsCount" class="badge bg-secondary">Memuat...</span>
                </div>
                <div id="newsContainer">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Memuat berita...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let allNews = [];
let sentimentChartInstance = null;

// Load data on page load
loadNews();

function loadNews() {
    document.getElementById('newsContainer').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-2">Memuat berita...</p>
        </div>`;
    document.getElementById('newsCount').textContent = 'Memuat...';

    fetch('/api/news')
        .then(response => {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
        })
        .then(data => {
            allNews = Array.isArray(data) ? data : [];
            displayNews(allNews);
            initSentimentChart();
            document.getElementById('newsCount').textContent = allNews.length + ' berita';
        })
        .catch(err => {
            console.error('Gagal memuat berita:', err);
            document.getElementById('newsContainer').innerHTML = `
                <div class="text-center py-5 text-danger">
                    <p>❌ Gagal memuat berita. <button class="btn btn-sm btn-outline-danger" onclick="loadNews()">Coba Lagi</button></p>
                    <small class="text-muted">${err.message}</small>
                </div>`;
            document.getElementById('newsCount').textContent = 'Error';
        });
}

function initSentimentChart() {
    const positive = allNews.filter(n => n.sentiment === 'Positive').length;
    const negative = allNews.filter(n => n.sentiment === 'Negative').length;
    const neutral  = allNews.filter(n => n.sentiment === 'Neutral').length;

    const ctx = document.getElementById('sentimentChart').getContext('2d');

    if (sentimentChartInstance) {
        sentimentChartInstance.destroy();
    }

    sentimentChartInstance = new Chart(ctx, {
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
    const countryFilter   = document.getElementById('countryFilter').value;

    let filtered = allNews;

    if (sentimentFilter !== 'all') {
        filtered = filtered.filter(n => n.sentiment === sentimentFilter);
    }

    if (countryFilter !== 'all') {
        filtered = filtered.filter(n => (n.country?.name || 'Global') === countryFilter);
    }

    displayNews(filtered);
}

function displayNews(news) {
    const container = document.getElementById('newsContainer');

    if (!news || news.length === 0) {
        container.innerHTML = '<div class="text-center py-5 text-muted">Tidak ada berita ditemukan</div>';
        return;
    }

    let html = '';
    news.forEach(item => {
        const sentiment = item.sentiment || 'Neutral';
        let sentimentClass = 'sentiment-neutral';
        let badgeClass = 'badge-neutral';

        if (sentiment === 'Positive') {
            sentimentClass = 'sentiment-positive';
            badgeClass = 'badge-positive';
        } else if (sentiment === 'Negative') {
            sentimentClass = 'sentiment-negative';
            badgeClass = 'badge-negative';
        }

        const countryName = item.country?.name || 'Global';
        const score = item.sentiment_score != null ? Number(item.sentiment_score).toFixed(2) : '0.00';

        let dateStr = '-';
        if (item.published_at) {
            try {
                dateStr = new Date(item.published_at).toLocaleDateString('id-ID', {
                    year: 'numeric', month: 'short', day: 'numeric'
                });
            } catch(e) { dateStr = item.published_at; }
        }

        const articleUrl = item.url && item.url.startsWith('http') ? item.url : null;

        html += `
            <div class="card p-3 mb-3 ${sentimentClass}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    ${articleUrl
                        ? `<h6 class="card-title mb-0"><a href="${articleUrl}" target="_blank" rel="noopener noreferrer" style="text-decoration:none; color:inherit;">${item.title}</a></h6>`
                        : `<h6 class="card-title mb-0">${item.title}</h6>`
                    }
                    <span class="badge ${badgeClass} ms-2 flex-shrink-0">${sentiment}</span>
                </div>
                <p class="text-muted mb-2">${item.description || 'Tidak ada deskripsi.'}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        🌍 ${countryName} | 📅 ${dateStr} | 📰 ${item.source || 'Unknown'}
                    </small>
                    <small class="text-muted">Score: ${score}</small>
                </div>
                ${articleUrl ? `<div class="mt-2"><a href="${articleUrl}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary">Baca Artikel →</a></div>` : ''}
            </div>
        `;
    });

    container.innerHTML = html;
}

function syncNews() {
    const btn = document.getElementById('syncBtn');
    const status = document.getElementById('syncStatus');

    btn.disabled = true;
    btn.textContent = '⏳ Syncing...';
    status.textContent = 'Mengambil berita terbaru dari GNews API...';
    status.className = 'mt-2 small text-info';

    fetch('/api/news/sync', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            status.textContent = `✅ Berhasil sync ${data.synced} artikel baru!`;
            status.className = 'mt-2 small text-success';
            // Reload news setelah sync
            setTimeout(() => loadNews(), 1000);
        } else {
            status.textContent = '❌ ' + (data.message || 'Sync gagal');
            status.className = 'mt-2 small text-danger';
        }
    })
    .catch(err => {
        status.textContent = '❌ Error: ' + err.message;
        status.className = 'mt-2 small text-danger';
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = '🔄 Sync Berita';
    });
}
</script>
</body>
</html>