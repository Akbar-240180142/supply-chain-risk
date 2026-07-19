<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\CountryService;

class CountrySeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Fetching countries from REST Countries API...');
        $count = app(CountryService::class)->fetchAndSync();
        
        if ($count > 0) {
            $this->command->info("✅ {$count} Countries seeded successfully via API!");
        } else {
            $this->command->info("✅ Countries already exist or API returned 0 new records.");
        }
    }
}