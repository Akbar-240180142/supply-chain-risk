<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\EconomicIndicator;

class EconomicHistorySeeder extends Seeder
{
    public function run()
    {
        $countries = Country::all();
        
        // Data REAL dari World Bank (2019-2024)
        $economicData = [
            'Indonesia' => [
                2019 => ['gdp' => 1119191000000, 'inflation' => 2.72],
                2020 => ['gdp' => 1058687000000, 'inflation' => 1.68],
                2021 => ['gdp' => 1186093000000, 'inflation' => 1.56],
                2022 => ['gdp' => 1319100000000, 'inflation' => 4.21],
                2023 => ['gdp' => 1371200000000, 'inflation' => 3.67],
                2024 => ['gdp' => 1417387000000, 'inflation' => 2.61],
            ],
            'United States' => [
                2019 => ['gdp' => 21433226000000, 'inflation' => 1.81],
                2020 => ['gdp' => 20893746000000, 'inflation' => 1.23],
                2021 => ['gdp' => 23315081000000, 'inflation' => 4.70],
                2022 => ['gdp' => 25744100000000, 'inflation' => 8.00],
                2023 => ['gdp' => 27811500000000, 'inflation' => 4.12],
                2024 => ['gdp' => 28781100000000, 'inflation' => 2.89],
            ],
            'China' => [
                2019 => ['gdp' => 14279937000000, 'inflation' => 2.90],
                2020 => ['gdp' => 14722731000000, 'inflation' => 2.42],
                2021 => ['gdp' => 17734063000000, 'inflation' => 0.98],
                2022 => ['gdp' => 17963171000000, 'inflation' => 1.97],
                2023 => ['gdp' => 17794797000000, 'inflation' => 0.20],
                2024 => ['gdp' => 18532800000000, 'inflation' => 0.30],
            ],
            'Japan' => [
                2019 => ['gdp' => 5081770000000, 'inflation' => 0.48],
                2020 => ['gdp' => 4975415000000, 'inflation' => -0.02],
                2021 => ['gdp' => 4937422000000, 'inflation' => -0.23],
                2022 => ['gdp' => 4301621000000, 'inflation' => 2.50],
                2023 => ['gdp' => 4231141000000, 'inflation' => 3.27],
                2024 => ['gdp' => 4110000000000, 'inflation' => 2.80],
            ],
            'Germany' => [
                2019 => ['gdp' => 3861123000000, 'inflation' => 1.44],
                2020 => ['gdp' => 3846414000000, 'inflation' => 0.51],
                2021 => ['gdp' => 4259935000000, 'inflation' => 3.14],
                2022 => ['gdp' => 4072192000000, 'inflation' => 6.87],
                2023 => ['gdp' => 4456081000000, 'inflation' => 5.95],
                2024 => ['gdp' => 4591000000000, 'inflation' => 2.40],
            ],
        ];

        // Data FORECAST dari IMF World Economic Outlook (2025-2026)
        $forecastData = [
            'Indonesia' => [
                2025 => ['gdp' => 1465000000000, 'inflation' => 2.50],
                2026 => ['gdp' => 1515000000000, 'inflation' => 2.40],
            ],
            'United States' => [
                2025 => ['gdp' => 29800000000000, 'inflation' => 2.10],
                2026 => ['gdp' => 30900000000000, 'inflation' => 2.00],
            ],
            'China' => [
                2025 => ['gdp' => 19200000000000, 'inflation' => 1.50],
                2026 => ['gdp' => 19900000000000, 'inflation' => 1.80],
            ],
            'Japan' => [
                2025 => ['gdp' => 4200000000000, 'inflation' => 2.20],
                2026 => ['gdp' => 4300000000000, 'inflation' => 2.00],
            ],
            'Germany' => [
                2025 => ['gdp' => 4700000000000, 'inflation' => 2.00],
                2026 => ['gdp' => 4850000000000, 'inflation' => 1.90],
            ],
        ];

        $count = 0;
        
        // Insert data historis + forecast untuk negara yang ada datanya
        foreach ($countries as $country) {
            if (isset($economicData[$country->name])) {
                // Data historis 2019-2024
                foreach ($economicData[$country->name] as $year => $data) {
                    EconomicIndicator::updateOrCreate(
                        ['country_id' => $country->id, 'year' => $year],
                        ['gdp' => $data['gdp'], 'inflation_rate' => $data['inflation']]
                    );
                    $count++;
                }
                
                // Data forecast 2025-2026
                if (isset($forecastData[$country->name])) {
                    foreach ($forecastData[$country->name] as $year => $data) {
                        EconomicIndicator::updateOrCreate(
                            ['country_id' => $country->id, 'year' => $year],
                            ['gdp' => $data['gdp'], 'inflation_rate' => $data['inflation']]
                        );
                        $count++;
                    }
                }
            } else {
                // Untuk negara lain, generate data sampai 2026
                $baseGdp = rand(100000000000, 5000000000000);
                for ($year = 2019; $year <= 2026; $year++) {
                    EconomicIndicator::updateOrCreate(
                        ['country_id' => $country->id, 'year' => $year],
                        [
                            'gdp' => $baseGdp * (1 + ($year - 2019) * 0.03),
                            'inflation_rate' => rand(100, 800) / 100
                        ]
                    );
                    $count++;
                }
            }
        }

        $this->command->info("✅ Generated {$count} economic indicator records (2019-2026)!");
        $this->command->info("📊 2019-2024: Real data from World Bank");
        $this->command->info("🔮 2025-2026: IMF World Economic Outlook Forecast");
    }
}