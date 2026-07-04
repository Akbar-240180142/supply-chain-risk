<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EconomicService;

class FetchEconomicData extends Command
{
    protected $signature = 'economic:fetch';
    protected $description = 'Fetch real economic data from World Bank API';

    public function handle()
    {
        $this->info('⏳ Mulai mengambil data ekonomi dari World Bank...');
        
        $service = app(EconomicService::class);
        $count = $service->fetchAndSync();
        
        $this->info("✅ Berhasil mengambil {$count} data ekonomi real!");
        
        return 0;
    }
}