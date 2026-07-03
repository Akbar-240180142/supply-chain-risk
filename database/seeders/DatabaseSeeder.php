<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DictionarySeeder::class,
            CountrySeeder::class, // Tambahkan baris ini
        ]);
    }
}