<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage News - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* Fix: pagination SVG icons tidak raksasa */
        .pagination svg { width: 1rem; height: 1rem; vertical-align: middle; }
        .page-link svg { width: 0.875rem; height: 0.875rem; }
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
        <h2>📰 Manage News</h2>
        <a href="{{ route('admin.news.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add New News
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
                            <th>Title</th>
                            <th>Country</th>
                            <th>Sentiment</th>
                            <th>Published</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($news as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ Str::limit($item->title, 50) }}</td>
                                <td>{{ optional($item->country)->name ?? 'Global' }}</td>
                                <td>
                                    @php
                                        $badge = $item->sentiment === 'Positive' ? 'success' : ($item->sentiment === 'Negative' ? 'danger' : 'secondary');
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ $item->sentiment }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($item->published_at)->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.news.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="{{ route('admin.news.delete', $item->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Delete this news?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No news available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Custom pagination: ganti SVG raksasa dengan teks biasa --}}
            @if($news->hasPages())
            <nav aria-label="News pagination" class="mt-3">
                <ul class="pagination pagination-sm justify-content-center flex-wrap">
                    {{-- Tombol Previous --}}
                    @if($news->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">&laquo; Prev</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $news->previousPageUrl() }}">&laquo; Prev</a>
                        </li>
                    @endif

                    {{-- Nomor halaman --}}
                    @foreach($news->getUrlRange(max(1, $news->currentPage()-2), min($news->lastPage(), $news->currentPage()+2)) as $page => $url)
                        @if($page == $news->currentPage())
                            <li class="page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    {{-- Tombol Next --}}
                    @if($news->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $news->nextPageUrl() }}">Next &raquo;</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">Next &raquo;</span>
                        </li>
                    @endif
                </ul>
                <p class="text-center text-muted small">
                    Menampilkan {{ $news->firstItem() }}–{{ $news->lastItem() }} dari {{ $news->total() }} berita
                    (Halaman {{ $news->currentPage() }} dari {{ $news->lastPage() }})
                </p>
            </nav>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>