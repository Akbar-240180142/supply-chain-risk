<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit News - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('admin.index') }}">⚙️ Admin Dashboard</a>
        <a href="{{ route('admin.news') }}" class="btn btn-outline-light btn-sm">← Back</a>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">✏️ Edit News</h5>
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

                    <form action="{{ route('admin.news.update', $news->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Title *</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $news->title) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Content *</label>
                            <textarea name="content" class="form-control" rows="5" required>{{ old('content', $news->content) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Source *</label>
                            <input type="text" name="source" class="form-control" value="{{ old('source', $news->source) }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Published At *</label>
                                <input type="datetime-local" name="published_at" class="form-control" value="{{ old('published_at', \Carbon\Carbon::parse($news->published_at)->format('Y-m-d\TH:i')) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sentiment *</label>
                                <select name="sentiment" class="form-select" required>
                                    <option value="Positive" {{ old('sentiment', $news->sentiment) == 'Positive' ? 'selected' : '' }}>Positive</option>
                                    <option value="Negative" {{ old('sentiment', $news->sentiment) == 'Negative' ? 'selected' : '' }}>Negative</option>
                                    <option value="Neutral" {{ old('sentiment', $news->sentiment) == 'Neutral' ? 'selected' : '' }}>Neutral</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Country</label>
                            <select name="country_id" class="form-select">
                                <option value="">-- Select Country --</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id', $news->country_id) == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update News
                        </button>
                        <a href="{{ route('admin.news') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>