<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles & Permissions (Global)
        $this->call(PermissionsSeeder::class);

        // 1.1 Demo data (company, users, records)
        $this->call(DemoDataSeeder::class);

        // 2. Default opportunity stages for all companies
        $this->call(CompanyDefaultStagesSeeder::class);
    }

}
