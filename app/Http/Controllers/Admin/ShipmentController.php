<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\Port;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function index()
    {
        $shipments = Shipment::latest()->paginate(20);
        return view('admin.shipments.index', compact('shipments'));
    }

    public function create()
    {
        return view('admin.shipments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string|unique:shipments,tracking_number',
            'sender_name' => 'nullable|string|max:255',
            'recipient_name' => 'nullable|string|max:255',
            'origin' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'status' => 'required|in:Pending,In Transit,Delayed,Delivered',
        ]);

        Shipment::create($request->all());

        return redirect()->route('admin.shipments.index')->with('success', 'Shipment successfully created.');
    }

    public function edit($id)
    {
        $shipment = Shipment::findOrFail($id);
        return view('admin.shipments.edit', compact('shipment'));
    }

    public function update(Request $request, $id)
    {
        $shipment = Shipment::findOrFail($id);

        $request->validate([
            'tracking_number' => 'required|string|unique:shipments,tracking_number,' . $id,
            'sender_name' => 'nullable|string|max:255',
            'recipient_name' => 'nullable|string|max:255',
            'origin' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'status' => 'required|in:Pending,In Transit,Delayed,Delivered',
        ]);

        $shipment->update($request->all());

        return redirect()->route('admin.shipments.index')->with('success', 'Shipment successfully updated.');
    }

    public function delete($id)
    {
        $shipment = Shipment::findOrFail($id);
        $shipment->delete();

        return redirect()->route('admin.shipments.index')->with('success', 'Shipment successfully deleted.');
    }

    // Manage tracking events for a specific shipment
    public function events($id)
    {
        $shipment = Shipment::with('trackingEvents.port')->findOrFail($id);
        $ports = Port::orderBy('port_name')->get();
        return view('admin.shipments.events', compact('shipment', 'ports'));
    }

    public function storeEvent(Request $request, $id)
    {
        $shipment = Shipment::findOrFail($id);

        $request->validate([
            'status' => 'required|string|max:255',
            'description' => 'required|string',
            'port_id' => 'nullable|exists:ports,id',
            'occurred_at' => 'required|date',
        ]);

        $shipment->trackingEvents()->create([
            'status' => $request->status,
            'description' => $request->description,
            'port_id' => $request->port_id,
            'occurred_at' => $request->occurred_at,
        ]);

        // Automatically update the main shipment status if selected
        if ($request->has('update_shipment_status')) {
            $shipment->update(['status' => $request->status]);
        }

        return redirect()->route('admin.shipments.events', $id)->with('success', 'Tracking event successfully added.');
    }

    public function deleteEvent($shipmentId, $eventId)
    {
        $event = TrackingEvent::where('shipment_id', $shipmentId)->findOrFail($eventId);
        $event->delete();

        return redirect()->route('admin.shipments.events', $shipmentId)->with('success', 'Tracking event deleted.');
    }
}
