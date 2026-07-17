<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Events - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
            margin-left: 10px;
            border-left: 2px solid #dee2e6;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 25px;
        }
        .timeline-marker {
            position: absolute;
            left: -39px;
            top: 2px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background-color: #3b82f6;
            border: 3px solid #fff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
        }
        .timeline-item.delivered .timeline-marker {
            background-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
        }
        .timeline-item.delayed .timeline-marker {
            background-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.25);
        }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="/admin">⚙️ Admin Dashboard</a>
        <a href="{{ route('admin.shipments.index') }}" class="btn btn-outline-light btn-sm">← Back to Shipments</a>
    </div>
</nav>

<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h2>📦 Tracking Events: <span class="text-primary">{{ $shipment->tracking_number }}</span></h2>
            <p class="text-muted">Origin: <strong>{{ $shipment->origin ?? '-' }}</strong> → Destination: <strong>{{ $shipment->destination ?? '-' }}</strong> | Status: <strong>{{ $shipment->status }}</strong></p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif

    <div class="row">
        <!-- Event Timeline -->
        <div class="col-md-7 mb-4">
            <div class="card shadow-sm p-4 h-100">
                <h4 class="mb-4"><i class="bi bi-clock-history"></i> Tracking Timeline</h4>
                
                @if($shipment->trackingEvents->count() > 0)
                    <div class="timeline mt-2">
                        @foreach($shipment->trackingEvents->sortByDesc('occurred_at') as $event)
                            @php
                                $statusClass = strtolower($event->status);
                            @endphp
                            <div class="timeline-item {{ $statusClass }}">
                                <div class="timeline-marker"></div>
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1 fw-bold">{{ $event->status }}</h6>
                                        <p class="mb-1 text-secondary" style="font-size: 0.95rem;">{{ $event->description }}</p>
                                        @if($event->port)
                                            <small class="text-muted d-block"><i class="bi bi-anchor"></i> Location: {{ $event->port->port_name }} ({{ $event->port->country_name }})</small>
                                        @endif
                                        <small class="text-muted" style="font-size: 0.8rem;"><i class="bi bi-calendar-event"></i> {{ \Carbon\Carbon::parse($event->occurred_at)->format('d M Y, H:i') }}</small>
                                    </div>
                                    <a href="{{ route('admin.shipments.events.delete', [$shipment->id, $event->id]) }}" class="btn btn-sm btn-link text-danger p-0" onclick="return confirm('Delete this event?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x display-4"></i>
                        <p class="mt-2 mb-0">No events recorded for this shipment yet.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Add Event Form -->
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm p-4">
                <h4 class="mb-4"><i class="bi bi-plus-circle"></i> Add Tracking Event</h4>

                @if($errors->any())
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.shipments.events.store', $shipment->id) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="status" class="form-label">Event Status / Location Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="Pending">Pending</option>
                            <option value="In Transit">In Transit</option>
                            <option value="Arrived">Arrived</option>
                            <option value="Departed">Departed</option>
                            <option value="Customs Cleared">Customs Cleared</option>
                            <option value="Delayed">Delayed</option>
                            <option value="Delivered">Delivered</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="port_id" class="form-label">Port Location (Optional)</label>
                        <select class="form-select" id="port_id" name="port_id">
                            <option value="">-- Select Port (No Port) --</option>
                            @foreach($ports as $port)
                                <option value="{{ $port->id }}">{{ $port->port_name }} ({{ $port->country_name }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="occurred_at" class="form-label">Timestamp (Occurred At)</label>
                        <input type="datetime-local" class="form-control" id="occurred_at" name="occurred_at" required value="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description / Remarks</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="e.g., Vessel arrived at Port of Jakarta, cargo unloading in progress." required></textarea>
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="update_shipment_status" name="update_shipment_status" value="1" checked>
                        <label class="form-check-label text-secondary" for="update_shipment_status" style="font-size: 0.9rem;">
                            Update main shipment status to this status
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Add Event</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
