<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Supply Chain Risk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="/admin">⚙️ Admin Dashboard</a>
        <div>
            <a href="/" class="btn btn-outline-light btn-sm me-2">🌍 View Site</a>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">Logout</button>
            </form>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-3">Dashboard Overview</h2>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-2 col-sm-6">
            <div class="card border-primary h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-globe display-5 text-primary"></i>
                    <h3 class="mt-3">{{ $stats['countries'] }}</h3>
                    <p class="text-muted mb-0" style="font-size:0.9rem;">Countries</p>
                    <a href="/country/1" class="btn btn-sm btn-outline-primary mt-2">View</a>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-sm-6">
            <div class="card border-success h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-newspaper display-5 text-success"></i>
                    <h3 class="mt-3">{{ $stats['news'] }}</h3>
                    <p class="text-muted mb-0" style="font-size:0.9rem;">News</p>
                    <a href="{{ route('admin.news') }}" class="btn btn-sm btn-outline-success mt-2">Manage</a>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-sm-6">
            <div class="card border-warning h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-anchor display-5 text-warning"></i>
                    <h3 class="mt-3">{{ $stats['ports'] }}</h3>
                    <p class="text-muted mb-0" style="font-size:0.9rem;">Ports</p>
                    <a href="{{ route('admin.ports') }}" class="btn btn-sm btn-outline-warning mt-2">Manage</a>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-sm-6">
            <div class="card border-danger h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-box display-5 text-danger"></i>
                    <h3 class="mt-3">{{ $stats['shipments'] }}</h3>
                    <p class="text-muted mb-0" style="font-size:0.9rem;">Shipments</p>
                    <a href="{{ route('admin.shipments.index') }}" class="btn btn-sm btn-outline-danger mt-2">Manage</a>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-sm-6">
            <div class="card border-info h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-star display-5 text-info"></i>
                    <h3 class="mt-3">{{ $stats['watchlists'] }}</h3>
                    <p class="text-muted mb-0" style="font-size:0.9rem;">Watchlists</p>
                    <a href="/watchlist" class="btn btn-sm btn-outline-info mt-2">View</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">📰 Quick Actions - News</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.news.create') }}" class="btn btn-success mb-2">
                        <i class="bi bi-plus-circle"></i> Add New News
                    </a>
                    <a href="{{ route('admin.news') }}" class="btn btn-primary mb-2">
                        <i class="bi bi-list"></i> Manage News
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">⚓ Quick Actions - Ports</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.ports.create') }}" class="btn btn-success mb-2">
                        <i class="bi bi-plus-circle"></i> Add New Port
                    </a>
                    <a href="{{ route('admin.ports') }}" class="btn btn-primary mb-2">
                        <i class="bi bi-list"></i> Manage Ports
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mt-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">📦 Shipment & Tracking Management</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Kelola data pengiriman (shipment) dan status tracking logistik</p>
                    <a href="{{ route('admin.shipments.index') }}" class="btn btn-primary me-2">
                        <i class="bi bi-box"></i> Manage Shipments
                    </a>
                    <a href="{{ route('admin.shipments.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Create Shipment
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mt-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">👥 User Management</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Kelola user sistem</p>
                    <a href="{{ route('admin.users') }}" class="btn btn-primary">
                        <i class="bi bi-people"></i> Manage Users
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>