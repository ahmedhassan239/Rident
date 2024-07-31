<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use CountriesTableSeeder;
use Database\Seeders\CountriesTableSeeder as SeedersCountriesTableSeeder;
use Database\Seeders\GovernoratesTableSeeder as SeedersGovernoratesTableSeeder;
use Database\Seeders\HealthcareProvidersTableSeeder as SeedersHealthcareProvidersTableSeeder;
use Database\Seeders\SpecialtiesTableSeeder as SeedersSpecialtiesTableSeeder;
use Database\Seeders\SubSpecialtiesTableSeeder as SeedersSubSpecialtiesTableSeeder;
use GovernoratesTableSeeder;
use HealthcareProvidersTableSeeder;
use Illuminate\Database\Seeder;
use SpecialtiesTableSeeder;
use SubSpecialtiesTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SeedersCountriesTableSeeder::class);
        $this->call(SeedersGovernoratesTableSeeder::class);
        $this->call(SeedersHealthcareProvidersTableSeeder::class);
        $this->call(SeedersSpecialtiesTableSeeder::class);
        $this->call(SeedersSubSpecialtiesTableSeeder::class);

    }
}
