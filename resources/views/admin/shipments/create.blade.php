<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Shipment - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="/admin">⚙️ Admin Dashboard</a>
        <a href="{{ route('admin.shipments.index') }}" class="btn btn-outline-light btn-sm">← Back to Shipments</a>
    </div>
</nav>

<div class="container" style="max-width: 800px;">
    <div class="card shadow-sm p-4">
        <h2 class="mb-4">📦 Create New Shipment</h2>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.shipments.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="tracking_number" class="form-label">Tracking Number</label>
                <input type="text" class="form-control" id="tracking_number" name="tracking_number" required value="{{ old('tracking_number', 'TRK-' . strtoupper(uniqid())) }}">
                <div class="form-text">Unique tracking identifier. Autofilled with a random value.</div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="sender_name" class="form-label">Sender Name</label>
                    <input type="text" class="form-control" id="sender_name" name="sender_name" value="{{ old('sender_name') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="recipient_name" class="form-label">Recipient Name</label>
                    <input type="text" class="form-control" id="recipient_name" name="recipient_name" value="{{ old('recipient_name') }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="origin" class="form-label">Origin (City / Country)</label>
                    <input type="text" class="form-control" id="origin" name="origin" value="{{ old('origin') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="destination" class="form-label">Destination (City / Country)</label>
                    <input type="text" class="form-control" id="destination" name="destination" value="{{ old('destination') }}">
                </div>
            </div>

            <div class="mb-4">
                <label for="status" class="form-label">Current Shipment Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="Pending" {{ old('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="In Transit" {{ old('status') === 'In Transit' ? 'selected' : '' }}>In Transit</option>
                    <option value="Delayed" {{ old('status') === 'Delayed' ? 'selected' : '' }}>Delayed</option>
                    <option value="Delivered" {{ old('status') === 'Delivered' ? 'selected' : '' }}>Delivered</option>
                </select>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.shipments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Shipment</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
