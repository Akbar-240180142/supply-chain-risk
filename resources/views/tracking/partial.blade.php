<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h4 class="mb-0">
            <i class="bi bi-box"></i> Resi: {{ $shipment->tracking_number }}
        </h4>
        <span class="badge bg-{{ $shipment->status == 'Delayed' ? 'danger' : ($shipment->status == 'Delivered' ? 'success' : 'primary') }} fs-6">
            {{ $shipment->status }}
        </span>
    </div>
</div>

<div class="row">
    <!-- Kolom Timeline -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Perjalanan</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($shipment->trackingEvents as $event)
                        <div class="timeline-item d-flex mb-3">
                            <div class="timeline-icon bg-light rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width:35px; height:35px;">
                                <i class="bi bi-geo-alt text-primary"></i>
                            </div>
                            <div class="timeline-content border-bottom pb-2 w-100">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1 fw-bold fs-6">{{ $event->status }}</h6>
                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $event->occurred_at->format('d M, H:i') }}</small>
                                </div>
                                <p class="mb-1 text-muted small">{{ $event->description }}</p>
                                @if($event->port)
                                    <p class="mb-0 text-dark small">
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
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-danger border-2">
            <div class="card-header bg-danger text-white py-2">
                <h6 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Risiko Lokasi Saat Ini</h6>
            </div>
            <div class="card-body bg-light p-3">
                @php
                    $latestEvent = $shipment->trackingEvents->first();
                    $currentPort = $latestEvent ? $latestEvent->port : null;
                    $country = $currentPort ? $currentPort->country : null;
                @endphp

                @if(!$country)
                    <div class="alert alert-info py-2 small mb-0">Lokasi saat ini tidak diketahui.</div>
                @else
                    <p class="small mb-3">Lokasi: <strong>{{ $currentPort->name }}</strong>, <strong>{{ $country->name }}</strong>.</p>
                    
                    <!-- Info Cuaca -->
                    <h6 class="fw-bold mt-3 border-bottom pb-1 fs-6"><i class="bi bi-cloud-sun"></i> Cuaca</h6>
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
                        <div class="d-flex align-items-center mb-2">
                            <span class="fs-2 me-2">{{ $icon }}</span>
                            <div>
                                <div class="fw-bold">{{ $weather->temperature }}°C</div>
                                <div class="text-muted small">{{ $desc }} (Curah Hujan: {{ $weather->rain }} mm)</div>
                            </div>
                        </div>
                        @if($weather->is_storm || $weather->rain > 5)
                            <div class="text-danger small fw-bold">
                                <i class="bi bi-exclamation-circle"></i> Peringatan Cuaca Buruk!
                            </div>
                        @else
                            <div class="text-success small"><i class="bi bi-check-circle"></i> Cuaca normal.</div>
                        @endif
                    @else
                        <p class="text-muted small">Data cuaca tidak tersedia.</p>
                    @endif

                    <!-- Info Berita/Risiko -->
                    <h6 class="fw-bold mt-3 border-bottom pb-1 fs-6"><i class="bi bi-newspaper"></i> Berita</h6>
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
                                        <div class="fw-bold text-dark" style="font-size: 0.8rem;">{{ \Illuminate\Support\Str::limit($news->title, 50) }}</div>
                                        <small class="text-muted" style="font-size: 0.7rem;">{{ $news->source ?? 'Unknown' }} · Sentimen: <span class="text-{{ $news->sentiment == 'Negative' ? 'danger' : ($news->sentiment == 'Positive' ? 'success' : 'secondary') }}">{{ ucfirst($news->sentiment) }}</span></small>
                                    </a>
                                </li>
                            @endforeach
                            </ul>
                        @else
                            <p class="text-muted small mb-0"><i class="bi bi-info-circle"></i> Belum ada berita terkait untuk lokasi ini.</p>
                        @endif
                    @else
                        <p class="text-success small mb-0"><i class="bi bi-check-circle"></i> Tidak ada berita negatif.</p>
                    @endif

                    <!-- Skor Risiko Total -->
                    @if($country->riskScores->count() > 0)
                        @php $risk = $country->riskScores->sortByDesc('record_date')->first(); @endphp
                        <h6 class="fw-bold mt-3 border-bottom pb-1 fs-6"><i class="bi bi-graph-up-arrow"></i> Skor Risiko</h6>
                        <div class="progress mb-1" style="height: 15px;">
                            <div class="progress-bar {{ $risk->total_risk_score > 60 ? 'bg-danger' : ($risk->total_risk_score > 30 ? 'bg-warning' : 'bg-success') }}" 
                                 role="progressbar" style="width: {{ $risk->total_risk_score }}%;" 
                                 aria-valuenow="{{ $risk->total_risk_score }}" aria-valuemin="0" aria-valuemax="100">
                                {{ $risk->total_risk_score }}/100
                            </div>
                        </div>
                    @endif

                @endif
            </div>
        </div>
    </div>
</div>
