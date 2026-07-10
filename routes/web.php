<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;

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
    $newsFromDb = \App\Models\NewsCache::with('country')
        ->latest('published_at')
        ->limit(50)
        ->get();
    
    if ($newsFromDb->count() === 0) {
        $newsService = app(\App\Services\NewsService::class);
        $newsService->fetchAndSync();
        $newsFromDb = \App\Models\NewsCache::with('country')
            ->latest('published_at')
            ->limit(50)
            ->get();
    }
    
    return view('news', ['news' => $newsFromDb]);
})->name('news');

Route::get('/api/news', function() {
    $newsFromDb = \App\Models\NewsCache::with('country')
        ->latest('published_at')
        ->limit(50)
        ->get();
    return response()->json($newsFromDb);
})->name('api.news');

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

// ============ REST API ENDPOINTS (WAJIB PDF) ============
Route::prefix('api')->group(function () {
    Route::get('/countries', [DashboardController::class, 'apiCountries']);
    Route::get('/risk', [DashboardController::class, 'apiRisk']);
    Route::get('/currency', [DashboardController::class, 'apiCurrency']);
    Route::get('/economic-trends', [DashboardController::class, 'getEconomicTrends']);
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