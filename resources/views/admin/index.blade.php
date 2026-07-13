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
        <a class="navbar-brand" href="/">⚙️ Admin Dashboard</a>
        <div>
            <a href="/" class="btn btn-outline-light btn-sm me-2">🌍 View Site</a>
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
        <div class="col-md-3">
            <div class="card border-primary h-100">
                <div class="card-body text-center">
                    <i class="bi bi-globe display-4 text-primary"></i>
                    <h3 class="mt-3">{{ $stats['countries'] }}</h3>
                    <p class="text-muted mb-0">Countries</p>
                    <a href="/country/1" class="btn btn-sm btn-outline-primary mt-2">View</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-success h-100">
                <div class="card-body text-center">
                    <i class="bi bi-newspaper display-4 text-success"></i>
                    <h3 class="mt-3">{{ $stats['news'] }}</h3>
                    <p class="text-muted mb-0">News Articles</p>
                    <a href="{{ route('admin.news') }}" class="btn btn-sm btn-outline-success mt-2">Manage</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-warning h-100">
                <div class="card-body text-center">
                    <i class="bi bi-anchor display-4 text-warning"></i>
                    <h3 class="mt-3">{{ $stats['ports'] }}</h3>
                    <p class="text-muted mb-0">Ports</p>
                    <a href="{{ route('admin.ports') }}" class="btn btn-sm btn-outline-warning mt-2">Manage</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-info h-100">
                <div class="card-body text-center">
                    <i class="bi bi-star display-4 text-info"></i>
                    <h3 class="mt-3">{{ $stats['watchlists'] }}</h3>
                    <p class="text-muted mb-0">Watchlists</p>
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