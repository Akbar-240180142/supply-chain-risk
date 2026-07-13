<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index()
    {
        return view('tracking.index');
    }

    public function search(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string',
        ]);

        $trackingNumber = $request->input('tracking_number');
        $shipment = Shipment::with(['trackingEvents.port.country.weather', 'trackingEvents.port.country.news', 'trackingEvents.port.country.riskScores'])
            ->where('tracking_number', $trackingNumber)
            ->first();

        if (!$shipment) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Nomor resi tidak ditemukan.'], 404);
            }
            return back()->with('error', 'Nomor resi tidak ditemukan.');
        }

        if ($request->wantsJson()) {
            return response()->json(['shipment' => $shipment]);
        }

        return view('tracking.show', compact('shipment'));
    }

    public function apiSearch(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string',
        ]);

        $trackingNumber = $request->input('tracking_number');
        $shipment = Shipment::with(['trackingEvents.port.country.weather', 'trackingEvents.port.country.news', 'trackingEvents.port.country.riskScores'])
            ->where('tracking_number', $trackingNumber)
            ->first();

        if (!$shipment) {
            return response()->json(['error' => 'Nomor resi tidak ditemukan.'], 404);
        }

        return view('tracking.partial', compact('shipment'));
    }
}
