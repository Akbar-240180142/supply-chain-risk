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
        #map { height: 600px; width: 100%; border-radius: 8px; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; }
        .port-large { background-color: #dc3545; }
        .port-medium { background-color: #ffc107; }
        .port-small { background-color: #198754; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">🌍 Supply Chain Risk Intelligence</a>
        <span class="navbar-text text-white">🚢 Global Port Locations</span>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-9">
            <div class="card p-3">
                <h5 class="card-title mb-3">🗺️ World Port Map</h5>
                <div id="map"></div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card p-3">
                <h5 class="card-title mb-3">📋 Port List</h5>
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Port Name</th>
                                <th>Country</th>
                                <th>Size</th>
                            </tr>
                        </thead>
                        <tbody id="portTableBody">
                            <tr><td colspan="3" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <h6>Legend:</h6>
                    <span class="badge port-large">Large</span>
                    <span class="badge port-medium">Medium</span>
                    <span class="badge port-small">Small</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    const map = L.map('map').setView([20, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Fetch ports data
    fetch('/api/ports')
        .then(response => response.json())
        .then(ports => {
            displayPorts(ports);
            updateTable(ports);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('portTableBody').innerHTML = 
                '<tr><td colspan="3" class="text-center text-danger">Error loading data</td></tr>';
        });

    function displayPorts(ports) {
        ports.forEach(port => {
            let color = 'green';
            let sizeClass = 'port-small';
            
            if (port.harbor_size === 'Large') {
                color = 'red';
                sizeClass = 'port-large';
            } else if (port.harbor_size === 'Medium') {
                color = 'orange';
                sizeClass = 'port-medium';
            }

            L.circleMarker([port.latitude, port.longitude], {
                radius: port.harbor_size === 'Large' ? 12 : port.harbor_size === 'Medium' ? 8 : 5,
                fillColor: color,
                color: '#fff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(map).bindPopup(`
                <b>${port.port_name}</b><br>
                Country: ${port.country_name}<br>
                Size: ${port.harbor_size}<br>
                Status: ${port.is_active ? '✅ Active' : '❌ Inactive'}
            `);
        });
    }

    function updateTable(ports) {
        const tbody = document.getElementById('portTableBody');
        tbody.innerHTML = '';
        
        ports.forEach(port => {
            let badgeClass = 'port-small';
            if (port.harbor_size === 'Large') badgeClass = 'port-large';
            else if (port.harbor_size === 'Medium') badgeClass = 'port-medium';
            
            const row = `
                <tr>
                    <td><strong>${port.port_name}</strong></td>
                    <td>${port.country_name}</td>
                    <td><span class="badge ${badgeClass} text-white">${port.harbor_size}</span></td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    }
});
</script>
</body>
</html>