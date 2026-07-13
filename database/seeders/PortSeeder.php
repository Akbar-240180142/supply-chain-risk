<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\PortService;

class PortSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🚢 Seeding ports from tayljordan/ports dataset API...');
        
        $count = app(PortService::class)->fetchAndSync();
        
        if ($count > 0) {
            $this->command->info("✅ {$count} ports seeded successfully from API!");
        } else {
            $this->command->error("❌ Failed to seed ports from API. Database remains empty.");
        }
    }
}