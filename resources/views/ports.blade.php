<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Port Locations - Supply Chain Risk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; margin-bottom: 20px; }
        #portMap { height: 600px; width: 100%; border-radius: 8px; }
        .port-marker {
            transition: transform 0.2s;
        }
        .port-marker:hover {
            transform: scale(1.3);
        }
        .port-active { background-color: #198754; }
        .port-inactive { background-color: #dc3545; }
        .port-maintenance { background-color: #ffc107; }
        .stat-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #0d6efd;
        }
        .stat-label {
            font-size: 0.85rem;
            color: #6c757d;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">🚢 Port Locations Dashboard</span>
        <div>
            <a href="/" class="btn btn-outline-light">← Back to Dashboard</a>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="stat-box">
                    <div class="stat-number" id="totalPorts">0</div>
                    <div class="stat-label">Total Ports</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="stat-box">
                    <div class="stat-number text-success" id="activePorts">0</div>
                    <div class="stat-label">Active Ports</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="stat-box">
                    <div class="stat-number text-danger" id="inactivePorts">0</div>
                    <div class="stat-label">Inactive Ports</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="stat-box">
                    <div class="stat-number text-warning" id="maintenancePorts">0</div>
                    <div class="stat-label">Under Maintenance</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card p-4 mb-4">
        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label">🔍 Search Port</label>
                <input type="text" id="searchPort" class="form-control" placeholder="Type port name...">
            </div>
            <div class="col-md-5">
                <label class="form-label">🌍 Filter by Country</label>
                <select id="filterCountry" class="form-select">
                    <option value="">All Countries</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">🚦 Status</label>
                <select id="filterStatus" class="form-select">
                    <option value="">All Status</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                    <option value="Maintenance">Maintenance</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Map -->
        <div class="col-md-8">
            <div class="card p-3">
                <h5 class="card-title mb-3">🗺️ Global Port Map</h5>
                <div id="portMap"></div>
            </div>
        </div>

        <!-- Port List -->
        <div class="col-md-4">
            <div class="card p-3">
                <h5 class="card-title mb-3">📋 Port List</h5>
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-hover table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>Port</th>
                                <th>Country</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="portTableBody">
                            <tr><td colspan="3" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let portMap;
let portMarkers = [];
let allPortsData = [];

document.addEventListener('DOMContentLoaded', function() {
    // Init map
    portMap = L.map('portMap').setView([20, 100], 3);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(portMap);

    // Load ports data
    fetch('/api/ports')
        .then(response => response.json())
        .then(data => {
            allPortsData = data;
            updateStats(data);
            displayPortsOnMap(data);
            updatePortTable(data);
        })
        .catch(error => console.error('Error loading ports:', error));

    // Search & Filter
    document.getElementById('searchPort').addEventListener('input', filterPorts);
    document.getElementById('filterCountry').addEventListener('change', filterPorts);
    document.getElementById('filterStatus').addEventListener('change', filterPorts);
});

function filterPorts() {
    const searchTerm = document.getElementById('searchPort').value.toLowerCase();
    const countryFilter = document.getElementById('filterCountry').value;
    const statusFilter = document.getElementById('filterStatus').value;

    const filtered = allPortsData.filter(port => {
        const matchSearch = port.name.toLowerCase().includes(searchTerm);
        const matchCountry = !countryFilter || port.country_id == countryFilter;
        const matchStatus = !statusFilter || port.status === statusFilter;
        return matchSearch && matchCountry && matchStatus;
    });

    displayPortsOnMap(filtered);
    updatePortTable(filtered);
}

function updateStats(ports) {
    const total = ports.length;
    const active = ports.filter(p => p.status === 'Active').length;
    const inactive = ports.filter(p => p.status === 'Inactive').length;
    const maintenance = ports.filter(p => p.status === 'Maintenance').length;
    
    document.getElementById('totalPorts').textContent = total;
    document.getElementById('activePorts').textContent = active;
    document.getElementById('inactivePorts').textContent = inactive;
    document.getElementById('maintenancePorts').textContent = maintenance;
}

function displayPortsOnMap(ports) {
    // Clear existing markers
    portMarkers.forEach(marker => portMap.removeLayer(marker));
    portMarkers = [];

    ports.forEach(port => {
        let color = '#198754'; // green for active
        if (port.status === 'Inactive') {
            color = '#dc3545';
        } else if (port.status === 'Maintenance') {
            color = '#ffc107';
        }

        // FIX: Konversi latitude dan longitude ke float
        const lat = parseFloat(port.latitude);
        const lng = parseFloat(port.longitude);

        const iconHtml = `
            <div style="
                background-color: ${color};
                width: 25px;
                height: 25px;
                border-radius: 50%;
                border: 3px solid white;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            ">⚓</div>
        `;

        const customIcon = L.divIcon({
            html: iconHtml,
            className: 'port-marker',
            iconSize: [25, 25],
            iconAnchor: [12, 12]
        });

        // FIX: Gunakan lat dan lng yang sudah di-convert
        const marker = L.marker([lat, lng], { icon: customIcon })
            .addTo(portMap)
            .bindPopup(`
                <div style="min-width: 200px;">
                    <h6 style="margin: 0 0 10px 0; font-weight: bold;">⚓ ${port.name}</h6>
                    <div style="margin-bottom: 5px;"><strong>Code:</strong> ${port.code}</div>
                    <div style="margin-bottom: 5px;"><strong>Country:</strong> ${port.country}</div>
                    <div style="margin-bottom: 5px;"><strong>Status:</strong> 
                        <span class="badge bg-${port.status === 'Active' ? 'success' : (port.status === 'Inactive' ? 'danger' : 'warning')}">
                            ${port.status}
                        </span>
                    </div>
                    <div><strong>Coordinates:</strong><br>${lat.toFixed(4)}, ${lng.toFixed(4)}</div>
                </div>
            `);

        portMarkers.push(marker);
    });
}

function updatePortTable(ports) {
    const tbody = document.getElementById('portTableBody');
    tbody.innerHTML = '';

    if (!ports || ports.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No ports found</td></tr>';
        return;
    }

    console.log('Ports data:', ports); // Debug log

    ports.forEach(port => {
        // Pastikan data tidak null
        const portName = port.name || 'Unknown';
        const portCode = port.code || 'N/A';
        const countryName = port.country || 'Unknown';
        const portStatus = port.status || 'Active';
        
        const badgeClass = portStatus === 'Active' ? 'bg-success' : (portStatus === 'Inactive' ? 'bg-danger' : 'bg-warning text-dark');
        
        const row = `
            <tr>
                <td><strong>${portName}</strong><br><small class="text-muted">${portCode}</small></td>
                <td>${countryName}</td>
                <td><span class="badge ${badgeClass}">${portStatus}</span></td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
}
</script>
</body>
</html>