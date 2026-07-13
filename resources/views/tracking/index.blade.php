<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Paket - Supply Chain Risk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="bi bi-globe-americas me-2"></i>Supply Chain Risk
        </a>
    </div>
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg" style="border-radius: 1rem;">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-box-seam display-1 text-primary"></i>
                        <h2 class="fw-bold mt-3">Lacak Paket Anda</h2>
                        <p class="text-muted">Pantau status pengiriman paket dan analisis risiko di lokasinya.</p>
                    </div>

                    <form action="{{ route('tracking.search') }}" method="POST">
                        @csrf
                        <div class="input-group input-group-lg mb-3">
                            <input type="text" name="tracking_number" class="form-control" placeholder="Masukkan Nomor Resi (misal: TA-2026-0001)" required>
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i> Lacak
                            </button>
                        </div>
                    </form>

                    @if(session('error'))
                        <div class="alert alert-danger text-center">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
