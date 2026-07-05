
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Port - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('admin.index') }}">⚙️ Admin Dashboard</a>
        <a href="{{ route('admin.ports') }}" class="btn btn-outline-light btn-sm">← Back</a>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">✏️ Edit Port</h5>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.ports.update', $port->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Port Name *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $port->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Port Code *</label>
                            <input type="text" name="code" class="form-control" value="{{ old('code', $port->code) }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country *</label>
                                <select name="country_id" class="form-select" required>
                                    <option value="">-- Select Country --</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" {{ old('country_id', $port->country_id) == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status *</label>
                                <select name="status" class="form-select" required>
                                    <option value="Active" {{ old('status', $port->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Inactive" {{ old('status', $port->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="Maintenance" {{ old('status', $port->status) == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Latitude *</label>
                                <input type="number" step="any" name="latitude" class="form-control" value="{{ old('latitude', $port->latitude) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Longitude *</label>
                                <input type="number" step="any" name="longitude" class="form-control" value="{{ old('longitude', $port->longitude) }}" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Port
                        </button>
                        <a href="{{ route('admin.ports') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>