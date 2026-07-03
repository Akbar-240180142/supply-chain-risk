<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Watchlist;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WatchlistController extends Controller
{
    public function index()
    {
        // Pakai user ID 1 (admin default)
        $userId = 1;
        
        $watchlist = Watchlist::where('user_id', $userId)
            ->with('country')
            ->get();
        
        return response()->json($watchlist);
    }

    public function toggle(Request $request)
    {
        $userId = 1;
        $countryId = $request->input('country_id');
        
        $existing = Watchlist::where('user_id', $userId)
            ->where('country_id', $countryId)
            ->first();
        
        if ($existing) {
            $existing->delete();
            return response()->json(['status' => 'removed', 'country_id' => $countryId]);
        } else {
            Watchlist::create([
                'user_id' => $userId,
                'country_id' => $countryId
            ]);
            return response()->json(['status' => 'added', 'country_id' => $countryId]);
        }
    }
}