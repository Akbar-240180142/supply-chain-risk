<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Country;
use App\Models\RiskScore;
use App\Models\CurrencyRate;
use App\Models\Port;
use App\Models\EconomicIndicator;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic dictionary and countries
        $this->seed(\Database\Seeders\DictionarySeeder::class);
        $this->seed(\Database\Seeders\CountrySeeder::class);

        // Create a default user with ID = 1 since routes use hardcoded user_id = 1
        User::factory()->create(['id' => 1]);

        $countries = Country::take(2)->get();
        $c1 = $countries[0];
        $c2 = $countries[1];

        // Create dependent records for country 1
        RiskScore::create([
            'country_id' => $c1->id,
            'record_date' => now()->toDateString(),
            'weather_risk' => 1.5,
            'inflation_risk' => 2.0,
            'currency_risk' => 1.2,
            'news_risk' => 3.0,
            'total_risk_score' => 7.7,
            'risk_level' => 'Medium'
        ]);

        EconomicIndicator::create([
            'country_id' => $c1->id,
            'year' => date('Y'),
            'gdp' => 1000000000,
            'inflation_rate' => 3.5,
            'population' => 270000000,
            'exports' => 500000000,
            'imports' => 400000000,
        ]);

        CurrencyRate::create([
            'base_currency' => 'USD',
            'target_currency' => $c1->currency_code ?? 'IDR',
            'rate' => 15000.0,
            'record_date' => now()->toDateString()
        ]);

        Port::create([
            'port_name' => 'Tanjung Priok',
            'country_id' => $c1->id,
            'country_name' => $c1->name,
            'latitude' => -6.1033,
            'longitude' => 106.8792,
            'harbor_size' => 'Large',
            'is_active' => true
        ]);

        // Create dependent records for country 2
        RiskScore::create([
            'country_id' => $c2->id,
            'record_date' => now()->toDateString(),
            'weather_risk' => 2.5,
            'inflation_risk' => 1.0,
            'currency_risk' => 2.2,
            'news_risk' => 1.0,
            'total_risk_score' => 6.7,
            'risk_level' => 'Medium'
        ]);

        EconomicIndicator::create([
            'country_id' => $c2->id,
            'year' => date('Y'),
            'gdp' => 20000000000,
            'inflation_rate' => 1.5,
            'population' => 330000000,
            'exports' => 1500000000,
            'imports' => 1400000000,
        ]);
    }

    public function test_api_dashboard_data()
    {
        $response = $this->get('/api/dashboard-data');
        $response->assertStatus(200);

        $data = $response->json();
        $this->assertCount(20, $data['chart_risk']);
        $this->assertEquals(7.7, $data['chart_risk'][0]);
        $this->assertEquals(6.7, $data['chart_risk'][1]);
    }

    public function test_api_compare()
    {
        $c1 = Country::first();
        $c2 = Country::skip(1)->first();
        $response = $this->post('/api/compare', [
            'country1' => $c1->id,
            'country2' => $c2->id,
        ]);
        $response->assertStatus(200);
    }

    public function test_api_ports()
    {
        $response = $this->get('/api/ports');
        $response->assertStatus(200);
    }

    public function test_api_news()
    {
        $response = $this->get('/api/news');
        $response->assertStatus(200);
    }

    public function test_api_watchlist()
    {
        $response = $this->get('/api/watchlist');
        $response->assertStatus(200);
    }

    public function test_api_watchlist_toggle()
    {
        $country = Country::first();
        $response = $this->post('/api/watchlist/toggle', [
            'country_id' => $country->id
        ]);
        $response->assertStatus(200);
    }

    public function test_api_countries()
    {
        $response = $this->get('/api/countries');
        $response->assertStatus(200);
    }

    public function test_api_risk()
    {
        $response = $this->get('/api/risk');
        $response->assertStatus(200);
    }

    public function test_api_currency()
    {
        $response = $this->get('/api/currency');
        $response->assertStatus(200);
    }

    public function test_api_economic_trends()
    {
        $response = $this->get('/api/economic-trends');
        $response->assertStatus(200);
    }

    public function test_admin_store_news()
    {
        $response = $this->post('/admin/news/store', [
            'title' => 'Test News Title',
            'content' => 'Test News Content Description',
            'source' => 'Test Source',
            'published_at' => now()->toDateTimeString(),
            'sentiment' => 'Positive',
        ]);

        $response->assertRedirect('/admin/news');
        $this->assertDatabaseHas('news_cache', [
            'title' => 'Test News Title',
        ]);
    }

    public function test_admin_store_port()
    {
        $country = Country::first();
        $response = $this->post('/admin/ports/store', [
            'name' => 'Test Port',
            'code' => 'TST',
            'country_id' => $country->id,
            'latitude' => -5.0,
            'longitude' => 110.0,
            'status' => 'Active',
        ]);

        $response->assertRedirect('/admin/ports');
        $this->assertDatabaseHas('ports', [
            'port_name' => 'Test Port',
        ]);
    }

    public function test_admin_store_user()
    {
        $response = $this->post('/admin/users/store', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'role' => 'admin'
        ]);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
        ]);
    }
}
