<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles & Permissions (Global)
        $this->seedRolesAndPermissions();

        // 2. Default opportunity stages for all companies
        $this->call(CompanyDefaultStagesSeeder::class);
    }

    private function seedRolesAndPermissions(): void
    {
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $member = Role::firstOrCreate(['name' => 'member']);

        $permissions = [
            'manage-team',
            'view-billing',
            'export-data',
        ];

        foreach ($permissions as $p) {
            $perm = Permission::firstOrCreate(['name' => $p]);
            $admin->givePermissionTo($perm);
        }
    }
}
