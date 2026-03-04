<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\OpportunityStage;
use App\Models\User;
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
