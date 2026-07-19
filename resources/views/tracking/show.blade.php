<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resi: {{ $shipment->tracking_number }} - Supply Chain Risk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="bi bi-globe-americas me-2"></i>Supply Chain Risk
        </a>
        <div>
            <a href="{{ route('tracking.index') }}" class="btn btn-outline-light btn-sm">
                <i class="bi bi-search"></i> Lacak Lainnya
            </a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="mb-0">
                <i class="bi bi-box"></i> Resi: {{ $shipment->tracking_number }}
            </h2>
            <span class="badge bg-{{ $shipment->status == 'Delayed' ? 'danger' : ($shipment->status == 'Delivered' ? 'success' : 'primary') }} fs-5">
                {{ $shipment->status }}
            </span>
        </div>
    </div>

    <div class="row">
        <!-- Kolom Timeline -->
        <div class="col-md-7 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Perjalanan</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($shipment->trackingEvents as $event)
                            <div class="timeline-item d-flex mb-4">
                                <div class="timeline-icon bg-light rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
                                    <i class="bi bi-geo-alt text-primary"></i>
                                </div>
                                <div class="timeline-content border-bottom pb-3 w-100">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1 fw-bold">{{ $event->status }}</h6>
                                        <small class="text-muted">{{ $event->occurred_at->format('d M Y, H:i') }}</small>
                                    </div>
                                    <p class="mb-1 text-muted">{{ $event->description }}</p>
                                    @if($event->port)
                                        <p class="mb-0 text-dark">
                                            <i class="bi bi-building"></i> {{ $event->port->name }}, {{ $event->port->country_name }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Analisis Risiko -->
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm h-100 border-danger border-2">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Analisis Risiko Lokasi Saat Ini</h5>
                </div>
                <div class="card-body bg-light">
                    @php
                        $latestEvent = $shipment->trackingEvents->first();
                        $currentPort = $latestEvent ? $latestEvent->port : null;
                        $country = $currentPort ? $currentPort->country : null;
                    @endphp

                    @if(!$country)
                        <div class="alert alert-info">Lokasi saat ini tidak diketahui atau sedang dalam transit panjang.</div>
                    @else
                        <p>Lokasi saat ini berada di pelabuhan <strong>{{ $currentPort->name }}</strong>, <strong>{{ $country->name }}</strong>.</p>
                        
                        <!-- Info Cuaca -->
                        <h6 class="fw-bold mt-4 border-bottom pb-2"><i class="bi bi-cloud-sun"></i> Kondisi Cuaca</h6>
                        @if($country->weather->count() > 0)
                            @php 
                                $weather = $country->weather->first(); 
                                $icon = '☀️';
                                $desc = 'Cerah / Berawan';
                                if ($weather->is_storm) {
                                    $icon = '⛈️';
                                    $desc = 'Badai';
                                } elseif ($weather->rain > 5) {
                                    $icon = '🌧️';
                                    $desc = 'Hujan Lebat';
                                } elseif ($weather->rain > 0) {
                                    $icon = '🌦️';
                                    $desc = 'Hujan Ringan';
                                }
                            @endphp
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-1 me-3">{{ $icon }}</span>
                                <div>
                                    <div class="fw-bold fs-5">{{ $weather->temperature }}°C</div>
                                    <div class="text-muted text-capitalize">{{ $desc }} (Curah Hujan: {{ $weather->rain }} mm)</div>
                                </div>
                            </div>
                            @if($weather->is_storm || $weather->rain > 5)
                                <div class="alert alert-warning py-2 mb-0">
                                    <i class="bi bi-exclamation-circle"></i> Peringatan Cuaca Buruk! Berpotensi menghambat logistik.
                                </div>
                            @else
                                <div class="text-success"><i class="bi bi-check-circle"></i> Cuaca normal, operasional aman.</div>
                            @endif
                        @else
                            <p class="text-muted small">Data cuaca tidak tersedia.</p>
                        @endif

                        <!-- Info Berita/Risiko -->
                        <h6 class="fw-bold mt-4 border-bottom pb-2"><i class="bi bi-newspaper"></i> Peringatan Berita</h6>
                        @if($country->news->count() > 0)
                            @php
                                $realNews = $country->news->filter(function($n) { return $n->url && !str_contains($n->url, 'example.com'); });
                                $displayNews = $realNews->sortByDesc('published_at')->take(2);
                            @endphp
                            @if($displayNews->count() > 0)
                                <ul class="list-unstyled mb-0">
                                @foreach($displayNews as $news)
                                    <li class="mb-2">
                                        <a href="{{ $news->url }}" target="_blank" class="text-decoration-none">
                                            <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ \Illuminate\Support\Str::limit($news->title, 60) }}</div>
                                            <small class="text-muted">{{ $news->source ?? 'Unknown' }} · Sentimen: <span class="text-{{ $news->sentiment == 'Negative' ? 'danger' : ($news->sentiment == 'Positive' ? 'success' : 'secondary') }}">{{ ucfirst($news->sentiment) }}</span></small>
                                        </a>
                                    </li>
                                @endforeach
                                </ul>
                            @else
                                <p class="text-muted small mb-0"><i class="bi bi-info-circle"></i> Belum ada berita terkait untuk lokasi ini.</p>
                            @endif
                        @else
                            <p class="text-success small mb-0"><i class="bi bi-check-circle"></i> Tidak ada berita negatif yang mengganggu.</p>
                        @endif

                        <!-- Skor Risiko Total -->
                        @if($country->riskScores->count() > 0)
                            @php $risk = $country->riskScores->sortByDesc('record_date')->first(); @endphp
                            <h6 class="fw-bold mt-4 border-bottom pb-2"><i class="bi bi-graph-up-arrow"></i> Skor Risiko Total ({{ $country->name }})</h6>
                            <div class="progress mb-2" style="height: 20px;">
                                <div class="progress-bar {{ $risk->total_risk_score > 60 ? 'bg-danger' : ($risk->total_risk_score > 30 ? 'bg-warning' : 'bg-success') }}" 
                                     role="progressbar" style="width: {{ $risk->total_risk_score }}%;" 
                                     aria-valuenow="{{ $risk->total_risk_score }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $risk->total_risk_score }}/100
                                </div>
                            </div>
                            <small class="text-muted">Semakin tinggi skor, semakin tinggi kemungkinan paket tertunda (delay).</small>
                        @endif

                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-3">
        <a href="{{ route('tracking.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Pencarian
        </a>
    </div>
</div>

</body>
</html>
