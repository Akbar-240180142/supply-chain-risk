<?php

namespace Database\Seeders;

use App\Models\Port;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ports = Port::all();
        if ($ports->count() < 2) {
            return;
        }

        // Shipment 1: Delayed due to weather
        $shipment1 = Shipment::create([
            'tracking_number' => 'TA-2026-0001',
            'sender_name' => 'PT. Export Nusantara',
            'recipient_name' => 'Global Imports LLC',
            'origin' => 'Jakarta, Indonesia',
            'destination' => 'Los Angeles, USA',
            'status' => 'Delayed',
        ]);

        $port1 = $ports->first();
        
        TrackingEvent::create([
            'shipment_id' => $shipment1->id,
            'port_id' => $port1->id,
            'status' => 'Arrived',
            'description' => 'Package arrived at port facility.',
            'occurred_at' => Carbon::now()->subDays(2),
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment1->id,
            'port_id' => $port1->id,
            'status' => 'Delayed',
            'description' => 'Package delayed at port. Pending clearance.',
            'occurred_at' => Carbon::now()->subDay(),
        ]);

        // Shipment 2: In Transit
        $shipment2 = Shipment::create([
            'tracking_number' => 'TA-2026-0002',
            'sender_name' => 'Tech Supply Co.',
            'recipient_name' => 'Retail Electronics',
            'origin' => 'Shenzhen, China',
            'destination' => 'Rotterdam, Netherlands',
            'status' => 'In Transit',
        ]);

        $port2 = $ports->last();

        TrackingEvent::create([
            'shipment_id' => $shipment2->id,
            'port_id' => $port2->id,
            'status' => 'Departed',
            'description' => 'Package departed from port.',
            'occurred_at' => Carbon::now()->subHours(10),
        ]);
    }
}
