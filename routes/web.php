<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\TrackingController;

// ============ DASHBOARD ============
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/api/dashboard-data', [DashboardController::class, 'getDashboardData'])->name('api.dashboard');

// ============ COUNTRY DETAIL ============
Route::get('/country/{id}', [DashboardController::class, 'showCountry'])->name('country.detail');

// ============ COMPARISON ============
Route::get('/comparison', [DashboardController::class, 'comparison'])->name('comparison');
Route::post('/api/compare', [DashboardController::class, 'compareCountries'])->name('api.compare');

// ============ PORTS ============
Route::get('/ports', [DashboardController::class, 'ports'])->name('ports');
Route::get('/api/ports', [DashboardController::class, 'getPortsData'])->name('api.ports');

// ============ NEWS ============
Route::get('/news', function() {
    $gnewsKey = env('GNEWS_API_KEY');
    $hasRealKey = $gnewsKey && $gnewsKey !== 'your_gnews_api_key_here';

    // Cek apakah sudah ada berita real di database (jangan fetch sinkron agar halaman cepat)
    $realCount = \App\Models\NewsCache::where('url', 'not like', '%example.com%')
        ->whereNotNull('url')
        ->where('url', '!=', '')
        ->count();

    // Ambil daftar semua negara untuk dropdown filter
    $countries = \App\Models\Country::orderBy('name')->get();

    return view('news', [
        'hasRealKey'  => $hasRealKey,
        'isRealData'  => $realCount > 0,
        'hasData'     => $realCount > 0,
        'countries'   => $countries,
    ]);
})->name('news');

Route::get('/api/news', function() {
    // Prioritaskan berita dengan URL real
    $news = \App\Models\NewsCache::with('country')
        ->where(function($q) {
            $q->where('url', 'not like', '%example.com%')
              ->whereNotNull('url')
              ->where('url', '!=', '');
        })
        ->latest('published_at')
        ->limit(100)
        ->get();

    // Fallback ke semua berita jika tidak ada yang real
    if ($news->count() === 0) {
        $news = \App\Models\NewsCache::with('country')
            ->latest('published_at')
            ->limit(100)
            ->get();
    }

    // Pastikan setiap item punya country (handle null)
    $result = $news->map(function($item) {
        return [
            'id'              => $item->id,
            'title'           => $item->title,
            'description'     => $item->description,
            'url'             => $item->url,
            'source'          => $item->source ?? 'Unknown',
            'published_at'    => $item->published_at,
            'sentiment'       => $item->sentiment ?? 'Neutral',
            'sentiment_score' => $item->sentiment_score ?? 0,
            'country'         => $item->country
                ? ['id' => $item->country->id, 'name' => $item->country->name]
                : ['id' => null, 'name' => 'Global'],
        ];
    });

    return response()->json($result);
})->name('api.news');

// Endpoint untuk trigger sync berita dari GNews (AJAX, tidak blocking page load)
Route::post('/api/news/sync', function() {
    $gnewsKey = env('GNEWS_API_KEY');
    if (!$gnewsKey || $gnewsKey === 'your_gnews_api_key_here') {
        return response()->json(['success' => false, 'message' => 'GNews API key tidak dikonfigurasi'], 400);
    }
    try {
        $count = app(\App\Services\NewsService::class)->fetchAndSync();
        return response()->json(['success' => true, 'synced' => $count]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
})->name('api.news.sync');

// ============ WATCHLIST ============
Route::get('/watchlist', function() {
    $userId = 1;
    $watchlist = \App\Models\Watchlist::where('user_id', $userId)
        ->with(['country' => function($q) {
            $q->with(['riskScores' => function($q2) {
                $q2->latest('record_date')->limit(1);
            }]);
        }])
        ->get();
    return view('watchlist', compact('watchlist'));
})->name('watchlist');

Route::delete('/watchlist/{countryId}', function($countryId) {
    $userId = 1;
    \App\Models\Watchlist::where('user_id', $userId)
        ->where('country_id', $countryId)
        ->delete();
    return redirect()->route('watchlist')->with('success', 'Country removed from watchlist');
})->name('watchlist.remove');

Route::get('/api/watchlist', function() {
    $userId = 1;
    return \App\Models\Watchlist::where('user_id', $userId)
        ->with(['country' => function($q) {
            $q->with(['riskScores' => function($q2) {
                $q2->latest('record_date')->limit(1);
            }]);
        }])
        ->get();
});

Route::post('/api/watchlist/toggle', function() {
    $userId = 1;
    $countryId = request()->input('country_id');
    $existing = \App\Models\Watchlist::where('user_id', $userId)
        ->where('country_id', $countryId)
        ->first();
    if ($existing) {
        $existing->delete();
        return response()->json(['status' => 'removed']);
    } else {
        \App\Models\Watchlist::create([
            'user_id' => $userId,
            'country_id' => $countryId
        ]);
        return response()->json(['status' => 'added']);
    }
});

// ============ TRACKING ============
Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
Route::post('/tracking', [TrackingController::class, 'search'])->name('tracking.search');

// ============ REST API ENDPOINTS (WAJIB PDF) ============
Route::prefix('api')->group(function () {
    Route::get('/countries', [DashboardController::class, 'apiCountries']);
    Route::get('/risk', [DashboardController::class, 'apiRisk']);
    Route::get('/currency', [DashboardController::class, 'apiCurrency']);
    Route::get('/economic-trends', [DashboardController::class, 'getEconomicTrends']);
    Route::post('/tracking', [TrackingController::class, 'apiSearch'])->name('api.tracking.search');
});

// ============ ADMIN DASHBOARD ROUTES ============
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    
    // News Management
    Route::get('/news', [AdminController::class, 'news'])->name('news');
    Route::get('/news/create', [AdminController::class, 'createNews'])->name('news.create');
    Route::post('/news/store', [AdminController::class, 'storeNews'])->name('news.store');
    Route::get('/news/{id}/edit', [AdminController::class, 'editNews'])->name('news.edit');
    Route::post('/news/{id}/update', [AdminController::class, 'updateNews'])->name('news.update');
    Route::get('/news/{id}/delete', [AdminController::class, 'deleteNews'])->name('news.delete');
    
    // Port Management
    Route::get('/ports', [AdminController::class, 'ports'])->name('ports');
    Route::get('/ports/create', [AdminController::class, 'createPort'])->name('ports.create');
    Route::post('/ports/store', [AdminController::class, 'storePort'])->name('ports.store');
    Route::get('/ports/{id}/edit', [AdminController::class, 'editPort'])->name('ports.edit');
    Route::post('/ports/{id}/update', [AdminController::class, 'updatePort'])->name('ports.update');
    Route::get('/ports/{id}/delete', [AdminController::class, 'deletePort'])->name('ports.delete');
    
    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/store', [AdminController::class, 'storeUser'])->name('users.store');
    Route::post('/users/{id}/update', [AdminController::class, 'updateUser'])->name('users.update');
    Route::get('/users/{id}/delete', [AdminController::class, 'deleteUser'])->name('users.delete');
});

// ============ MODERN DASHBOARD ============
Route::get('/modern', [DashboardController::class, 'index'])->name('dashboard.modern');