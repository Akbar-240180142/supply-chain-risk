<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Watchlist - Supply Chain Risk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">🌍 Supply Chain Risk Intelligence</a>
        <span class="navbar-text text-white">⭐ My Watchlist</span>
    </div>
</nav>

<div class="container">
    <div class="card p-4">
        <h4 class="mb-3">⭐ Monitored Countries</h4>
        
        @if($watchlist->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Country</th>
                            <th>Risk Score</th>
                            <th>Risk Level</th>
                            <th>Currency</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($watchlist as $item)
                            @php
                                $country = $item->country;
                                $risk = $country->riskScores->first();
                                $riskScore = $risk ? $risk->total_risk_score : 0;
                                $riskLevel = $risk ? $risk->risk_level : 'Low';
                                
                                $badgeClass = 'bg-success';
                                if ($riskLevel === 'Medium') $badgeClass = 'bg-warning text-dark';
                                if ($riskLevel === 'High') $badgeClass = 'bg-danger';
                            @endphp
                            <tr>
                                <td><strong>{{ $country->name }}</strong></td>
                                <td>{{ $riskScore }}</td>
                                <td><span class="badge {{ $badgeClass }}">{{ $riskLevel }}</span></td>
                                <td>{{ $country->currency_code ?? 'N/A' }}</td>
                                <td>
                                    <form action="{{ route('watchlist.remove', $country->id) }}" method="POST" onsubmit="return confirm('Remove {{ $country->name }} from watchlist?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">🗑️ Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <h5 class="text-muted">No countries in your watchlist</h5>
                <a href="/" class="btn btn-primary">Go to Dashboard</a>
            </div>
        @endif
        
        <div class="mt-3">
            <a href="/" class="btn btn-secondary">← Back to Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>