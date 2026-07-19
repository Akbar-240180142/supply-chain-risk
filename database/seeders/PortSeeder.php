<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\PortService;

class PortSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Fetching ports from World Port Index...');
        
        $count = app(PortService::class)->fetchAndSync();
        
        if ($count > 0) {
            $this->command->info("✅ {$count} Ports seeded successfully via API!");
        } else {
            $this->command->info("✅ Ports already synced or API returned 0 new records.");
        }
    }
}