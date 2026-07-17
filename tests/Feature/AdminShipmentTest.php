<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Shipment;
use App\Models\Port;
use App\Models\Country;

class AdminShipmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed dictionary and necessary records
        Country::create([
            'name' => 'Indonesia',
            'cca2' => 'ID',
            'cca3' => 'IDN',
            'capital' => 'Jakarta',
            'region' => 'Asia',
        ]);
    }

    public function test_guest_cannot_access_admin_dashboard()
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_admin_dashboard()
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get('/admin');
        $response->assertRedirect('/');
    }

    public function test_admin_can_access_admin_dashboard()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
    }

    public function test_admin_can_crud_shipments()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // 1. Create
        $response = $this->actingAs($admin)->post('/admin/shipments/store', [
            'tracking_number' => 'TRK-TEST123',
            'sender_name' => 'Alice',
            'recipient_name' => 'Bob',
            'origin' => 'Jakarta',
            'destination' => 'Singapore',
            'status' => 'Pending',
        ]);

        $response->assertRedirect('/admin/shipments');
        $this->assertDatabaseHas('shipments', [
            'tracking_number' => 'TRK-TEST123',
            'sender_name' => 'Alice',
        ]);

        $shipment = Shipment::where('tracking_number', 'TRK-TEST123')->firstOrFail();

        // 2. Edit / Update
        $response = $this->actingAs($admin)->post("/admin/shipments/{$shipment->id}/update", [
            'tracking_number' => 'TRK-TEST123',
            'sender_name' => 'Alice Updated',
            'recipient_name' => 'Bob',
            'origin' => 'Jakarta',
            'destination' => 'Singapore',
            'status' => 'In Transit',
        ]);

        $response->assertRedirect('/admin/shipments');
        $this->assertDatabaseHas('shipments', [
            'id' => $shipment->id,
            'sender_name' => 'Alice Updated',
            'status' => 'In Transit',
        ]);

        // 3. Delete
        $response = $this->actingAs($admin)->get("/admin/shipments/{$shipment->id}/delete");
        $response->assertRedirect('/admin/shipments');
        $this->assertDatabaseMissing('shipments', [
            'id' => $shipment->id,
        ]);
    }

    public function test_admin_can_manage_tracking_events()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $shipment = Shipment::create([
            'tracking_number' => 'TRK-EVENT-TEST',
            'status' => 'Pending',
        ]);

        $country = Country::first();
        $port = Port::create([
            'port_name' => 'Port of Tanjung Priok',
            'country_id' => $country->id,
            'country_name' => $country->name,
            'latitude' => -6.1,
            'longitude' => 106.9,
            'is_active' => true,
        ]);

        // Add event
        $response = $this->actingAs($admin)->post("/admin/shipments/{$shipment->id}/events/store", [
            'status' => 'In Transit',
            'description' => 'Departed from origin facility',
            'port_id' => $port->id,
            'occurred_at' => now()->format('Y-m-d H:i:s'),
            'update_shipment_status' => '1',
        ]);

        $response->assertRedirect("/admin/shipments/{$shipment->id}/events");
        $this->assertDatabaseHas('tracking_events', [
            'shipment_id' => $shipment->id,
            'status' => 'In Transit',
            'port_id' => $port->id,
        ]);

        // Check if shipment status was updated
        $shipment->refresh();
        $this->assertEquals('In Transit', $shipment->status);
    }
}
