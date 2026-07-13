<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Ports - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* Fix: pagination SVG icons tidak raksasa */
        .pagination svg { width: 1rem; height: 1rem; vertical-align: middle; }
        .page-link svg  { width: 0.875rem; height: 0.875rem; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('admin.index') }}">⚙️ Admin Dashboard</a>
        <a href="{{ route('admin.index') }}" class="btn btn-outline-light btn-sm">← Back</a>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>⚓ Manage Ports</h2>
        <a href="{{ route('admin.ports.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add New Port
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Country</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ports as $port)
                            <tr>
                                <td>{{ $port->id }}</td>
                                {{-- port_name adalah kolom yang digunakan, bukan name --}}
                                <td>{{ $port->port_name ?: ($port->name ?: 'N/A') }}</td>
                                <td>
                                    @if($port->code)
                                        <code>{{ $port->code }}</code>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                {{-- country_name di-store langsung di tabel ports --}}
                                <td>{{ $port->country_name ?: optional($port->country)->name ?: 'N/A' }}</td>
                                <td>
                                    @php
                                        $badge = $port->status === 'Active' ? 'success' : ($port->status === 'Inactive' ? 'danger' : 'warning');
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ $port->status ?? 'Unknown' }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.ports.edit', $port->id) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="{{ route('admin.ports.delete', $port->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Delete this port?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No ports available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Custom pagination: ganti SVG raksasa dengan teks biasa --}}
            @if($ports->hasPages())
            <nav aria-label="Ports pagination" class="mt-3">
                <ul class="pagination pagination-sm justify-content-center flex-wrap">
                    @if($ports->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">&laquo; Prev</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $ports->previousPageUrl() }}">&laquo; Prev</a>
                        </li>
                    @endif

                    @foreach($ports->getUrlRange(max(1, $ports->currentPage()-2), min($ports->lastPage(), $ports->currentPage()+2)) as $page => $url)
                        @if($page == $ports->currentPage())
                            <li class="page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    @if($ports->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $ports->nextPageUrl() }}">Next &raquo;</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">Next &raquo;</span>
                        </li>
                    @endif
                </ul>
                <p class="text-center text-muted small">
                    Menampilkan {{ $ports->firstItem() }}–{{ $ports->lastItem() }} dari {{ $ports->total() }} port
                    (Halaman {{ $ports->currentPage() }} dari {{ $ports->lastPage() }})
                </p>
            </nav>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>