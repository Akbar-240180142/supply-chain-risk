<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipment Management - Admin</title>
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📦 Shipment Management</h2>
        <a href="{{ route('admin.shipments.create') }}" class="btn btn-primary">+ Create Shipment</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Tracking Number</th>
                            <th>Origin</th>
                            <th>Destination</th>
                            <th>Sender</th>
                            <th>Recipient</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments as $shipment)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">{{ $shipment->tracking_number }}</td>
                                <td>{{ $shipment->origin ?? '-' }}</td>
                                <td>{{ $shipment->destination ?? '-' }}</td>
                                <td>{{ $shipment->sender_name ?? '-' }}</td>
                                <td>{{ $shipment->recipient_name ?? '-' }}</td>
                                <td>
                                    @php
                                        $badgeColor = match($shipment->status) {
                                            'Delivered' => 'success',
                                            'In Transit' => 'primary',
                                            'Delayed' => 'warning',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badgeColor }}">{{ $shipment->status }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.shipments.events', $shipment->id) }}" class="btn btn-sm btn-outline-info me-1">
                                        <i class="bi bi-clock-history"></i> Events
                                    </a>
                                    <a href="{{ route('admin.shipments.edit', $shipment->id) }}" class="btn btn-sm btn-warning me-1">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="{{ route('admin.shipments.delete', $shipment->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this shipment?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No shipments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $shipments->links() }}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
