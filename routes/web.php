<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

// ============ DASHBOARD ============
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/api/dashboard-data', [DashboardController::class, 'getDashboardData'])->name('api.dashboard');

// ============ COUNTRY DETAIL ============
Route::get('/country/{id}', [DashboardController::class, 'showCountry'])->name('country.detail');

// ============ COMPARISON ============
Route::get('/comparison', function() {
    return view('comparison');
})->name('comparison');

// ============ PORTS ============
Route::get('/ports', function() {
    return view('ports');
})->name('ports');
Route::get('/api/ports', function() {
    return \App\Models\Port::with('country')->get();
})->name('api.ports');

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